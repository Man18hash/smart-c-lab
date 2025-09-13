<?php

use Illuminate\Support\Facades\Route;

// Admin auth/controllers
use App\Http\Controllers\Auth\LoginController as AdminLoginController;
use App\Http\Controllers\Admin\HomeController as AdminHomeController;
use App\Http\Controllers\Admin\LaptopController as AdminLaptopController;
use App\Http\Controllers\Admin\StudentController as AdminStudentController;
use App\Http\Controllers\Admin\IpAssetController as AdminIpAssetController;
use App\Http\Controllers\Admin\BorrowingController as AdminBorrowingController;
use App\Http\Controllers\Admin\HistoryController as AdminHistoryController; // ⬅️ add this line

// Student auth/controllers
use App\Http\Controllers\Student\HomeController as StudentHomeController;
use App\Http\Controllers\Student\Auth\RegisterController as StudentRegisterController;
use App\Http\Controllers\Student\Auth\LoginController as StudentLoginController;
use App\Http\Controllers\Student\BorrowController as StudentBorrowController;

/*
|--------------------------------------------------------------------------
| Root → redirect to admin login
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => redirect()->route('login'));

/*
|--------------------------------------------------------------------------
| Admin Authentication (guest only)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminLoginController::class, 'login'])->name('login.attempt');
});

/*
|--------------------------------------------------------------------------
| Student Authentication (guest only)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/student/register', [StudentRegisterController::class, 'showForm'])->name('student.register');
    Route::post('/student/register', [StudentRegisterController::class, 'register'])->name('student.register.store');

    Route::get('/student/login', [StudentLoginController::class, 'showLoginForm'])->name('student.login');
    Route::post('/student/login', [StudentLoginController::class, 'login'])->name('student.login.attempt');
});

/*
|--------------------------------------------------------------------------
| Logout (shared)
|--------------------------------------------------------------------------
*/
Route::post('/logout', [AdminLoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Admin Area (auth + role:admin)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        Route::get('/home', [AdminHomeController::class, 'index'])->name('home');

        // Borrower (requests) + actions
        Route::get('/borrower',                        [AdminBorrowingController::class, 'index'])->name('borrower');
        Route::post('/borrower/{borrowing}/approve',   [AdminBorrowingController::class, 'approve'])->name('borrowing.approve');
        Route::post('/borrower/{borrowing}/decline',   [AdminBorrowingController::class, 'decline'])->name('borrowing.decline');
        Route::post('/borrower/{borrowing}/check-out', [AdminBorrowingController::class, 'checkOut'])->name('borrowing.checkout');
        Route::post('/borrower/{borrowing}/check-in',  [AdminBorrowingController::class, 'checkIn'])->name('borrowing.checkin');
        Route::post('/borrower/{borrowing}/stop-timer',[AdminBorrowingController::class, 'checkIn'])->name('borrowing.stop-timer');

        // Student CRUD
        Route::get('/student',               [AdminStudentController::class, 'index'])->name('student');
        Route::post('/student',              [AdminStudentController::class, 'store'])->name('student.store');
        Route::put('/student/{student}',     [AdminStudentController::class, 'update'])->name('student.update');
        Route::delete('/student/{student}',  [AdminStudentController::class, 'destroy'])->name('student.destroy');

        // Laptop CRUD
        Route::get('/laptop',             [AdminLaptopController::class, 'index'])->name('laptop');
        Route::post('/laptop',            [AdminLaptopController::class, 'store'])->name('laptop.store');
        Route::put('/laptop/{laptop}',    [AdminLaptopController::class, 'update'])->name('laptop.update');
        Route::delete('/laptop/{laptop}', [AdminLaptopController::class, 'destroy'])->name('laptop.destroy');

        // IP Assets CRUD
        Route::get('/ip',               [AdminIpAssetController::class, 'index'])->name('ip');
        Route::post('/ip',              [AdminIpAssetController::class, 'store'])->name('ip.store');
        Route::put('/ip/{ip_asset}',    [AdminIpAssetController::class, 'update'])->name('ip.update');
        Route::delete('/ip/{ip_asset}', [AdminIpAssetController::class, 'destroy'])->name('ip.destroy');

        // History (controller; not a static view)
        Route::get('/history', [AdminHistoryController::class, 'index'])->name('history');
    });

/*
|--------------------------------------------------------------------------
| Student Area (auth + role:student)
|--------------------------------------------------------------------------
*/
Route::prefix('student')
    ->name('student.')
    ->middleware(['auth', 'role:student'])
    ->group(function () {
        Route::get('/home', [StudentHomeController::class, 'index'])->name('home');
        Route::get('/borrow',  [StudentBorrowController::class, 'index'])->name('borrow');
        Route::post('/borrow', [StudentBorrowController::class, 'store'])->name('borrow.store');
        Route::view('/history', 'student.history')->name('history');
    });

/*
|--------------------------------------------------------------------------
| Convenience redirects
|--------------------------------------------------------------------------
*/
Route::get('/admin', fn () => redirect()->route('admin.home'))->middleware(['auth','role:admin']);
Route::get('/student', fn () => redirect()->route('student.home'))->middleware(['auth','role:student']);
