<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StartupController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\FavoriteController;

use App\Http\Controllers\ReviewController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InvestmentController;

// Public Routes
Route::get('/', [StartupController::class, 'index'])->name('home');
Route::resource('startups', StartupController::class)->except(['destroy']);
Route::get('/search', [StartupController::class, 'search'])->name('startups.search');

// Auth Routes
Route::prefix('auth')->name('auth.')->group(function () {
    Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
    Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',    [AuthController::class, 'login'])->name('login.store');
    Route::match(['get', 'post'], '/logout',   [AuthController::class, 'logout'])->name('logout');
});

Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout']);

// Forgot / Reset Password Routes
Route::get('/auth/forgot-password',  [PasswordResetController::class, 'showForgotForm'])->name('password.forgot');
Route::post('/auth/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.send');
Route::get('/auth/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset.form');
Route::post('/auth/reset-password',  [PasswordResetController::class, 'resetPassword'])->name('password.reset');

// Protected Routes
Route::middleware('auth.session')->group(function () {
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/',          [DashboardController::class, 'index'])->name('index');
        Route::get('/inquiries', [DashboardController::class, 'inquiries'])->name('inquiries');
        Route::post('/inquiries/{inquiry}/reply', [DashboardController::class, 'replyInquiry'])->name('inquiries.reply');
        Route::get('/analytics', [DashboardController::class, 'analytics'])->name('analytics');
    });
    
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/kyc', [ProfileController::class, 'storeKyc'])->name('profile.kyc');
    Route::post('/profile/notifications/{notification}/read', [ProfileController::class, 'readNotification'])->name('profile.notifications.read');
    Route::post('/profile/notifications/read-all', [ProfileController::class, 'readAllNotifications'])->name('profile.notifications.read_all');

    // Investment Process & Checkout Routes
    Route::get('/investment-process', [InvestmentController::class, 'process'])->name('investments.process');
    Route::get('/invest/{startup}', [InvestmentController::class, 'checkout'])->name('investments.checkout');
    Route::post('/invest/{startup}/pay', [InvestmentController::class, 'pay'])->name('investments.pay');
    Route::get('/investments/{investment}/success', [InvestmentController::class, 'success'])->name('investments.success');
    Route::get('/investments/{investment}/receipt', [InvestmentController::class, 'receipt'])->name('investments.receipt');

    Route::post('/inquiry/{startup}',          [InquiryController::class, 'store'])->name('inquiry.store');
    Route::get('/favorites',                   [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/{startup}/toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

    Route::post('/reviews/{startup}',          [ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/reviews/{review}',         [ReviewController::class, 'destroy'])->name('reviews.destroy');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
    Route::get('/',                           [AdminController::class, 'index'])->name('index');
    Route::get('/startups',                   [AdminController::class, 'startups'])->name('startups');
    Route::post('/startups/{startup}/approve',[AdminController::class, 'approve'])->name('startups.approve');
    Route::post('/startups/{startup}/reject', [AdminController::class, 'reject'])->name('startups.reject');
    Route::delete('/startups/{startup}',      [AdminController::class, 'deleteStartup'])->name('startups.delete');
    
    // Admin User & KYC Management
    Route::get('/users',                      [AdminController::class, 'users'])->name('users');
    Route::post('/users/{user}/role',         [AdminController::class, 'updateUserRole'])->name('users.role.update');
    Route::post('/users/{user}/kyc/approve',  [AdminController::class, 'approveKyc'])->name('users.kyc.approve');
    Route::post('/users/{user}/kyc/reject',   [AdminController::class, 'rejectKyc'])->name('users.kyc.reject');
    Route::delete('/users/{user}',            [AdminController::class, 'deleteUser'])->name('users.delete');
    
    // Admin Inquiries
    Route::get('/inquiries',                  [AdminController::class, 'inquiries'])->name('inquiries');
    Route::post('/inquiries/{inquiry}/reply', [AdminController::class, 'replyInquiry'])->name('inquiries.reply');
    Route::post('/inquiries/{inquiry}/close', [AdminController::class, 'closeInquiry'])->name('inquiries.close');
    
    // Admin Investments
    Route::get('/investments',                [AdminController::class, 'investments'])->name('investments');
    Route::post('/investments/{investment}/approve', [AdminController::class, 'approveInvestment'])->name('investments.approve');
    Route::post('/investments/{investment}/reject',  [AdminController::class, 'rejectInvestment'])->name('investments.reject');

    Route::get('/categories',                 [AdminController::class, 'categories'])->name('categories');
    Route::post('/categories',                [AdminController::class, 'storeCategory'])->name('categories.store');
    Route::delete('/categories/{category}',   [AdminController::class, 'deleteCategory'])->name('categories.delete');
});
