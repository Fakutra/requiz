<?php

use App\Http\Controllers\AdminPanel\AdminController;
use App\Http\Controllers\AdminPanel\ApplicantController;
use App\Http\Controllers\AdminPanel\PositionController;
use App\Http\Controllers\LowonganController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/job/detail', function(){
    return view('jobdetail');
})->name('jobdetail');

Route::get('/job/applied', function(){
    return view('client/applied');
})->name('jobapplied');

Route::get('/userprofile', function(){
    return view('client/profile');
})->name('userprofile');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/lowongan', [LowonganController::class, 'index'])->name('lowongan.index');
    Route::get('apply/{id}', [LowonganController::class, 'create'])->name('apply.create');

});

require __DIR__.'/auth.php';

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', function () {
        return view('admin/dashboard');
    });
    Route::get('admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('admin/position', [PositionController::class, 'index'])->name('position.index');
    Route::get('admin/position/create', [PositionController::class, 'create'])->name('position.create');
    Route::post('admin/position', [PositionController::class, 'store'])->name('position.store');
    Route::get('admin/position/{id}/edit', [PositionController::class, 'edit'])->name('position.edit');
    Route::put('admin/position/{id}', [PositionController::class, 'update'])->name('position.update');
    Route::delete('admin/position/{id}', [PositionController::class, 'destroy'])->name('position.destroy');

    Route::get('admin/applicant', [ApplicantController::class, 'index'])->name('applicant.index');
});