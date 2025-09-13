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
    public function index()
    {
        $userId  = Auth::id();

        // Ensure student profile exists
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

        // Only available laptops for selection
        $availableLaptops = Laptop::where('status', 'available')
            ->orderBy('device_name')
            ->get();

        // Student’s own requests
        $borrowings = Borrowing::with(['laptop'])
            ->where('student_id', $student->id)
            ->orderByDesc('requested_at')
            ->paginate(12);

        return view('student.borrow', compact('student', 'availableLaptops', 'borrowings'));
    }

    public function store(Request $request)
    {
        $student = Student::where('user_id', Auth::id())->firstOrFail();

        $validated = $request->validate([
            'laptop_id'      => ['required', 'exists:laptops,id'],
            'duration_hours' => ['required', 'integer', 'min:1', 'max:12'],
            'purpose'        => ['nullable', 'string', 'max:255'],
        ]);

        $hours  = (int) $request->input('duration_hours', 1);

        $laptop = Laptop::where('id', $validated['laptop_id'])
                        ->where('status', 'available')
                        ->firstOrFail();

        $now = Carbon::now();
        $due = (clone $now)->addHours($hours);

        Borrowing::create([
            'student_id'     => $student->id,
            'laptop_id'      => $laptop->id,
            'ip_asset_id'    => null, // student flow doesn't assign IP
            'status'         => 'pending',
            'purpose'        => $validated['purpose'] ?? null,
            'duration_hours' => $hours,          // ← SAVE IT
            'requested_at'   => $now,
            'due_at'         => $due,

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
