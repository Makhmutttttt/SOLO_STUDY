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
            'difficult_level' => 'required|string|max:255',
            'num_questions' => 'required|integer|min:1|max:50',
            'content' => 'nullable|string|min:100',
            'ready_test' => 'nullable|string|min:100',

        ]);

        $subject = $request->input('subject');
        $topic = $request->input('topic');
        $difficult_level = $request->input('difficult_level');
        $numQuestions = $request->input('num_questions');
        $content = $request->input('content');
        $readyTest = $request->input('ready_test');

        // Создаем тест в БД, но пока без вопросов
        $test = Test::create([
            'title' => "$subject - $topic",
            // 'description' => "Автоматически сгенерированный тест",
            'description' => $readyTest ? "Сгенерировано из готового теста" : ($content ? "Сгенерировано по лекции" : "Сгенерировано по теме"),

            'status' => 'pending', // Указываем статус, пока ждем AI
            'user_id' => auth()->id(),
            'difficult_level' => $difficult_level,
            'num_questions' => $numQuestions, // ✅ Сохраняем в БД
            'content' => $content, // ✅ Сохраняем в БД
            'ready_test' => $readyTest,
        ]);


        Log::info('Создан тест', ['test_id' => $test->id]);

        // Запускаем задачу в очередь
        ProcessAiRequest::dispatch(
            $test->id,
            $subject,
            $topic,
            $difficult_level,
            $numQuestions,
            $content,
            $readyTest // 👈 передаём ready_test
        );
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



    public function destroy($id)
    {
        $test = Test::findOrFail($id);

        // Удаляем ошибки, связанные с вопросами этого теста
        foreach ($test->questions as $question) {
            $question->mistakes()->delete(); // если у Question есть отношение mistakes()
        }

        // Удаляем вопросы
        $test->questions()->delete();

        // Удаляем сам тест
        $test->delete();

        return redirect()->back()->with('success', 'Тест удалён.');
    }

}
