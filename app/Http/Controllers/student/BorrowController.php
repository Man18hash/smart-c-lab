<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\Laptop;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BorrowController extends Controller
{
    /**
     * Show borrow page: available laptops + student's own requests.
     */
    public function index()
    {
        $userId = Auth::id();

        // Ensure student profile exists (linked by user_id)
        $student = Student::where('user_id', $userId)->first();
        if (!$student) {
            $student = Student::create([
                'user_id'      => $userId,
                'full_name'    => Auth::user()->name ?? 'Student',
                'grade'        => 'N/A',
                'section'      => 'N/A',
                'address'      => '',
                'adviser'      => null,
                'phone_number' => null,
                'email'        => Auth::user()->email,
                'status'       => 'active',
            ]);
        }

        // Only laptops that are available
        $availableLaptops = Laptop::where('status', 'available')
            ->orderBy('device_name')
            ->get();

        // Student’s own requests (most recent first)
        $borrowings = Borrowing::with(['laptop'])
            ->where('student_id', $student->id)
            ->orderByDesc('requested_at')
            ->paginate(12);

        return view('student.borrow', compact('student', 'availableLaptops', 'borrowings'));
    }

    /**
     * Store a borrow request (accepts any hours + minutes; min total 1 minute).
     */
    public function store(Request $request)
    {
        $student = Student::where('user_id', Auth::id())->firstOrFail();

        $validated = $request->validate([
            'laptop_id'  => ['required', 'exists:laptops,id'],
            'duration_h' => ['required', 'integer', 'min:0'],        // no upper limit
            'duration_m' => ['required', 'integer', 'min:0', 'max:59'], // minutes field
            'purpose'    => ['nullable', 'string', 'max:255'],
        ]);

        $hours        = (int) $validated['duration_h'];
        $minutes      = (int) $validated['duration_m'];
        $totalMinutes = ($hours * 60) + $minutes;

        // Minimum: 1 minute
        if ($totalMinutes < 1) {
            return back()
                ->withErrors(['duration_h' => 'Duration must be at least 1 minute.'])
                ->withInput();
        }

        // Ensure the selected laptop is still available
        $laptop = Laptop::where('id', $validated['laptop_id'])
            ->where('status', 'available')
            ->firstOrFail();

        $now = Carbon::now();
        $due = (clone $now)->addMinutes($totalMinutes);

        Borrowing::create([
            'student_id'     => $student->id,
            'laptop_id'      => $laptop->id,
            'ip_asset_id'    => null,             // Student flow doesn't assign IP
            'status'         => 'pending',        // Timer only starts when admin approves
            'purpose'        => $validated['purpose'] ?? null,
            'duration_hours' => $hours,           // keep hours part in existing column
            'requested_at'   => $now,
            'due_at'         => $due,

            // Snapshots
            'student_snapshot_json' => [
                'id'        => $student->id,
                'full_name' => $student->full_name,
                'grade'     => $student->grade,
                'section'   => $student->section,
                'email'     => $student->email,
            ],
            'device_snapshot_json' => [
                'id'          => $laptop->id,
                'device_name' => $laptop->device_name,
                'status'      => $laptop->status,
                'image'       => $laptop->image_path,
            ],
            'ip_snapshot_json' => null,
        ]);

        return redirect()
            ->route('student.borrow')
            ->with('success', 'Borrow request submitted. Status is now "Pending".');
    }
}
