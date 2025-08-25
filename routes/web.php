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
use App\Http\Controllers\AdminPanel\QuestionBundleController;
use App\Http\Controllers\AdminPanel\QuestionController;
use Illuminate\Support\Facades\Auth;


Route::get('/', function () {
    return view('welcome');
})->name('welcome');


Route::get('/joblist', function () {
    return view('joblist');
})->name('joblist');

Route::get('/jobdetail', function() {
    return view('jobdetail');
})->name('jobdetail');

Route::get('lowongan', [LowonganController::class, 'index'])->name('lowongan.index');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'role:user'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::post('/{position:slug}/apply', [LowonganController::class, 'store'])->name('apply.store');
    Route::get('history', [HistoryController::class, 'index'])->name('history.index');
});

require __DIR__.'/auth.php';

Route::get('/adminn', [AdminController::class, 'indexx']);

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', function () {
        return view('admin/dashboard');
    });
    
    Route::get('admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

    Route::get('admin/batch', [BatchController::class, 'index'])->name('batch.index');
    Route::post('admin/batch', [BatchController::class, 'store'])->name('batch.store');
    Route::get('admin/batch/{batch}', [BatchController::class, 'show'])->name('batch.show');
    Route::put('admin/batch/{id}', [BatchController::class, 'update'])->name('batch.update');
    Route::delete('admin/batch/{id}', [BatchController::class, 'destroy'])->name('batch.destroy');
    Route::get('admin/batch/checkSlug', [BatchController::class, 'checkSlug'])->name('batch.checkSlug');

    Route::post('admin/batch/{batch}/position', [PositionController::class, 'store'])->name('position.store');
    Route::put('admin/position/{position}', [PositionController::class, 'update'])->name('position.update');
    Route::delete('admin/batch/position/{id}', [PositionController::class, 'destroy'])->name('position.destroy');
    Route::get('admin/batch/position/checkSlug', [PositionController::class, 'checkSlug'])->name('position.checkSlug');

    Route::get('admin/applicant', [ApplicantController::class, 'index'])->name('applicant.index');
    Route::get('/admin/applicant/export', [ApplicantController::class, 'export'])->name('admin.applicant.export');
    Route::put('admin/applicant/{applicant}', [ApplicantController::class, 'update'])->name('admin.applicant.update');
    Route::delete('admin/applicant/{applicant}', [ApplicantController::class, 'destroy'])->name('admin.applicant.destroy');
    Route::get('admin/applicant/seleksi', [ApplicantController::class, 'seleksiIndex'])->name('admin.applicant.seleksi.index');
    Route::get('admin/applicant/seleksi/{stage}', [ApplicantController::class, 'process'])->name('admin.applicant.seleksi.process');
    Route::post('admin/applicant/seleksi/update-status', [ApplicantController::class, 'updateStatus'])->name('admin.applicant.seleksi.update-status');// Route untuk edit applicant
    Route::get('/admin/applicant/{id}/edit', [ApplicantController::class, 'edit'])->name('admin.applicant.edit');
    Route::put('/admin/applicant/{id}', [ApplicantController::class, 'update'])->name('applicant.update');
    Route::delete('/admin/applicant/{id}', [ApplicantController::class, 'destroy'])->name('admin.applicant.destroySeleksi');
    Route::get('/admin/applicant/seleksi/{stage}', [ApplicantController::class, 'showStageApplicants'])->name('admin.applicant.seleksi.process');


    // Menampilkan daftar test
    Route::get('admin/test', [TestController::class, 'index'])->name('test.index');
    // Route::get('admin/test/create', [TestController::class, 'create'])->name('test.create');
    Route::post('admin/test', [TestController::class, 'store'])->name('test.store');
    // Route::get('admin/test/{id}/edit', [TestController::class, 'edit'])->name('test.edit');
    Route::put('admin/test/{id}', [TestController::class, 'update'])->name('test.update');
    Route::delete('admin/test/{id}', [TestController::class, 'destroy'])->name('test.destroy');
    // Route::get('admin/test/{id}', [TestController::class, 'show'])->name('test.show');
    Route::get('admin/test/checkSlug', [TestController::class, 'checkSlug'])->name('test.checkSlug');

    // Question
    Route::get('admin/question', [QuestionController::class, 'index'])->name('question.index');
    Route::post('admin/question', [QuestionController::class, 'store'])->name('question.store');
    Route::put('admin/question/{question}', [QuestionController::class, 'update'])->name('question.update'); 
    Route::delete('admin/question/{question}', [QuestionController::class, 'destroy'])->name('question.destroy');
    // Route untuk fitur import Excel
    Route::post('admin/question/import', [QuestionController::class, 'import'])->name('question.import');
    Route::get('admin/question/template', [QuestionController::class, 'downloadTemplate'])->name('question.template');

    Route::get('admin/bundle', [QuestionBundleController::class, 'index'])->name('bundle.index');
    Route::get('admin/bundle/{bundle}', [QuestionBundleController::class, 'show'])->name('bundle.show');
    Route::post('admin/bundle', [QuestionBundleController::class, 'store'])->name('bundle.store');
    Route::put('admin/bundle/{bundle}', [QuestionBundleController::class, 'update'])->name('bundle.update');
    Route::delete('admin/bundle/{bundle}', [QuestionBundleController::class, 'destroy'])->name('bundle.destroy');
    Route::get('admin/bundle/checkSlug', [QuestionBundleController::class, 'checkSlug'])->name('bundle.checkSlug');

    // Route untuk menambah soal ke bundle tertentu
    Route::post('admin/bundle/{bundle}/questions', [QuestionBundleController::class, 'addQuestion'])
        ->name('bundle.questions.add');

    // Route untuk menghapus soal dari bundle tertentu
    Route::delete('admin/bundle/{bundle}/questions/{question}', [QuestionBundleController::class, 'removeQuestion'])
        ->name('bundle.questions.remove');

});