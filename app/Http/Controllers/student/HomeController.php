<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Resolve the logged-in Student row based on the user's email.
     */
    protected function resolveStudent(): Student
    {
        $user = Auth::user();
        $student = Student::where('email', $user->email)->first();
        abort_unless($student, 403, 'Your account is not linked to a student profile. Please contact the admin.');
        return $student;
    }

    /**
     * Student dashboard (no admin features).
     */
    public function index(Request $request)
    {
        $student = $this->resolveStudent();

        // Personal stats (for this student only)
        $stats = [
            'pending'   => Borrowing::where('student_id', $student->id)->where('status', 'pending')->count(),
            'approved'  => Borrowing::where('student_id', $student->id)->where('status', 'approved')->count(),
            'out'       => Borrowing::where('student_id', $student->id)->where('status', 'checked_out')->count(),
            'returned'  => Borrowing::where('student_id', $student->id)->where('status', 'returned')->count(),
        ];

        // Ongoing = approved or checked_out (countdown visible if due_at exists)
        $ongoing = Borrowing::with(['laptop'])
            ->where('student_id', $student->id)
            ->whereIn('status', ['approved', 'checked_out'])
            ->orderByRaw('CASE WHEN status="checked_out" THEN 0 ELSE 1 END, due_at IS NULL, due_at ASC')
            ->take(6)
            ->get();

        // Recent requests list
        $recent = Borrowing::with(['laptop'])
            ->where('student_id', $student->id)
            ->orderByDesc('requested_at')
            ->take(8)
            ->get();

        return view('student.home', compact('student', 'stats', 'ongoing', 'recent'));
    }
}
