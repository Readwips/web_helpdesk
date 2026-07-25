<?php

use App\Http\Controllers\AssetAssignmentController;
use App\Http\Controllers\AssetCategoryController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetRepairController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\KnowledgeArticleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TicketActionController;
use App\Http\Controllers\TicketCategoryController;
use App\Http\Controllers\TicketCommentController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('tickets', TicketController::class);
    Route::post('tickets/{ticket}/comments', [TicketCommentController::class, 'store'])->name('tickets.comments.store');
    Route::put('tickets/{ticket}/assign', [TicketActionController::class, 'assign'])->middleware('role:admin')->name('tickets.assign');
    Route::put('tickets/{ticket}/status', [TicketActionController::class, 'status'])->middleware('role:admin,technician')->name('tickets.status');
    Route::put('tickets/{ticket}/confirm', [TicketActionController::class, 'confirm'])->middleware('role:user')->name('tickets.confirm');
    Route::put('tickets/{ticket}/reopen', [TicketActionController::class, 'reopen'])->middleware('role:user')->name('tickets.reopen');
    Route::get('attachments/{attachment}', [TicketCommentController::class, 'download'])->name('attachments.download');

    Route::resource('assets', AssetController::class);
    Route::get('assets/{asset}/assign', [AssetAssignmentController::class, 'create'])->middleware('role:admin')->name('assets.assign.create');
    Route::post('assets/{asset}/assign', [AssetAssignmentController::class, 'store'])->middleware('role:admin')->name('assets.assign.store');
    Route::put('assets/{asset}/return', [AssetAssignmentController::class, 'return'])->middleware('role:admin')->name('assets.return');
    Route::get('assets/{asset}/repairs/create', [AssetRepairController::class, 'create'])->middleware('role:admin,technician')->name('repairs.create');
    Route::post('assets/{asset}/repairs', [AssetRepairController::class, 'store'])->middleware('role:admin,technician')->name('repairs.store');
    Route::get('repairs/{repair}', [AssetRepairController::class, 'show'])->middleware('role:admin,technician')->name('repairs.show');
    Route::get('repairs/{repair}/edit', [AssetRepairController::class, 'edit'])->middleware('role:admin,technician')->name('repairs.edit');
    Route::put('repairs/{repair}', [AssetRepairController::class, 'update'])->middleware('role:admin,technician')->name('repairs.update');

    Route::resource('knowledge', KnowledgeArticleController::class)->parameters(['knowledge' => 'article']);

    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class)->except('show');
        Route::resource('departments', DepartmentController::class)->except('show');
        Route::resource('ticket-categories', TicketCategoryController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('asset-categories', AssetCategoryController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::get('reports/{type?}', [ReportController::class, 'index'])->where('type', 'tickets|assets|repairs|technicians')->name('reports.index');
        Route::get('reports/{type}/excel', [ReportController::class, 'excel'])->where('type', 'tickets|assets|repairs|technicians')->name('reports.excel');
        Route::get('reports/{type}/pdf', [ReportController::class, 'pdf'])->where('type','tickets|assets|repairs|technicians')->name('reports.pdf');
    });
});

require __DIR__.'/auth.php';
