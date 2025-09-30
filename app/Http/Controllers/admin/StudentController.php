<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::orderBy('full_name')->paginate(12);
        return view('admin.student', compact('students'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name'    => ['required', 'string', 'max:120'],
            'grade'        => ['required', 'string', 'max:20'],
            'section'      => ['required', 'string', 'max:50'],
            'address'      => ['required', 'string', 'max:255'],
            'adviser'      => ['nullable', 'string', 'max:120'],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'email'        => ['required', 'email', 'max:191', Rule::unique('students', 'email')],
            'status'       => ['required', Rule::in(['active', 'inactive'])],
        ]);

        Student::create($data);

        return redirect()->route('admin.student')->with('success', 'Student added successfully.');
    }

    public function update(Request $request, Student $student)
    {
        $data = $request->validate([
            'full_name'    => ['required', 'string', 'max:120'],
            'grade'        => ['required', 'string', 'max:20'],
            'section'      => ['required', 'string', 'max:50'],
            'address'      => ['required', 'string', 'max:255'],
            'adviser'      => ['nullable', 'string', 'max:120'],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'email'        => ['required', 'email', 'max:191', Rule::unique('students', 'email')->ignore($student->id)],
            'status'       => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $student->update($data);

        return redirect()->route('admin.student')->with('success', 'Student updated.');
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('admin.student')->with('success', 'Student deleted.');
    }

    public function changePassword(Request $request, Student $student)
    {
        $data = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Update the associated user's password
        if ($student->user) {
            $student->user->update([
                'password' => bcrypt($data['password'])
            ]);
        }

        return redirect()->route('admin.student')->with('success', 'Password changed successfully for ' . $student->full_name);
    }
}
