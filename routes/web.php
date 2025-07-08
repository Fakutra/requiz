<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LowonganController;
use App\Http\Controllers\AdminPanel\TestController;
use App\Http\Controllers\AdminPanel\AdminController;
use App\Http\Controllers\AdminPanel\BatchController;
use App\Http\Controllers\AdminPanel\PositionController;
use App\Http\Controllers\AdminPanel\ApplicantController;

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
    Route::get('{batchSlug}/{positionSlug}/apply', [LowonganController::class, 'create'])->name('apply.create');
    // Route::post('{batchSlug}/{positionSlug}/apply', [LowonganController::class, 'store'])->name('apply.store');
    // Route::get('{slug}/{slug}/apply', [LowonganController::class, 'create'])->name('apply.create');
    Route::post('/{positionSlug}/apply', [LowonganController::class, 'store'])->name('apply.store');
    // Route::get('/{batchSlug}/{positionSlug}/apply', [LowonganController::class, 'apply'])->name('apply.store');


    
    Route::get('history', [HistoryController::class, 'index'])->name('history.index');


});

require __DIR__.'/auth.php';

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', function () {
        return view('admin/dashboard');
    });
    Route::get('admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

    Route::get('admin/position', [PositionController::class, 'index'])->name('position.index');
    Route::get('admin/position/create', [PositionController::class, 'create'])->name('position.create');
    // Route::post('admin/batch/{batch}/position', [PositionController::class, 'store'])->name('position.store');
    Route::post('/admin/position', [PositionController::class, 'store'])->name('position.store');
    Route::get('admin/batch/position/{id}/edit', [PositionController::class, 'edit'])->name('position.edit');
    Route::put('admin/position/{id}', [PositionController::class, 'update'])->name('position.update');
    Route::delete('admin/position/{id}', [PositionController::class, 'destroy'])->name('position.destroy');
    Route::get('admin/position/checkSlug', [PositionController::class, 'checkSlug'])->name('position.checkSlug');
    
    // Route::get('admin/batch', [BatchController::class, 'index'])->name('batch.index');
    // Route::get('admin/batch/create', [BatchController::class, 'create'])->name('batch.create');
    // Route::post('admin/batch', [BatchController::class, 'store'])->name('batch.store');
    // Route::get('admin/batch/{id}/edit', [BatchController::class, 'edit'])->name('batch.edit');
    // Route::put('admin/batch/{id}', [BatchController::class, 'update'])->name('batch.update');
    // Route::delete('admin/batch/{id}', [BatchController::class, 'destroy'])->name('batch.destroy');
    // Route::get('admin/batch/checkSlug', [BatchController::class, 'checkSlug'])->name('batch.checkSlug');

    Route::get('admin/applicant', [ApplicantController::class, 'index'])->name('applicant.index');
    Route::get('admin/applicant/{id}/show', [ApplicantController::class, 'show'])->name('applicant.show');
    Route::put('/admin/applicant/{id}', [ApplicantController::class, 'update'])->name('applicant.update');
    // Route::get('admin/applicant/search-applicant', [ApplicantController::class, 'search'])->name('applicant.search'); // 👈 Tambahkan ini
    Route::post('/admin/applicant/update-status', [ApplicantController::class, 'updateStatus'])->name('applicant.update.status');
    
    // Menampilkan daftar test
    Route::get('admin/test', [TestController::class, 'index'])->name('test.index');
    Route::get('admin/test/create', [TestController::class, 'create'])->name('test.create');
    Route::post('admin/test', [TestController::class, 'store'])->name('test.store');
    Route::get('admin/test/{id}/edit', [TestController::class, 'edit'])->name('test.edit');
    Route::put('admin/test/{id}', [TestController::class, 'update'])->name('test.update');
    Route::delete('admin/test/{id}', [TestController::class, 'destroy'])->name('test.destroy');
    Route::get('admin/test/{id}', [TestController::class, 'show'])->name('test.show');
    Route::get('admin/test/checkSlug', [TestController::class, 'checkSlug'])->name('test.checkSlug');
});