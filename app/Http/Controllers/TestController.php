<?php
// Test Controller
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\ProcessAiRequest;
use App\Models\Test;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use App\Models\Question;
use Illuminate\Support\Facades\Log;


class TestController extends Controller
{
    public function create()
    {
        return view('tests.create');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'topic' => 'required|string|max:255',
            'num_questions' => 'required|integer|min:1|max:50',
        ]);

        $subject = $request->input('subject');
        $topic = $request->input('topic');
        $numQuestions = $request->input('num_questions');

        // Создаем тест в БД, но пока без вопросов
        $test = Test::create([
            'title' => "$subject - $topic",
            'description' => "Автоматически сгенерированный тест",
            'status' => 'pending', // Указываем статус, пока ждем AI
            'user_id' => auth()->id(),
            'num_questions' => $numQuestions, // ✅ Сохраняем в БД
        ]);


        Log::info('Создан тест', ['test_id' => $test->id]);

        // Запускаем задачу в очередь
        ProcessAiRequest::dispatch($test->id, $subject, $topic, $numQuestions);

        // return redirect()->route('tests.index')->with('status', 'Тест создается. Обновите страницу позже.');
        return response()->json([
            'test_id' => $test->id,
            'status' => 'pending'
        ]);
    }

    public function index()
    {
        // Показываем только тесты, которые уже готовы (status = ready)
        $tests = Test::where('user_id', Auth::id())
                     ->where('status', 'ready')
                     ->get();

        return view('tests.index', compact('tests'));
    }




    public function checkTestStatus($id)
    {
        $test = Test::find($id);

        if (!$test) {
            return response()->json(['status' => 'Not Found']);
        }

        if ($test->status === 'Ready') {
            $questions = Question::where('test_id', $test->id)->get();

            return response()->json([
                'status' => 'Ready',
                'test' => $test,
                'questions' => $questions,

            ]);
        }

        return response()->json(['status' => $test->status]);
    }

}
