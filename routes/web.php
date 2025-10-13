<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

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
use App\Http\Controllers\AdminPanel\ReportController;

// ====== Seleksi (baru, dipisah per tahap) ======
use App\Http\Controllers\AdminPanel\Selection\RekapController;
use App\Http\Controllers\AdminPanel\Selection\ProcessController;
use App\Http\Controllers\AdminPanel\Selection\StageActionController;
use App\Http\Controllers\AdminPanel\Selection\AdministrasiController;
use App\Http\Controllers\AdminPanel\Selection\TesTulisController;
use App\Http\Controllers\AdminPanel\Selection\TechnicalTestController;
use App\Http\Controllers\AdminPanel\Selection\InterviewController;
use App\Http\Controllers\AdminPanel\Selection\OfferingController;
use App\Http\Controllers\AdminPanel\Selection\ActionsController;
use App\Http\Controllers\AdminPanel\Selection\AdministrasiEmailController;
use App\Http\Controllers\AdminPanel\Selection\TesTulisEmailController;
use App\Http\Controllers\AdminPanel\Selection\TechnicalTestEmailController;

// ================= PUBLIC =================
Route::get('/', fn () => view('welcome'))->name('welcome');
Route::get('/joblist', fn () => view('joblist'))->name('joblist');
Route::get('/jobdetail', fn () => view('jobdetail'))->name('jobdetail');

Route::get('/dashboard', fn () => view('dashboard'))
->middleware(['auth', 'verified', 'role:user'])
->name('dashboard');

// ---------- Auth required (user) ----------
Route::middleware('auth')->group(function () {
// Profile
Route::get   ('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::patch ('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

// Lowongan & History
Route::get ('/lowongan', [LowonganController::class, 'index'])->name('lowongan.index');
Route::post('/{position:slug}/apply', [LowonganController::class, 'store'])->name('apply.store');
Route::get ('/history',  [HistoryController::class, 'index'])->name('history.index');

// QUIZ
Route::get ('/quiz/{slug}', [QuizController::class, 'start'])
    ->middleware(['throttle:20,1', 'signed'])
    ->name('quiz.start');
Route::post('/quiz/{slug}', [QuizController::class, 'submitSection'])->name('quiz.submit');
Route::post('/quiz/{slug}/autosave', [QuizController::class, 'autosave'])->name('quiz.autosave');
Route::get ('/quiz/{slug}/finish',   [QuizController::class, 'finish'])->name('quiz.finish');

// (opsional) keep alive saat mengerjakan quiz
Route::get('/keepalive', fn () => response()->noContent())->name('keepalive');

// Peserta upload jawaban technical test untuk suatu schedule
Route::post('technical-test/schedule/{schedule}/answer', [TechnicalTestAnswerController::class, 'store'])
    ->name('technical.answers.store');
});

require __DIR__ . '/auth.php';

// ================= ADMIN =================
Route::middleware(['auth', 'role:admin'])->group(function () {
Route::get('/admin', fn () => view('admin/dashboard'));
Route::get('admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

// -------- Batch & Position ----------
Route::get   ('admin/batch',            [BatchController::class, 'index'])->name('batch.index');
Route::post  ('admin/batch',            [BatchController::class, 'store'])->name('batch.store');
Route::get   ('admin/batch/{batch}',    [BatchController::class, 'show'])->name('batch.show');
Route::put   ('admin/batch/{id}',       [BatchController::class, 'update'])->name('batch.update');
Route::delete('admin/batch/{id}',       [BatchController::class, 'destroy'])->name('batch.destroy');
Route::get   ('admin/batch/checkSlug',  [BatchController::class, 'checkSlug'])->name('batch.checkSlug');

Route::post  ('admin/batch/{batch}/position', [PositionController::class, 'store'])->name('position.store');
Route::put   ('admin/position/{position}',    [PositionController::class, 'update'])->name('position.update');
Route::delete('admin/batch/position/{id}',    [PositionController::class, 'destroy'])->name('position.destroy');
Route::get   ('admin/batch/position/checkSlug',[PositionController::class, 'checkSlug'])->name('position.checkSlug');

// -------- Applicant (index/export/update/destroy) ----------
Route::get   ('admin/applicant',                 [ApplicantController::class, 'index'])->name('admin.applicant.index');
Route::get   ('admin/applicant/export',          [ApplicantController::class, 'export'])->name('admin.applicant.export');
Route::put   ('admin/applicant/{applicant}',     [ApplicantController::class, 'update'])->name('admin.applicant.update');
Route::delete('admin/applicant/{applicant}',     [ApplicantController::class, 'destroy'])->name('admin.applicant.destroy');

// (opsional) jika kamu memang punya halaman edit terpisah:
// Route::get('admin/applicant/{id}/edit', [ApplicantController::class, 'edit'])->name('admin.applicant.edit');

// -------- SELEKSI (rekap + per tahap + aksi) ----------
Route::prefix('admin/applicant/seleksi')->name('admin.applicant.seleksi.')->group(function () {

    // REKAP
    Route::get('/', [RekapController::class, 'index'])->name('index');

        // ADMINISTRASI (pakai controller khusus)
        Route::prefix('administrasi')->name('administrasi.')->group(function () {
            Route::get('/', [AdministrasiController::class, 'index'])->name('index');
            Route::post('/bulk-mark', [AdministrasiController::class, 'bulkMark'])->name('bulkMark');
            Route::get('/export', [AdministrasiController::class, 'export'])->name('export');
            Route::post('/send-email', [AdministrasiEmailController::class, 'send'])->name('sendEmail');
            Route::post('/set-selected-ids', [AdministrasiController::class, 'setSelectedIds'])->name('setSelectedIds');
        });

        // TES TULIS (pakai controller khusus)
        Route::prefix('tes-tulis')->name('tes_tulis.')->group(function () {
            Route::get('/', [TesTulisController::class, 'index'])->name('index');
            Route::post('/bulk-mark', [TesTulisController::class, 'bulkMark'])->name('bulkMark');
            Route::get('/export', [TesTulisController::class, 'export'])->name('export');
            Route::post('/send-email', [TesTulisEmailController::class, 'send'])->name('sendEmail');
            Route::post('/set-selected-ids', [TesTulisController::class, 'setSelectedIds'])->name('setSelectedIds');
            Route::post('/score-essay', [TesTulisController::class, 'scoreEssay'])->name('scoreEssay');
        });

        // TECHNICAL TEST
        Route::prefix('technical-test')->name('technical_test.')->group(function () {
            Route::get('/', [TechnicalTestController::class, 'index'])->name('index');
            Route::patch('/{answer}/update-score', [TechnicalTestController::class, 'updateScore'])->name('updateScore');
            Route::post('/bulk-mark', [TechnicalTestController::class, 'bulkMark'])->name('bulkMark');
            Route::get('/export', [TechnicalTestController::class, 'export'])->name('export');
            Route::post('/send-email', [TechnicalTestEmailController::class, 'send'])->name('sendEmail');
        });

        // INTERVIEW
        Route::prefix('interview')->name('interview.')->group(function () {
            Route::get('/', [InterviewController::class, 'index'])->name('index');
            Route::post('/score', [InterviewController::class, 'storeScore'])->name('storeScore');
            Route::post('/bulk-mark', [InterviewController::class, 'bulkMark'])->name('bulkMark');
            Route::get('/export', [InterviewController::class, 'export'])->name('export');
        });

        Route::get('/offering', function (Request $r, ProcessController $c) {
            return $c->index($r, 'Offering', 'admin.applicant.seleksi.offering.index');
        })->name('offering');

        // AKSI: Lolos/Gagal (generik)
        Route::post('/mark', [StageActionController::class, 'mark'])->name('mark');
    });
});


Route::post('admin/applicant/seleksi/update-status', [ActionsController::class, 'updateStatus'])
->name('admin.applicant.seleksi.updateStatus');
Route::post('admin/applicant/seleksi/send-email', [ActionsController::class, 'sendEmail'])
->name('admin.applicant.seleksi.sendEmail');
// -------- Test & Section ----------
Route::get   ('admin/test',              [TestController::class, 'index'])->name('test.index');
Route::get   ('admin/test/{test}',       [TestController::class, 'show'])->name('test.show');
Route::post  ('admin/test',              [TestController::class, 'store'])->name('test.store');
Route::put   ('admin/test/{test}',       [TestController::class, 'update'])->name('test.update');
Route::delete('admin/test/{test}',       [TestController::class, 'destroy'])->name('test.destroy');
Route::get   ('admin/test/checkSlug',    [TestController::class, 'checkSlug'])->name('test.checkSlug');

Route::post  ('admin/test/{test}/section',   [TestSectionController::class, 'store'])->name('section.store');
Route::get   ('admin/section/checkSlug',     [TestSectionController::class, 'checkSlug'])->name('section.checkSlug');
Route::put   ('admin/section/{section}',     [TestSectionController::class, 'update'])->name('section.update');
Route::delete('admin/test/section/{section}',[TestSectionController::class, 'destroy'])->name('section.destroy');

// -------- Question ----------
Route::get   ('admin/question',               [QuestionController::class, 'index'])->name('question.index');
Route::post  ('admin/question',               [QuestionController::class, 'store'])->name('question.store');
Route::put   ('admin/question/{question}',    [QuestionController::class, 'update'])->name('question.update');
Route::delete('admin/question/{question}',    [QuestionController::class, 'destroy'])->name('question.destroy');
Route::post  ('admin/question/import',        [QuestionController::class, 'import'])->name('question.import');
Route::get   ('admin/question/template',      [QuestionController::class, 'downloadTemplate'])->name('question.template');

// -------- Bundle ----------
Route::get   ('admin/bundle',                       [QuestionBundleController::class, 'index'])->name('bundle.index');
Route::get   ('admin/bundle/{bundle}',              [QuestionBundleController::class, 'show'])->name('bundle.show');
Route::post  ('admin/bundle',                       [QuestionBundleController::class, 'store'])->name('bundle.store');
Route::put   ('admin/bundle/{bundle}',              [QuestionBundleController::class, 'update'])->name('bundle.update');
Route::delete('admin/bundle/{bundle}',              [QuestionBundleController::class, 'destroy'])->name('bundle.destroy');
Route::get   ('admin/bundle/checkSlug',             [QuestionBundleController::class, 'checkSlug'])->name('bundle.checkSlug');
Route::post  ('admin/bundle/{bundle}/questions',    [QuestionBundleController::class, 'addQuestion'])->name('bundle.questions.add');
Route::delete('admin/bundle/{bundle}/questions/{question}', [QuestionBundleController::class, 'removeQuestion'])->name('bundle.questions.remove');

// -------- Quiz Results ----------
Route::get('admin/quiz-results',                  [QuizResultController::class, 'index'])->name('quiz_results.index');
Route::get('admin/quiz-results/{testResult}',     [QuizResultController::class, 'show'])->name('quiz_results.show');

// -------- Essay Grading ----------
Route::get  ('essay-grading',                                 [EssayGradingController::class, 'index'])->name('essay_grading.index');
Route::patch('essay-grading/result/{testResult}',             [EssayGradingController::class, 'updateResult'])->name('essay_grading.update_result');

// -------- Technical Test Schedules ----------
Route::get   ('admin/tech-schedule',               [TechnicalTestScheduleController::class, 'index'])->name('tech-schedule.index');
Route::post  ('admin/tech-schedule',               [TechnicalTestScheduleController::class, 'store'])->name('tech-schedule.store');
Route::put   ('admin/tech-schedule/{schedule}',    [TechnicalTestScheduleController::class, 'update'])->name('tech-schedule.update');
Route::delete('admin/tech-schedule/{schedule}',    [TechnicalTestScheduleController::class, 'destroy'])->name('tech-schedule.destroy');

// -------- Interview Schedules ----------
Route::get   ('admin/interview-schedule',             [InterviewScheduleController::class, 'index'])->name('interview-schedule.index');
Route::post  ('admin/interview-schedule',             [InterviewScheduleController::class, 'store'])->name('interview-schedule.store');
Route::put   ('admin/interview-schedule/{schedule}',  [InterviewScheduleController::class, 'update'])->name('interview-schedule.update');
Route::delete('admin/interview-schedule/{schedule}',  [InterviewScheduleController::class, 'destroy'])->name('interview-schedule.destroy');

// Admin: Penilaian Technical Test
Route::get   ('admin/tech-answers',          [TechnicalTestAnswerController::class, 'index'])->name('tech-answers.index');
Route::patch ('admin/tech-answers/{answer}', [TechnicalTestAnswerController::class, 'update'])->name('tech-answers.update');


// Route::get('/admin/report', [ReportController::class, 'index'])->name('report.index');
Route::get('/admin/report', [ReportController::class, 'index'])
        ->name('admin.report.index');
Route::get('/admin/report/export', [ReportController::class, 'export'])
        ->name('admin.report.export');