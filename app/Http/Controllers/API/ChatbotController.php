<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ChatbotQuestion;
use App\Models\ChatbotConversation;
use Google\Cloud\Core\ServiceBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChatbotController extends Controller
{
    protected $gemini;

    public function __construct()
    {
        // Initialize Gemini API client
        $cloud = new ServiceBuilder([
            'keyFilePath' => storage_path('app/google-credentials.json'),
        ]);
        $this->gemini = $cloud->ai();
    }

    public function chat(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // First, check for predefined answers
        $predefinedAnswer = ChatbotQuestion::where('question', 'LIKE', '%' . $request->message . '%')
            ->first();

        if ($predefinedAnswer) {
            $response = $predefinedAnswer->answer;
        } else {
            // Use Gemini API for dynamic responses
            try {
                $result = $this->gemini->generateText([
                    'prompt' => [
                        'text' => $request->message
                    ],
                    'temperature' => 0.7,
                    'maxOutputTokens' => 1024,
                ]);

                $response = $result->text();
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Error generating response',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        // Save the conversation
        ChatbotConversation::create([
            'user_id' => $request->user()->id,
            'user_input' => $request->message,
            'bot_response' => $response
        ]);

        return response()->json([
            'message' => $response
        ]);
    }

    public function addQuestion(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'question' => 'required|string',
            'answer' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $question = ChatbotQuestion::create([
            'question' => $request->question,
            'answer' => $request->answer,
            'created_by' => $request->user()->id,
        ]);

        return response()->json($question, 201);
    }

    public function updateQuestion(Request $request, ChatbotQuestion $question)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'question' => 'string',
            'answer' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $question->update($request->only(['question', 'answer']));
        return response()->json($question);
    }

    public function deleteQuestion(Request $request, ChatbotQuestion $question)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $question->delete();
        return response()->json(['message' => 'Question deleted successfully']);
    }

    public function listQuestions(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $questions = ChatbotQuestion::with('creator')
            ->latest()
            ->paginate(20);

        return response()->json($questions);
    }

    public function getConversationHistory(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $conversations = ChatbotConversation::with('user')
            ->latest()
            ->paginate(50);

        return response()->json($conversations);
    }
} 