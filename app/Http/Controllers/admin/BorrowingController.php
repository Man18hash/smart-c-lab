<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\IpAsset;
use App\Models\Laptop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BorrowingController extends Controller
{
    /**
     * List requests + cards for ongoing checkouts.
     * Tabs: pending | approved (approved + checked_out) | done (returned)
     */
    public function index(Request $request)
    {
        $status = (string) $request->input('status', 'pending');
        $q      = (string) $request->input('q', '');

        $base = Borrowing::query()
            ->with(['student', 'laptop', 'ipAsset'])
            ->search($q)
            ->orderByDesc('requested_at');

        if ($status === 'pending') {
            $base->where('status', 'pending');
        } elseif ($status === 'active') {
            $base->where('status', 'checked_out');
        } elseif ($status === 'done') {
            $base->where('status', 'returned');
        }

        $borrowings = $base->paginate(10)->withQueryString();

        // Cards: show currently checked out (approved requests now go directly to checked_out)
        $ongoingBorrowings = Borrowing::with(['student','laptop','ipAsset'])
            ->where('status', 'checked_out')
            ->orderBy('due_at')
            ->take(12)
            ->get();

        // Free IPs to optionally assign on approval
        $freeIps = IpAsset::query()
            ->where('status', 'free')
            ->orderBy('name')
            ->get();

        return view('admin.borrower', compact(
            'borrowings', 'status', 'q', 'ongoingBorrowings', 'freeIps'
        ));
    }

    /**
     * Approve request:
     * - Optionally assign an IP (marks it 'assigned').
     * - Optionally adjust due_at; if not provided, we shift the requested duration
     *   to START COUNTING FROM NOW (approval time) to avoid countdown while pending.
     * - Mark laptop as 'out' (directly checked out, no separate checkout step).
     */
    public function approve(Request $request, Borrowing $borrowing)
    {
        $data = $request->validate([
            'due_at'      => ['nullable','date'],
            'ip_asset_id' => ['nullable','exists:ip_assets,id'],
            'remarks'     => ['nullable','string','max:255'],
        ]);

        // If admin chose a specific IP, swap any prior one
        if (!empty($data['ip_asset_id'])) {
            // Free previously assigned IP (if any and different)
            if ($borrowing->ip_asset_id && (int)$borrowing->ip_asset_id !== (int)$data['ip_asset_id']) {
                IpAsset::whereKey($borrowing->ip_asset_id)->update(['status' => 'free']);
            }
            // Assign the selected IP
            $borrowing->ip_asset_id = (int) $data['ip_asset_id'];
            IpAsset::whereKey($borrowing->ip_asset_id)->update(['status' => 'assigned']);
        }

        // Set due_at:
        // - If admin typed a date/time, use it.
        // - Otherwise compute the originally requested duration (requested_at → old due_at),
        //   and apply that duration from *now* so countdown starts post-approval.
        if (!empty($data['due_at'])) {
            $borrowing->due_at = $data['due_at'];
        } else {
            $minutes = $this->computeRequestedDurationMinutes($borrowing);
            $borrowing->due_at = now()->clone()->addMinutes($minutes);
        }

        // Mark as checked out directly (no separate checkout step)
        $borrowing->status = 'checked_out';
        $borrowing->approved_at = now();
        $borrowing->approved_by_admin_id = Auth::id();
        $borrowing->checked_out_at = now();
        $borrowing->checked_out_by_admin_id = Auth::id();
        $borrowing->remarks = $data['remarks'] ?? null;
        $borrowing->save();

        // Mark laptop as out (directly checked out)
        if ($borrowing->laptop) {
            $borrowing->laptop->update(['status' => 'out']);
        }

        return back()->with('success', 'Request approved and laptop checked out.');
    }

    /**
     * Decline request.
     * (If it already had an IP reserved for any reason, free it.)
     */
    public function decline(Request $request, Borrowing $borrowing)
    {
        $data = $request->validate([
            'remarks' => ['nullable','string','max:255'],
        ]);

        // Free any reserved IP and laptop if present
        if ($borrowing->ipAsset) {
            $borrowing->ipAsset->update(['status' => 'free']);
            $borrowing->ip_asset_id = null;
        }
        if ($borrowing->laptop && $borrowing->laptop->status === 'reserved') {
            $borrowing->laptop->update(['status' => 'available']);
        }

        $borrowing->status  = 'declined';
        $borrowing->remarks = $data['remarks'] ?? null;
        $borrowing->save();

        return back()->with('success', 'Request declined.');
    }

    /**
     * Check-out (device leaves the lab).
     * - Only make sense after approval.
     * - Mark laptop 'out'.
     */
    public function checkOut(Request $request, Borrowing $borrowing)
    {
        // If due is still null for some reason, use the originally requested duration from now.
        if (is_null($borrowing->due_at)) {
            $minutes = $this->computeRequestedDurationMinutes($borrowing);
            $borrowing->due_at = now()->clone()->addMinutes($minutes);
        }

        $borrowing->status = 'checked_out';
        $borrowing->checked_out_at = now();
        $borrowing->checked_out_by_admin_id = Auth::id();
        $borrowing->remarks = $request->input('remarks');

        $borrowing->save();

        if ($borrowing->laptop) {
            $borrowing->laptop->update(['status' => 'out']);
        }

        return back()->with('success', 'Laptop checked out.');
    }

    /**
     * Check-in (also used by "Terminate Time" button).
     * - Mark laptop 'available'.
     * - Free IP (and detach).
     */
    public function checkIn(Request $request, Borrowing $borrowing)
    {
        // Release laptop
        if ($borrowing->laptop) {
            $borrowing->laptop->update(['status' => 'available']);
        }
        // Release IP
        if ($borrowing->ipAsset) {
            $borrowing->ipAsset->update(['status' => 'free']);
            $borrowing->ip_asset_id = null;
        }

        $borrowing->status = 'returned';
        $borrowing->returned_at = now();
        $borrowing->checked_in_by_admin_id = Auth::id();
        $borrowing->remarks = $request->input('remarks');
        $borrowing->save();

        return back()->with('success', 'Laptop checked in.');
    }

    /**
     * Terminate/Done: Manually return laptop before time expires
     */
    public function terminate(Request $request, Borrowing $borrowing)
    {
        $data = $request->validate([
            'remarks' => ['nullable','string','max:255'],
        ]);

        // Release laptop
        if ($borrowing->laptop) {
            $borrowing->laptop->update(['status' => 'available']);
        }
        // Release IP
        if ($borrowing->ipAsset) {
            $borrowing->ipAsset->update(['status' => 'free']);
            $borrowing->ip_asset_id = null;
        }

        $borrowing->status = 'returned';
        $borrowing->returned_at = now();
        $borrowing->checked_in_by_admin_id = Auth::id();
        $borrowing->remarks = $data['remarks'] ?? 'Manually terminated by admin';
        $borrowing->save();

        return back()->with('success', 'Laptop returned successfully.');
    }

    /**
     * Auto-return expired borrowings (can be called via cron job)
     */
    public function autoReturnExpired()
    {
        $expiredBorrowings = Borrowing::where('status', 'checked_out')
            ->where('due_at', '<', now())
            ->with(['laptop', 'ipAsset'])
            ->get();

        $count = 0;
        foreach ($expiredBorrowings as $borrowing) {
            // Release laptop
            if ($borrowing->laptop) {
                $borrowing->laptop->update(['status' => 'available']);
            }
            // Release IP
            if ($borrowing->ipAsset) {
                $borrowing->ipAsset->update(['status' => 'free']);
                $borrowing->ip_asset_id = null;
            }

            $borrowing->status = 'returned';
            $borrowing->returned_at = now();
            $borrowing->remarks = 'Auto-returned due to time expiry';
            $borrowing->save();
            
            $count++;
        }

        return response()->json(['message' => "Auto-returned {$count} expired borrowings"]);
    }

    /**
     * Derive the originally requested duration in minutes.
     * We infer it from (requested_at -> due_at) if present;
     * otherwise use a safe default (e.g., 120 minutes).
     */
    protected function computeRequestedDurationMinutes(Borrowing $b): int
    {
        try {
            if ($b->requested_at && $b->due_at) {
                $mins = $b->requested_at->diffInMinutes($b->due_at, false);
                if ($mins > 0) {
                    return $mins;
                }
            }
        } catch (\Throwable $e) {
            // fall through to default
        }
        return 120; // default 2 hours if not derivable
    }
}
