<?php

use App\Http\Controllers\Admin\AdminChatController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\FaqResponsesController as AdminFaqResponsesController;
use App\Http\Controllers\Admin\LawController as AdminLawController;
use App\Http\Controllers\Admin\OpinionsController as AdminOpinionsController;
use App\Http\Controllers\Admin\UsageController as AdminUsageController;
use App\Http\Controllers\Admin\UsersController as AdminUsersController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MessageController;
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

Route::get('/', [ChatController::class, 'publicIndex'])->name('legal.ai');

Route::get('/legal-ai/new', [ChatController::class, 'publicNew'])->name('legal.ai.new');
Route::get('/legal-ai/saved', [ChatController::class, 'publicSaved'])->name('legal.ai.saved');
Route::get('/legal-ai/{conversationId}', [ChatController::class, 'publicShow'])
    ->whereNumber('conversationId')
    ->name('legal.ai.show');

Route::post('/legal-ai/conversations', [ConversationController::class, 'storePublic'])->name('legal.ai.conversations.store');
Route::patch('/legal-ai/conversations/{conversationId}', [ConversationController::class, 'updatePublic'])
    ->whereNumber('conversationId')
    ->name('legal.ai.conversations.update');
Route::post('/legal-ai/conversations/{conversationId}/toggle-pin', [ConversationController::class, 'togglePinPublic'])
    ->whereNumber('conversationId')
    ->name('legal.ai.conversations.toggle-pin');
Route::post('/legal-ai/conversations/{conversationId}/toggle-save', [ConversationController::class, 'toggleSavePublic'])
    ->whereNumber('conversationId')
    ->name('legal.ai.conversations.toggle-save');
Route::delete('/legal-ai/conversations/{conversationId}', [ConversationController::class, 'destroyPublic'])
    ->whereNumber('conversationId')
    ->name('legal.ai.conversations.destroy');

Route::post('/legal-ai/conversations/{conversationId}/messages', [MessageController::class, 'storePublic'])
    ->whereNumber('conversationId')
    ->middleware('throttle:20,1')
    ->name('legal.ai.messages.store');

Route::get('/dashboard', function () {
    if (auth()->user()->is_admin) {
        return redirect()->route('admin.legal.ai');
    }

    return redirect()->route('chat.index');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/saved', [ChatController::class, 'saved'])->name('chat.saved');
    Route::get('/chat/{conversation}', [ChatController::class, 'show'])->name('chat.show');

    Route::post('/conversations', [ConversationController::class, 'store'])->name('conversations.store');
    Route::patch('/conversations/{conversation}', [ConversationController::class, 'update'])->name('conversations.update');
    Route::post('/conversations/{conversation}/toggle-pin', [ConversationController::class, 'togglePin'])->name('conversations.toggle-pin');
    Route::post('/conversations/{conversation}/toggle-save', [ConversationController::class, 'toggleSave'])->name('conversations.toggle-save');
    Route::delete('/conversations/{conversation}', [ConversationController::class, 'destroy'])->name('conversations.destroy');

    Route::post('/conversations/{conversation}/messages', [MessageController::class, 'store'])->middleware('throttle:20,1')->name('messages.store');
});

Route::middleware(['auth', 'can:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/legal-ai', [AdminChatController::class, 'index'])->name('legal.ai');
    Route::get('/legal-ai/new', [AdminChatController::class, 'create'])->name('legal.ai.new');
    Route::get('/legal-ai/saved', [AdminChatController::class, 'saved'])->name('legal.ai.saved');
    Route::get('/legal-ai/{conversation}', [AdminChatController::class, 'show'])->name('legal.ai.show');
    Route::get('/users', [AdminUsersController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}', [AdminUsersController::class, 'update'])->name('users.update');
    Route::get('/usage', [AdminUsageController::class, 'index'])->name('usage.index');
    Route::post('/opinions/extract', [AdminOpinionsController::class, 'extract'])->name('opinions.extract');
    Route::resource('opinions', AdminOpinionsController::class);
    Route::resource('faq-responses', AdminFaqResponsesController::class)->except(['show', 'create']);

    Route::resource('laws', AdminLawController::class);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
