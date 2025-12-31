<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

// ====== Front / User area ======
use App\Http\Controllers\QuizController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LowonganController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminPanel\TechnicalTestAnswerController;
use App\Http\Controllers\NotificationController;

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
use App\Http\Controllers\AdminPanel\ActivityLogController;
use App\Http\Controllers\AdminPanel\PersonalityRuleController;
use App\Http\Controllers\AdminPanel\UserController;
use App\Http\Controllers\AdminPanel\FaqController;
use App\Http\Controllers\AdminPanel\SkregisController;
use App\Http\Controllers\AdminPanel\ContactController;
use App\Http\Controllers\AdminPanel\AboutController;
use App\Http\Controllers\AdminPanel\VendorController;
use App\Http\Controllers\AdminPanel\ScheduleController;
use App\Http\Controllers\AdminPanel\VendorPasswordController;
use App\Http\Controllers\AdminPanel\FieldController;
use App\Http\Controllers\AdminPanel\SubFieldController;
use App\Http\Controllers\AdminPanel\JobController;
use App\Http\Controllers\AdminPanel\SeksiController;


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
use App\Http\Controllers\AdminPanel\Selection\InterviewEmailController;
use App\Http\Controllers\AdminPanel\Selection\OfferingEmailController;
use App\Http\Controllers\AdminPanel\SettingController;
use App\Http\Controllers\OfferingResponseController;
use Illuminate\Support\Facades\Storage;

// ================= PUBLIC =================
Route::get('/', [DashboardController::class, 'index'])->name('welcome');
Route::get('/joblist', [LowonganController::class, 'index'])->name('joblist');
Route::get('/jobdetail/{position:slug}', [LowonganController::class, 'show'])->name('jobdetail');

// Halaman publik Tentang Kami
Route::get('/tentang-kami', [AboutController::class, 'show'])->name('about.show');

Route::get('/dashboard', fn() => view('dashboard'))
    ->middleware(['auth', 'verified', 'role:user'])
    ->name('dashboard');

Route::get('/manual-book/download/{type}', [SettingController::class, 'download'])->name('manualbook.download');


// ---------- Auth required (user) ----------
Route::middleware('auth', 'verified', 'role:user')->group(function () {

    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Lowongan & History
    Route::get('/lowongan', [LowonganController::class, 'index'])->name('lowongan.index');
    Route::post('/{position:slug}/apply', [LowonganController::class, 'store'])->name('apply.store');
    Route::get('/history',  [HistoryController::class, 'index'])->name('history.index');

    // ================= QUIZ =================

    // 1) Intro: pakai signed URL
    Route::get('/quiz/{slug}/intro', [QuizController::class, 'intro'])
        ->name('quiz.intro')
        ->middleware('signed');

    // 2) Halaman kerjain quiz utama: TIDAK signed
    Route::get('/quiz/{slug}', [QuizController::class, 'start'])
        ->name('quiz.start')
        ->middleware('throttle:20,1');

    // 3) AJAX question
    Route::get('/quiz/{slug}/q', [QuizController::class, 'question'])
        ->name('quiz.q');

    // 4) Submit section (Selesai Tes)
    Route::post('/quiz/{slug}', [QuizController::class, 'submitSection'])
        ->name('quiz.submit');

    // 5) Autosave jawaban
    Route::post('/quiz/{slug}/autosave', [QuizController::class, 'autosave'])
        ->name('quiz.autosave');

    // 6) Halaman finish
    Route::get('/quiz/{slug}/finish', [QuizController::class, 'finish'])
        ->name('quiz.finish');

    // Keep alive saat mengerjakan quiz
    Route::get('/keepalive', fn() => response()->noContent())->name('keepalive');

    // Peserta upload jawaban technical test untuk suatu schedule
    Route::post('technical-test/schedule/{schedule}/answer', [TechnicalTestAnswerController::class, 'store'])
        ->name('technical.answers.store');

    Route::post('/offering/{offering}/response', [OfferingResponseController::class, 'response'])
        ->name('offering.response');
});

require __DIR__ . '/auth.php';


// ================= ADMIN & VENDOR PANEL =================
Route::middleware(['auth'])->group(function () {

    /**
     * ========== ADMIN + VENDOR (role: admin,vendor) ==========
     * Vendor: cuma Selection + Schedule (CRUD tech & interview schedule)
     */
    Route::middleware('role:admin,vendor')->group(function () {

        // Dashboard panel (khusus admin)
        Route::get('/admin', [AdminController::class, 'index'])
            ->name('admin.dashboard');

        // Vendor - Ubah Password
        Route::get('admin/password', [VendorPasswordController::class, 'edit'])
            ->name('admin.vendor.password.edit');

        Route::put('admin/password', [VendorPasswordController::class, 'update'])
            ->name('admin.vendor.password.update');

        // -------- SELEKSI (rekap + per tahap + aksi) ----------
        Route::prefix('admin/applicant/seleksi')->name('admin.applicant.seleksi.')->group(function () {

            // REKAP
            Route::get('/', [RekapController::class, 'index'])->name('index');

            // ADMINISTRASI
            Route::prefix('administrasi')->name('administrasi.')->group(function () {
                Route::get('/', [AdministrasiController::class, 'index'])->name('index');
                Route::post('/bulk-mark', [AdministrasiController::class, 'bulkMark'])->name('bulkMark');
                Route::get('/export', [AdministrasiController::class, 'export'])->name('export');
                Route::post('/send-email', [AdministrasiEmailController::class, 'send'])->name('sendEmail');
                Route::post('/set-selected-ids', [AdministrasiController::class, 'setSelectedIds'])->name('setSelectedIds');
            });

            // TES TULIS
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
                Route::post('/bulk-set-picked-by', [InterviewController::class, 'bulkSetPickedBy'])->name('bulkSetPickedBy');
                Route::get('/export', [InterviewController::class, 'export'])->name('export');
                Route::post('/send-email', [InterviewEmailController::class, 'send'])->name('sendEmail');
            });

            // OFFERING
            Route::prefix('offering')->name('offering.')->group(function () {
                Route::get('/', [OfferingController::class, 'index'])->name('index');
                Route::post('/', [OfferingController::class, 'store'])->name('store');
                Route::post('{id}/update', [OfferingController::class, 'update'])->name('update');
                Route::get('/export', [OfferingController::class, 'export'])->name('export');
                Route::post('/bulk-mark', [OfferingController::class, 'bulkMark'])->name('bulkMark');
                Route::post('/send-email', [OfferingEmailController::class, 'send'])->name('sendEmail');
                Route::post('/sync-expired', [OfferingController::class, 'syncExpired'])->name('sync-expired');
            });

            // Aksi generik: mark lolos/tidak
            Route::post('/mark', [StageActionController::class, 'mark'])->name('mark');
        });



        Route::post('admin/applicant/seleksi/update-status', [ActionsController::class, 'updateStatus'])
            ->name('admin.applicant.seleksi.updateStatus');
        Route::post('admin/applicant/seleksi/send-email', [ActionsController::class, 'sendEmail'])
            ->name('admin.applicant.seleksi.sendEmail');

        // -------- Schedule INDEX (admin & vendor) ----------
        Route::get('/admin/schedule', [ScheduleController::class, 'index'])->name('admin.schedule.index');

        // -------- Technical Test Schedules (CRUD admin + vendor) ----------
        Route::get('admin/tech-schedule',            [TechnicalTestScheduleController::class, 'index'])->name('tech-schedule.index');
        Route::post('admin/tech-schedule',            [TechnicalTestScheduleController::class, 'store'])->name('tech-schedule.store');
        Route::put('admin/tech-schedule/{schedule}', [TechnicalTestScheduleController::class, 'update'])->name('tech-schedule.update');
        Route::delete('admin/tech-schedule/{schedule}', [TechnicalTestScheduleController::class, 'destroy'])->name('tech-schedule.destroy');

        // -------- Interview Schedules (CRUD admin + vendor) ----------
        Route::get('admin/interview-schedule',            [InterviewScheduleController::class, 'index'])->name('interview-schedule.index');
        Route::post('admin/interview-schedule',            [InterviewScheduleController::class, 'store'])->name('interview-schedule.store');
        Route::put('admin/interview-schedule/{schedule}', [InterviewScheduleController::class, 'update'])->name('interview-schedule.update');
        Route::delete('admin/interview-schedule/{schedule}', [InterviewScheduleController::class, 'destroy'])->name('interview-schedule.destroy');
    });


    // Master
    Route::middleware(['auth', 'role:admin'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            // Bidang (CRUD)
            Route::resource('fields', FieldController::class);

            // Sub Bidang (CRUD)
            Route::resource('sub-fields', SubFieldController::class)
                ->names('subfields')
                ->parameters(['sub-fields' => 'subfield']);
            // supaya route name menjadi admin.subfields.* 

            // Jabatan (CRUD)
            Route::resource('jobs', JobController::class);

            // Penempatan (CRUD)
            Route::resource('seksi', SeksiController::class);
        });

    /**
     * ========== ADMIN ONLY (role: admin) ==========
     * Semua konfigurasi & master data khusus admin.
     */
    Route::middleware('role:admin')->group(function () {
        // -------- Applicant (read/export + write) ----------
        Route::get('admin/applicant',        [ApplicantController::class, 'index'])->name('admin.applicant.index');
        Route::get('admin/applicant/export', [ApplicantController::class, 'export'])->name('admin.applicant.export');
        Route::put('admin/applicant/{applicant}',   [ApplicantController::class, 'update'])->name('admin.applicant.update');
        Route::delete('admin/applicant/{applicant}',   [ApplicantController::class, 'destroy'])->name('admin.applicant.destroy');

        // -------- User Admin Management ----------
        Route::get('admin/admin',       [UserController::class, 'admin'])->name('admin.administrator.index');
        Route::put('admin/user/vendor/{user}', [UserController::class, 'updateVendor'])->name('admin.user.updateVendor');

        // -------- User Account Management ----------
        Route::get('admin/user',        [UserController::class, 'index'])->name('admin.user.index');
        Route::post('admin/user',        [UserController::class, 'store'])->name('admin.user.store');
        Route::post('admin/user/vendor', [UserController::class, 'storeVendor'])->name('admin.user.storeVendor');
        Route::put('admin/user/{user}', [UserController::class, 'update'])->name('admin.user.update');
        Route::delete('admin/user/{user}', [UserController::class, 'destroy'])->name('admin.user.destroy');

        // -------- Batch & Position (full CRUD) ----------
        Route::post('admin/batch',           [BatchController::class, 'store'])->name('batch.store');
        Route::get('admin/batch',           [BatchController::class, 'index'])->name('batch.index');
        Route::get('admin/batch/{batch}',   [BatchController::class, 'show'])->name('batch.show');
        Route::put('admin/batch/{id}',      [BatchController::class, 'update'])->name('batch.update');
        Route::delete('admin/batch/{id}',      [BatchController::class, 'destroy'])->name('batch.destroy');
        Route::get('admin/batch/checkSlug', [BatchController::class, 'checkSlug'])->name('batch.checkSlug');

        Route::post('admin/batch/{batch}/position', [PositionController::class, 'store'])->name('position.store');
        Route::put('admin/position/{position}',    [PositionController::class, 'update'])->name('position.update');
        Route::delete('admin/batch/position/{id}',    [PositionController::class, 'destroy'])->name('position.destroy');
        Route::get('admin/batch/position/checkSlug', [PositionController::class, 'checkSlug'])->name('position.checkSlug');

        // -------- Test & Section (config) ----------
        Route::post('admin/test',               [TestController::class, 'store'])->name('test.store');
        Route::get('admin/test',               [TestController::class, 'index'])->name('test.index');
        Route::get('admin/test/{test}',        [TestController::class, 'show'])->name('test.show');
        Route::put('admin/test/{test}',        [TestController::class, 'update'])->name('test.update');
        Route::delete('admin/test/{test}',        [TestController::class, 'destroy'])->name('test.destroy');
        Route::get('admin/test/checkSlug',     [TestController::class, 'checkSlug'])->name('test.checkSlug');
        Route::post('/admin/tests/{test}/intro', [TestController::class, 'introStore'])->name('test.intro.store');

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

        // -------- Bundle (config + index/show) ----------
        Route::post('admin/bundle',                       [QuestionBundleController::class, 'store'])->name('bundle.store');
        Route::get('admin/bundle',                       [QuestionBundleController::class, 'index'])->name('bundle.index');
        Route::get('admin/bundle/{bundle}',              [QuestionBundleController::class, 'show'])->name('bundle.show');
        Route::put('admin/bundle/{bundle}',              [QuestionBundleController::class, 'update'])->name('bundle.update');
        Route::delete('admin/bundle/{bundle}',              [QuestionBundleController::class, 'destroy'])->name('bundle.destroy');
        Route::get('admin/bundle/checkSlug',             [QuestionBundleController::class, 'checkSlug'])->name('bundle.checkSlug');
        Route::post('admin/bundle/{bundle}/questions',    [QuestionBundleController::class, 'addQuestion'])->name('bundle.questions.add');
        Route::delete('admin/bundle/{bundle}/questions/{question}', [QuestionBundleController::class, 'removeQuestion'])->name('bundle.questions.remove');

        // -------- Quiz Results ----------
        Route::get('admin/quiz-results',              [QuizResultController::class, 'index'])->name('quiz_results.index');
        Route::get('admin/quiz-results/{testResult}', [QuizResultController::class, 'show'])->name('quiz_results.show');

        // -------- Essay Grading ----------
        Route::get('essay-grading',                     [EssayGradingController::class, 'index'])->name('essay_grading.index');
        Route::patch('essay-grading/result/{testResult}', [EssayGradingController::class, 'updateResult'])->name('essay_grading.update_result');

        // Admin: Penilaian Technical Test (update saja + index)
        Route::get('admin/tech-answers', [TechnicalTestAnswerController::class, 'index'])->name('tech-answers.index');
        Route::patch('admin/tech-answers/{answer}', [TechnicalTestAnswerController::class, 'update'])->name('tech-answers.update');

        // -------- Activity Logs ----------
        Route::get('/admin/logs', [ActivityLogController::class, 'index'])
            ->name('admin.logs.index');

        // -------- Personality Rules ----------
        Route::prefix('admin/personality-rules')->name('admin.personality-rules.')->group(function () {
            Route::get('/', [PersonalityRuleController::class, 'index'])->name('index');
            Route::post('/', [PersonalityRuleController::class, 'store'])->name('store');
            Route::match(['put', 'patch'], '/{id}', [PersonalityRuleController::class, 'update'])->name('update');
            Route::delete('/{id}', [PersonalityRuleController::class, 'destroy'])->name('destroy');
        });

        // ===== FAQ 
        Route::get('admin/faq',        [FaqController::class, 'index'])->name('admin.faq.index');
        Route::post('admin/faq',        [FaqController::class, 'store'])->name('admin.faq.store');
        Route::put('admin/faq/{faq}',  [FaqController::class, 'update'])->name('admin.faq.update');
        Route::delete('admin/faq/{faq}',  [FaqController::class, 'destroy'])->name('admin.faq.destroy');

        // ======= About Us (admin area) =========
        Route::get('/admin/about',         [AboutController::class, 'index'])->name('admin.about.index');
        Route::post('/admin/about',         [AboutController::class, 'store'])->name('admin.about.store');
        Route::put('/admin/about/{about}', [AboutController::class, 'update'])->name('admin.about.update');
        Route::delete('/admin/about/{about}', [AboutController::class, 'destroy'])->name('admin.about.destroy');

        // ======= Contact (admin) =========
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::resource('contact', ContactController::class)->except(['show']);
            Route::post('contact/{contact}/set-active', [ContactController::class, 'setActive'])
                ->name('contact.set-active');
        });

        // Privacy (admin static page)
        Route::get('admin/privacy', fn() => view('admin.privacy.index'))->name('admin.privacy.index');

        // Manual Book
        Route::get('admin/manual-book/', [SettingController::class, 'index'])->name('admin.manualbook.index');
        Route::post('admin/manual-book/upload', [SettingController::class, 'upload'])->name('admin.manualbook.upload');
        Route::delete('/manual-book/delete/{type}', [SettingController::class, 'destroy'])->name('admin.manualbook.delete');

        // -------- SK Regis ----------
        Route::get('admin/skregis',           [SkregisController::class, 'index'])->name('admin.skregis.index');
        Route::post('admin/skregis',           [SkregisController::class, 'store'])->name('admin.skregis.store');
        Route::put('admin/skregis/{skregis}', [SkregisController::class, 'update'])->name('admin.skregis.update');
        Route::delete('admin/skregis/{skregis}', [SkregisController::class, 'destroy'])->name('admin.skregis.destroy');

        // -------- Report----------
        Route::get('/admin/report',        [ReportController::class, 'index'])->name('admin.report.index');
        Route::get('/admin/report/export', [ReportController::class, 'export'])->name('admin.report.export');

        // --------- Vendor Master (admin only) -----------
        Route::get('admin/vendor',          [VendorController::class, 'index'])->name('admin.vendor.index');
        Route::post('admin/vendor',          [VendorController::class, 'store'])->name('admin.vendor.store');
        Route::delete('admin/vendor/{vendor}', [VendorController::class, 'destroy'])->name('admin.vendor.destroy');
        Route::put('admin/vendor/{vendor}', [VendorController::class, 'update'])->name('admin.vendor.update');
    });
});
