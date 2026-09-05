<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\InternshipController;
use App\Http\Controllers\MilestoneController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\DeliverableController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\GithubWebhookController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\InterviewController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StudentProjectController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/certificates/{hash}', [CertificateController::class, 'verify'])->name('certificates.verify');
Route::post('/webhooks/github', [GithubWebhookController::class, 'push'])->name('webhooks.github');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/avatar/{filename}', [ProfileController::class, 'avatar'])->where('filename', '[^/]+')->name('profile.avatar');
    Route::patch('/profile/student', [ProfileController::class, 'updateStudent'])
        ->middleware('role:student')->name('profile.student.update');
    Route::patch('/profile/supervisor', [ProfileController::class, 'updateSupervisor'])
        ->middleware('role:supervisor')->name('profile.supervisor.update');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [AnalyticsController::class, 'index'])
        ->middleware('role:student,supervisor,admin')->name('analytics.index');
    Route::get('/analytics/data', [AnalyticsController::class, 'data'])
        ->middleware('role:student,supervisor,admin')->name('analytics.data');
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('/applications/{application}/payment', [PaymentController::class, 'fund'])->middleware('role:supervisor')->name('payments.fund');
    Route::patch('/payments/{payment}/release', [PaymentController::class, 'release'])->middleware('role:supervisor')->name('payments.release');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{user}', [MessageController::class, 'conversation'])->whereNumber('user')->name('messages.conversation');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/certificates', [CertificateController::class, 'index'])->middleware('role:student')->name('certificates.index');
    Route::get('/certificates/{certificate}/download', [CertificateController::class, 'download'])->middleware('role:student')->name('certificates.download');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');

    // Grouped Interview Routes
    Route::name('interviews.')->prefix('interviews')->group(function () {
        Route::get('/', [InterviewController::class, 'index'])->name('index');
        Route::post('/applications/{application}', [InterviewController::class, 'store'])
            ->middleware('role:supervisor')->name('store');
        Route::patch('/{interview}/cancel', [InterviewController::class, 'cancel'])->name('cancel');
    });

    Route::get('/internships', [InternshipController::class, 'index'])->name('internships.index');
    Route::get('/student/projects', [StudentProjectController::class, 'index'])
        ->middleware('role:student')->name('student.projects');
    Route::post('/internships/{internship}/apply', [ApplicationController::class, 'store'])
        ->middleware('role:student')->name('applications.store');
    Route::get('/internships/{internship}/applications', [ApplicationController::class, 'index'])
        ->middleware('role:supervisor')->name('applications.index');
    Route::patch('/applications/{application}/status', [ApplicationController::class, 'updateStatus'])
        ->middleware('role:supervisor')->name('applications.status');
    Route::post('/milestones/{milestone}/deliverables', [DeliverableController::class, 'store'])
        ->middleware('role:student')->name('deliverables.store');
    Route::post('/deliverables/{deliverable}/verify', [DeliverableController::class, 'verify'])
        ->middleware('role:supervisor')->name('deliverables.verify');
    Route::get('/milestones/{milestone}/deliverables', [DeliverableController::class, 'review'])
        ->middleware('role:supervisor')->name('deliverables.review');
    Route::middleware('role:supervisor')->group(function () {
        Route::get('/internships/create', [InternshipController::class, 'create'])->name('internships.create');
        Route::post('/internships', [InternshipController::class, 'store'])->name('internships.store');
        Route::patch('/internships/{internship}/publish', [InternshipController::class, 'publish'])->name('internships.publish');
        Route::post('/internships/{internship}/certificates/issue', [CertificateController::class, 'issue'])->name('certificates.issue');
        Route::post('/internships/{internship}/milestones', [MilestoneController::class, 'store'])->name('milestones.store');
        Route::patch('/milestones/{milestone}', [MilestoneController::class, 'update'])->name('milestones.update');
        Route::delete('/milestones/{milestone}', [MilestoneController::class, 'destroy'])->name('milestones.destroy');
    });
});