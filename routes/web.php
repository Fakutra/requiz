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
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'role:user'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::get('lowongan', [LowonganController::class, 'index'])->name('lowongan.index');
    Route::get('apply/{slug}', [LowonganController::class, 'create'])->name('apply.create');
    Route::post('lowongan/{position}', [LowonganController::class, 'store'])->name('apply.store');
    
//     Route::get('lowongan/apply/{slug}', function () {
//     return route('lowongan.index');
// });

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
    Route::get('admin/position/checkSlug', [PositionController::class, 'checkSlug'])->name('position.checkSlug');
});