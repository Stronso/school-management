<?php

use Illuminate\Support\Facades\Route;

Route::get('/calendar', [\App\Http\Controllers\CalendarController::class, 'index'])->name('calendar.index');

Route::prefix('calendar')->group(function () {
    Route::get('/events', [\App\Http\Controllers\CalendarController::class, 'events'])->name('calendar.events');
    Route::post('/events', [\App\Http\Controllers\CalendarController::class, 'store'])->name('calendar.events.store');
    Route::put('/events/{event}', [\App\Http\Controllers\CalendarController::class, 'update'])->name('calendar.events.update');
    Route::delete('/events/{event}', [\App\Http\Controllers\CalendarController::class, 'destroy'])->name('calendar.events.destroy');
});
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CourseController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Authentication Routes
Auth::routes();

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // User Management
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.delete');
    
    // Course Management
    Route::get('/courses', [AdminController::class, 'courses'])->name('courses');
    Route::get('/courses/create', [AdminController::class, 'createCourse'])->name('courses.create');
    Route::post('/courses', [AdminController::class, 'storeCourse'])->name('courses.store');

    // Course Subscription Approvals
    Route::get('/course-subscriptions/approve', [App\Http\Controllers\CourseSubscriptionController::class, 'approvalPage'])->name('course_subscriptions.approve');
    Route::post('/course-subscriptions/approve/{subscription}', [App\Http\Controllers\CourseSubscriptionController::class, 'approveSubscription'])->name('course_subscriptions.approve.post');
    
    // Chat Management
    Route::get('/chat/messages', [AdminController::class, 'chatMessages'])->name('chat.messages');
    Route::delete('/chat/messages/{message}', [AdminController::class, 'deleteMessage'])->name('chat.messages.delete');
    
    // Chatbot Management
    Route::get('/chatbot/responses', [AdminController::class, 'chatbotResponses'])->name('chatbot.responses');
    Route::get('/chatbot/responses/create', [AdminController::class, 'createChatbotResponse'])->name('chatbot.responses.create');
    Route::post('/chatbot/responses', [AdminController::class, 'storeChatbotResponse'])->name('chatbot.responses.store');
    Route::delete('/chatbot/responses/{response}', [AdminController::class, 'deleteChatbotResponse'])->name('chatbot.responses.delete');
    
    // Compose Message
    Route::get('/chat/compose', [AdminController::class, 'composeMessage'])->name('chat.compose');
});

Route::middleware(['auth'])->group(function () {
    // Course Routes
    Route::resource('courses', CourseController::class);
    Route::get('courses/{course}/download', [CourseController::class, 'downloadFile'])->name('courses.download');
    Route::post('courses/generate-code', [CourseController::class, 'generateCode'])->name('courses.generate-code');

    // Student Course Subscription Page
    Route::get('/course-subscriptions/subscribe', [App\Http\Controllers\CourseSubscriptionController::class, 'studentSubscribePage'])->name('course_subscriptions.student_subscribe');
    Route::post('/course-subscriptions/subscribe', [App\Http\Controllers\CourseSubscriptionController::class, 'subscribeStudent'])->name('course_subscriptions.subscribe');

    // Chat Messages Route for normal users
    Route::get('/chat/messages/{conversationId?}', [App\Http\Controllers\HomeController::class, 'chatMessages'])->name('chat.messages');

    // Route to delete a message
    Route::delete('/chat/messages/{id}', [App\Http\Controllers\HomeController::class, 'deleteMessage'])->name('chat.messages.delete');

    // Route to block a user
    Route::post('/chat/block/{userId}', [App\Http\Controllers\HomeController::class, 'blockUser'])->name('chat.block');

    // Route to store a new message
    Route::post('/chat/messages', [App\Http\Controllers\HomeController::class, 'storeMessage'])->name('chat.messages.store');

    // Route to show user selection page for chat
    Route::get('/chat/select-user', [App\Http\Controllers\HomeController::class, 'showUserSelection'])->name('chat.select_user');
    // Deprecated send message route
    Route::get('/chat/send', [App\Http\Controllers\HomeController::class, 'showSendMessageForm'])->name('chat.send');

    // Progress chart API
    Route::get('/progress/monthly', [App\Http\Controllers\ProgressController::class, 'getMonthlyProgress'])->name('progress.monthly');
    Route::get('/progress', [App\Http\Controllers\ProgressController::class, 'showProgress'])->name('progress.show');

    // User Profile routes for students and teachers
    Route::get('/profile', [App\Http\Controllers\UserProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [App\Http\Controllers\UserProfileController::class, 'update'])->name('profile.update');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
