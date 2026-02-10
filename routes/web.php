<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PatternController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    $user = auth()->user();

    if ($user->isSuperAdmin()) {
        return redirect()->route('schools.index');
    }

    if ($user->school_id) {
        return redirect()->route('students.index', $user->school_id);
    }

    return redirect()->route('login');
})->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/schools', [SchoolController::class, 'index'])->name('schools.index');
    Route::get('/schools/create', [SchoolController::class, 'create'])->name('schools.create');
    Route::post('/schools', [SchoolController::class, 'store'])->name('schools.store');
    Route::get('/schools/{school}/edit', [SchoolController::class, 'edit'])->name('schools.edit');
    Route::put('/schools/{school}', [SchoolController::class, 'update'])->name('schools.update');
    Route::delete('/schools/{school}', [SchoolController::class, 'destroy'])->name('schools.destroy');

    Route::get('/schools/{school}/pattern', [PatternController::class, 'edit'])->name('patterns.edit');
    Route::put('/schools/{school}/pattern', [PatternController::class, 'update'])->name('patterns.update');
    Route::post('/schools/{school}/pattern/preview', [PatternController::class, 'preview'])->name('patterns.preview');

    Route::get('/schools/{school}/students', [StudentController::class, 'index'])->name('students.index');
    Route::get('/schools/{school}/students/create', [StudentController::class, 'create'])->name('students.create');
    Route::post('/schools/{school}/students', [StudentController::class, 'store'])->name('students.store');
});
