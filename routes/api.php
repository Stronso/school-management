<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/subscribed-courses', [\App\Http\Controllers\CourseSubscriptionController::class, 'getSubscribedCourses']);
});
use App\Http\Controllers\CourseSubscriptionController;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CourseController;
use App\Http\Controllers\API\ActivityController;
use App\Http\Controllers\API\ChatController;
use App\Http\Controllers\API\ChatbotController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/subscribe-course', [CourseSubscriptionController::class, 'subscribe']);
    Route::get('/course-subscription-status', [CourseSubscriptionController::class, 'checkSubscription']);
});

// Public routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('profile', [AuthController::class, 'profile']);
    Route::post('profile', [AuthController::class, 'updateProfile']);

    // Course routes
    Route::apiResource('courses', CourseController::class);
    Route::get('courses/{course}/download', [CourseController::class, 'download']);

    // Activity routes
    Route::get('courses/{course}/activities', [ActivityController::class, 'index']);
    Route::post('courses/{course}/activities', [ActivityController::class, 'store']);
    Route::get('activities/{activity}', [ActivityController::class, 'show']);
    Route::put('activities/{activity}', [ActivityController::class, 'update']);
    Route::delete('activities/{activity}', [ActivityController::class, 'destroy']);
    Route::post('activities/{activity}/submit', [ActivityController::class, 'submit']);
    Route::post('submissions/{submission}/grade', [ActivityController::class, 'grade']);
    Route::get('submissions/{submission}/download', [ActivityController::class, 'downloadSubmission']);

    // Chat routes
    Route::get('conversations', [ChatController::class, 'conversations']);
    Route::post('conversations', [ChatController::class, 'createConversation']);
    Route::get('conversations/{conversation}/messages', [ChatController::class, 'messages']);
    Route::post('conversations/{conversation}/messages', [ChatController::class, 'sendMessage']);
    Route::post('conversations/{conversation}/users', [ChatController::class, 'addUsers']);
    Route::delete('conversations/{conversation}/users/{user}', [ChatController::class, 'removeUser']);
    Route::delete('conversations/{conversation}/leave', [ChatController::class, 'leaveConversation']);

    // Chatbot routes
    Route::post('chatbot/chat', [ChatbotController::class, 'chat']);
    Route::get('chatbot/questions', [ChatbotController::class, 'listQuestions']);
    Route::post('chatbot/questions', [ChatbotController::class, 'addQuestion']);
    Route::put('chatbot/questions/{question}', [ChatbotController::class, 'updateQuestion']);
    Route::delete('chatbot/questions/{question}', [ChatbotController::class, 'deleteQuestion']);
    Route::get('chatbot/history', [ChatbotController::class, 'getConversationHistory']);
});
