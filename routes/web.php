<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ====== Front / User area ======
use App\Http\Controllers\QuizController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LowonganController;
use App\Http\Controllers\AdminPanel\TechnicalTestAnswerController;

// ====== Admin area (lama) ======
use App\Http\Controllers\AdminPanel\AdminController;
use App\Http\Controllers\AdminPanel\BatchController;
use App\Http\Controllers\AdminPanel\PositionController;
use App\Http\Controllers\AdminPanel\TestController;
use App\Http\Controllers\AdminPanel\TestSectionController;
use App\Http\Controllers\AdminPanel\QuestionController;
use App\Http\Controllers\AdminPanel\QuestionBundleController;
use App\Http\Controllers\AdminPanel\QuizResultController;
use App\Http\Controllers\AdminPanel\EssayGradingController;
use App\Http\Controllers\AdminPanel\ApplicantController;
use App\Http\Controllers\AdminPanel\TechnicalTestScheduleController;
use App\Http\Controllers\AdminPanel\InterviewScheduleController;
use App\Http\Controllers\DashboardController;

// ====== Seleksi (baru, dipisah per tahap) ======
use App\Http\Controllers\AdminPanel\Selection\RekapController;
use App\Http\Controllers\AdminPanel\Selection\AdministrasiController;
use App\Http\Controllers\AdminPanel\Selection\TesTulisController;
use App\Http\Controllers\AdminPanel\Selection\TechnicalTestController;
use App\Http\Controllers\AdminPanel\Selection\InterviewController;
use App\Http\Controllers\AdminPanel\Selection\OfferingController;
use App\Http\Controllers\AdminPanel\Selection\ActionsController;

// ================= PUBLIC =================
Route::get('/', [DashboardController::class, 'index'])->name('welcome');
Route::get('/joblist', [LowonganController::class, 'index'])->name('joblist');
Route::get('/jobdetail/{position:slug}', [LowonganController::class, 'show'])->name('jobdetail');

Route::get('/dashboard', fn() => view('dashboard'))
    ->middleware(['auth', 'verified', 'role:user'])
    ->name('dashboard');

// ---------- Auth required (user) ----------
Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Lowongan & History
    Route::get('/lowongan', [LowonganController::class, 'index'])->name('lowongan.index');
    Route::post('/{position:slug}/apply', [LowonganController::class, 'store'])->name('apply.store');
    Route::get('/history',  [HistoryController::class, 'index'])->name('history.index');

    // QUIZ
    Route::get('/quiz/{slug}', [QuizController::class, 'start'])
        ->name('quiz.start')
        ->middleware(['throttle:20,1', 'signed']);

    // routes/web.php
    Route::get('/quiz/{slug}/q', [QuizController::class, 'question'])
        ->name('quiz.q');       // plus guard lain kalau perlu

    Route::post('/quiz/{slug}', [QuizController::class, 'submitSection'])->name('quiz.submit');

    Route::post('/quiz/{slug}/autosave', [QuizController::class, 'autosave'])
        ->name('quiz.autosave');

    Route::get('/quiz/{slug}/intro', [QuizController::class, 'intro'])->name('quiz.intro');

    Route::get('/quiz/{slug}/finish',   [QuizController::class, 'finish'])->name('quiz.finish');

    // (opsional) keep alive saat mengerjakan quiz
    Route::get('/keepalive', fn() => response()->noContent())->name('keepalive');

    // Peserta upload jawaban technical test untuk suatu schedule
    Route::post('technical-test/schedule/{schedule}/answer', [TechnicalTestAnswerController::class, 'store'])
        ->name('technical.answers.store');
});

require __DIR__ . '/auth.php';

// ================= ADMIN =================
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', fn() => view('admin/dashboard'));
    Route::get('admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

    // -------- Batch & Position ----------
    Route::get('admin/batch',            [BatchController::class, 'index'])->name('batch.index');
    Route::post('admin/batch',            [BatchController::class, 'store'])->name('batch.store');
    Route::get('admin/batch/{batch}',    [BatchController::class, 'show'])->name('batch.show');
    Route::put('admin/batch/{id}',       [BatchController::class, 'update'])->name('batch.update');
    Route::delete('admin/batch/{id}',       [BatchController::class, 'destroy'])->name('batch.destroy');
    Route::get('admin/batch/checkSlug',  [BatchController::class, 'checkSlug'])->name('batch.checkSlug');

    Route::post('admin/batch/{batch}/position', [PositionController::class, 'store'])->name('position.store');
    Route::put('admin/position/{position}',    [PositionController::class, 'update'])->name('position.update');
    Route::delete('admin/batch/position/{id}',    [PositionController::class, 'destroy'])->name('position.destroy');
    Route::get('admin/batch/position/checkSlug', [PositionController::class, 'checkSlug'])->name('position.checkSlug');

    // -------- Applicant (index/export/update/destroy) ----------
    Route::get('admin/applicant',                 [ApplicantController::class, 'index'])->name('admin.applicant.index');
    Route::get('admin/applicant/export',          [ApplicantController::class, 'export'])->name('admin.applicant.export');
    Route::put('admin/applicant/{applicant}',     [ApplicantController::class, 'update'])->name('admin.applicant.update');
    Route::delete('admin/applicant/{applicant}',     [ApplicantController::class, 'destroy'])->name('admin.applicant.destroy');

    // (opsional) jika kamu memang punya halaman edit terpisah:
    // Route::get('admin/applicant/{id}/edit', [ApplicantController::class, 'edit'])->name('admin.applicant.edit');

    // -------- SELEKSI (rekap + per tahap + aksi) ----------
    Route::prefix('admin/applicant/seleksi')->name('admin.applicant.seleksi.')->group(function () {
        // Rekap per-batch (angka lolos/gagal per tahap)
        Route::get('/', [RekapController::class, 'index'])->name('index');

        // Halaman per tahap
        Route::get('/administrasi',   [AdministrasiController::class,  'index'])->name('administrasi');
        Route::get('/tes-tulis',      [TesTulisController::class,      'index'])->name('tes_tulis');
        Route::get('/technical-test', [TechnicalTestController::class, 'index'])->name('technical_test');
        Route::get('/interview',      [InterviewController::class,     'index'])->name('interview');
        Route::get('/offering',       [OfferingController::class,      'index'])->name('offering');

        // Aksi umum (dipakai semua tahap) — nama route dipertahankan
        // Route::post('/update-status', [ActionsController::class, 'updateStatus'])->name('update-status');
        // Route::post('/send-email',    [ActionsController::class, 'sendEmail'])->name('sendEmail');
        // ^^^ sengaja pakai name 'sendEmail' seperti lama supaya JS/Blade kamu tidak perlu diubah
    });
    Route::post('admin/applicant/seleksi/update-status', [ActionsController::class, 'updateStatus'])
        ->name('admin.applicant.seleksi.updateStatus');
    Route::post('admin/applicant/seleksi/send-email', [ActionsController::class, 'sendEmail'])
        ->name('admin.applicant.seleksi.sendEmail');
    // -------- Test & Section ----------
    Route::get('admin/test',              [TestController::class, 'index'])->name('test.index');
    Route::get('admin/test/{test}',       [TestController::class, 'show'])->name('test.show');
    Route::post('admin/test',              [TestController::class, 'store'])->name('test.store');
    Route::put('admin/test/{test}',       [TestController::class, 'update'])->name('test.update');
    Route::delete('admin/test/{test}',       [TestController::class, 'destroy'])->name('test.destroy');
    Route::get('admin/test/checkSlug',    [TestController::class, 'checkSlug'])->name('test.checkSlug');

    Route::post('admin/test/{test}/section',   [TestSectionController::class, 'store'])->name('section.store');
    Route::get('admin/section/checkSlug',     [TestSectionController::class, 'checkSlug'])->name('section.checkSlug');
    Route::put('admin/section/{section}',     [TestSectionController::class, 'update'])->name('section.update');
    Route::delete('admin/test/section/{section}', [TestSectionController::class, 'destroy'])->name('section.destroy');

    // -------- Question ----------
    Route::get('admin/question',               [QuestionController::class, 'index'])->name('question.index');
    Route::post('admin/question',               [QuestionController::class, 'store'])->name('question.store');
    Route::put('admin/question/{question}',    [QuestionController::class, 'update'])->name('question.update');
    Route::delete('admin/question/{question}',    [QuestionController::class, 'destroy'])->name('question.destroy');
    Route::post('admin/question/import',        [QuestionController::class, 'import'])->name('question.import');
    Route::get('admin/question/template',      [QuestionController::class, 'downloadTemplate'])->name('question.template');

    // -------- Bundle ----------
    Route::get('admin/bundle',                       [QuestionBundleController::class, 'index'])->name('bundle.index');
    Route::get('admin/bundle/{bundle}',              [QuestionBundleController::class, 'show'])->name('bundle.show');
    Route::post('admin/bundle',                       [QuestionBundleController::class, 'store'])->name('bundle.store');
    Route::put('admin/bundle/{bundle}',              [QuestionBundleController::class, 'update'])->name('bundle.update');
    Route::delete('admin/bundle/{bundle}',              [QuestionBundleController::class, 'destroy'])->name('bundle.destroy');
    Route::get('admin/bundle/checkSlug',             [QuestionBundleController::class, 'checkSlug'])->name('bundle.checkSlug');
    Route::post('admin/bundle/{bundle}/questions',    [QuestionBundleController::class, 'addQuestion'])->name('bundle.questions.add');
    Route::delete('admin/bundle/{bundle}/questions/{question}', [QuestionBundleController::class, 'removeQuestion'])->name('bundle.questions.remove');

    // -------- Quiz Results ----------
    Route::get('admin/quiz-results',                  [QuizResultController::class, 'index'])->name('quiz_results.index');
    Route::get('admin/quiz-results/{testResult}',     [QuizResultController::class, 'show'])->name('quiz_results.show');

    // -------- Essay Grading ----------
    Route::get('essay-grading',                                 [EssayGradingController::class, 'index'])->name('essay_grading.index');
    Route::patch('essay-grading/result/{testResult}',             [EssayGradingController::class, 'updateResult'])->name('essay_grading.update_result');

    // -------- Technical Test Schedules ----------
    Route::get('admin/tech-schedule',               [TechnicalTestScheduleController::class, 'index'])->name('tech-schedule.index');
    Route::post('admin/tech-schedule',               [TechnicalTestScheduleController::class, 'store'])->name('tech-schedule.store');
    Route::put('admin/tech-schedule/{schedule}',    [TechnicalTestScheduleController::class, 'update'])->name('tech-schedule.update');
    Route::delete('admin/tech-schedule/{schedule}',    [TechnicalTestScheduleController::class, 'destroy'])->name('tech-schedule.destroy');

    // -------- Interview Schedules ----------
    Route::get('admin/interview-schedule',             [InterviewScheduleController::class, 'index'])->name('interview-schedule.index');
    Route::post('admin/interview-schedule',             [InterviewScheduleController::class, 'store'])->name('interview-schedule.store');
    Route::put('admin/interview-schedule/{schedule}',  [InterviewScheduleController::class, 'update'])->name('interview-schedule.update');
    Route::delete('admin/interview-schedule/{schedule}',  [InterviewScheduleController::class, 'destroy'])->name('interview-schedule.destroy');

    // Admin: Penilaian Technical Test
    Route::get('admin/tech-answers',          [TechnicalTestAnswerController::class, 'index'])->name('tech-answers.index');
    Route::patch('admin/tech-answers/{answer}', [TechnicalTestAnswerController::class, 'update'])->name('tech-answers.update');
});
