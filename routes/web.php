<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ManagemenUser\PermissionController;
use App\Http\Controllers\ManagemenUser\RoleController;
use App\Http\Controllers\ManagemenUser\UserController;
use App\Http\Controllers\RapotController;
use App\Http\Controllers\ManagemenChallenge\ChallengeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ManagemenChallenge\QuestionBankController;
use App\Http\Controllers\AttemptController;
use App\Http\Controllers\AttemptQuestionController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\SimulasiController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\ManagemenLearning\LearningMaterialController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\RecommendationController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');

    // Fitur hint attempt: buka hint satu soal (penalti 2% konstanta sistem).
    Route::post('attempt-questions/{attemptQuestion}/hint', [AttemptQuestionController::class, 'openHint'])
        ->name('attempt-questions.hint');

    // Pengerjaan challenge: set soal tetap, submit per-soal, retry uraian,
    // SELESAI manual atau otomatis saat waktu habis.
    Route::get('/challenge/{challenge}/attempt', [AttemptController::class, 'start'])->name('attempts.start');
    Route::get('/attempts/{attempt}', [AttemptController::class, 'show'])->name('attempts.work');
    Route::post('/attempts/{attempt}/submit-answer', [AttemptController::class, 'submitAnswer'])->name('attempts.submit-answer');
    Route::post('/attempts/{attempt}/finish', [AttemptController::class, 'finish'])->name('attempts.finish');
    Route::get('/attempts/{attempt}/result', [AttemptController::class, 'result'])->name('attempts.result');

    Route::get('/recommendations/{recommendation}/material', [RecommendationController::class, 'openMaterial'])->name('recommendations.material');
    Route::get('/recommendations/{recommendation}/question', [RecommendationController::class, 'selectQuestion'])->name('recommendations.question');

    Route::get('/learning', [MateriController::class, 'index'])->name('learning.index');
    Route::get('/learning/{learningMaterial}', [MateriController::class, 'show'])->name('learning.show');
    Route::get('/learning/{learningMaterial}/simulation', [SimulasiController::class, 'simulation'])->name('learning.simulation');
    Route::post('/learning/{learningMaterial}/simulation', [SimulasiController::class, 'submitSimulation'])->name('learning.simulation.submit');
    Route::post('/learning/{learningMaterial}/simulation/save-answer', [SimulasiController::class, 'saveAnswer'])->name('learning.simulation.save-answer');
    Route::get('/learning/{learningMaterial}/riwayat', [SimulasiController::class, 'riwayat'])->name('learning.riwayat');

    Route::resource('learning-materials', LearningMaterialController::class)->except(['show']);
    Route::delete('learning-materials/{learningMaterial}/attachments/{attachment}', [LearningMaterialController::class, 'destroyAttachment'])
        ->name('learning-materials.attachments.destroy');

    // Manajemen Permission: route CRUD eksplisit mengikuti alur Sequence Diagram.
    // Pencarian (?keyword=) dan pagination (?page=) digabung ke permissions.index.
    Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::post('permissions', [PermissionController::class, 'store'])->name('permissions.store');
    Route::get('permissions/{permission_id}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
    Route::put('permissions/{permission_id}', [PermissionController::class, 'update'])->name('permissions.update');
    Route::delete('permissions/{permission_id}', [PermissionController::class, 'destroy'])->name('permissions.destroy');
    Route::resource('roles', controller: RoleController::class)->except(['show']);
    // Manajemen User: route CRUD eksplisit mengikuti alur Sequence Diagram.
    // Tanpa create/store — user dibuat lewat registrasi.
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/{user_id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('users/{user_id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('users/{user_id}', [UserController::class, 'destroy'])->name('users.destroy');

     Route::prefix('rapot')->name('rapot.')->group(function () {
        Route::get('/', [RapotController::class, 'index'])->name('index');
        Route::get('/{user}', [RapotController::class, 'show'])->name('show');
        Route::get('/{user}/challenge', [RapotController::class, 'challenges'])->name('challenges');
    });

    // Manajemen Kategori: route CRUD eksplisit mengikuti alur Sequence Diagram.
    Route::get('kategori', [CategoryController::class, 'index'])->name('kategori.index');
    Route::get('kategori/create', [CategoryController::class, 'create'])->name('kategori.create');
    Route::post('kategori', [CategoryController::class, 'store'])->name('kategori.store');
    Route::get('kategori/{kategori_id}/edit', [CategoryController::class, 'edit'])->name('kategori.edit');
    Route::put('kategori/{kategori_id}', [CategoryController::class, 'update'])->name('kategori.update');
    Route::delete('kategori/{kategori_id}', [CategoryController::class, 'destroy'])->name('kategori.destroy');

    // Bank Soal: sumber soal terpusat yang dipakai berulang lintas challenge.
    // Menggantikan alur lama "tulis soal manual per challenge" -- soal sekarang
    // dikelola di sini lalu ditarik otomatis saat sebuah challenge dibuat.
    // Route CRUD eksplisit mengikuti alur Sequence_Kelola_Bank_Soal_Admin.
    Route::get('bank-soal', [QuestionBankController::class, 'index'])->name('bank-soal.index');
    Route::get('bank-soal/create', [QuestionBankController::class, 'create'])->name('bank-soal.create');
    Route::post('bank-soal', [QuestionBankController::class, 'store'])->name('bank-soal.store');
    Route::get('bank-soal/{soal_id}/edit', [QuestionBankController::class, 'edit'])->name('bank-soal.edit');
    Route::put('bank-soal/{soal_id}', [QuestionBankController::class, 'update'])->name('bank-soal.update');
    Route::delete('bank-soal/{soal_id}', [QuestionBankController::class, 'destroy'])->name('bank-soal.destroy');

    // Kelola challenge: jumlah soal per tingkat diatur di form challenge;
    // sistem menyusun set soal tetap otomatis saat save.
    Route::resource('challenges', ChallengeController::class)->except(['show']);

    Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard.index');
    Route::get('/leaderboard/{challenge}', [LeaderboardController::class, 'show'])->name('leaderboard.show');

    Route::get('/activity-log', [LogController::class, 'index'])->name('activity-log.index');
});

require __DIR__.'/auth.php';