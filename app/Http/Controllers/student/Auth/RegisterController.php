<?php

namespace App\Http\Controllers\Student\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str; // 👈 add this

class RegisterController extends Controller
{
    public function showForm()
    {
        return view('student.auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'full_name'    => ['required','string','max:120'],
            'grade'        => ['required','string','max:20'],
            'section'      => ['required','string','max:50'],
            'address'      => ['required','string','max:255'],
            'adviser'      => ['nullable','string','max:120'],
            'phone_number' => ['nullable','string','max:30'],
            'email'        => ['required','email','max:191', Rule::unique('users','email')],
            'password'     => ['required','string','min:6','confirmed'],
        ]);

        // 🔹 Build a unique username (students won’t use it to log in,
        // but the DB requires a non-null unique value).
        $base = strtolower(Str::before($data['email'], '@')) ?: Str::slug($data['full_name'], '_');
        $base = $base ?: 'student';
        $username = $base;
        $i = 1;
        while (User::where('username', $username)->exists()) {
            $username = $base.'_'.$i++;
        }

        // Create user (role = student). NOTE: username is now non-null.
        $user = User::create([
            'name'     => $data['full_name'],
            'username' => $username,
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => 'student',
        ]);

        // Create student profile and link to user
        Student::create([
            'user_id'      => $user->id,
            'full_name'    => $data['full_name'],
            'grade'        => $data['grade'],
            'section'      => $data['section'],
            'address'      => $data['address'],
            'adviser'      => $data['adviser'] ?? null,
            'phone_number' => $data['phone_number'] ?? null,
            'email'        => $data['email'],
            'status'       => 'active',
        ]);

        // Auto-login the student, then go to student home
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('student.home');
    }
}
