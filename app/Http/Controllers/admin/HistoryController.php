<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $q     = (string) $request->input('q', '');
        $from  = $request->input('from') ?: null;
        $to    = $request->input('to')   ?: null;

        $query = Borrowing::query()
            ->with(['student','laptop'])
            ->where('status', 'returned')
            ->orderByDesc('returned_at');

        if ($q !== '') {
            $query->where(function ($qq) use ($q) {
                $qq->whereHas('student', fn ($s) => $s->where('first_name','like',"%{$q}%")
                                                     ->orWhere('last_name','like',"%{$q}%")
                                                     ->orWhere('email','like',"%{$q}%"))
                   ->orWhereHas('laptop', fn ($l) => $l->where('device_name','like',"%{$q}%"))
                   ->orWhere('purpose', 'like', "%{$q}%");
            });
        }

        if ($from) {
            $query->whereDate('returned_at', '>=', Carbon::parse($from)->toDateString());
        }
        if ($to) {
            $query->whereDate('returned_at', '<=', Carbon::parse($to)->toDateString());
        }

        $history = $query->paginate(12)->withQueryString();

        return view('admin.history', [
            'history' => $history,
            'q'       => $q,
            'from'    => $from,
            'to'      => $to,
        ]);
    }
}
