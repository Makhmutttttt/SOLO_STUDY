<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\Question;
use App\Models\Mistake;
use Illuminate\Support\Facades\Auth;

class PassController extends Controller
{
    public function start(Test $test)
    {
        // Приводим статус к нижнему регистру
        $status = strtolower($test->status);
        
        if ($status !== 'ready') {
            abort(403, 'Тест еще не готов к прохождению');
        }
    
        // Проверяем существование отношения через exists()
        if (!$test->questions()->exists()) {
            abort(403, 'Тест не содержит вопросов');
        }
    
        // Инициализация сессии
        session()->put('current_test', [
            'test_id' => $test->id,
            'questions' => $test->questions->pluck('id')->shuffle()->toArray(),
            'current_question' => 0,
            'answers' => [],
            'start_time' => now()
        ]);
    
        return redirect()->route('test.question');
    }

    public function showQuestion()
    {
        $session = session('current_test');
        $questionId = $session['questions'][$session['current_question']];
        $question = Question::findOrFail($questionId);
        
        // Перемешиваем варианты для отображения
        $options = json_decode($question->options, true);
        shuffle($options);

        return view('tests.pass', [
            'question' => $question,
            'options' => $options,
            'progress' => ($session['current_question'] / count($session['questions'])) * 100
        ]);
    }

    public function processAnswer(Request $request)
    {
        $session = session('current_test');
        $questionId = $session['questions'][$session['current_question']];
        $question = Question::findOrFail($questionId);
    
        // Сохранение ответа
        $session['answers'][$questionId] = [
            'selected' => $request->input('answer'),
            'correct' => $request->input('answer') == $question->correct_index
        ];
    
        // Обновление сессии
        session()->put('current_test', $session);
    
        // Обработка ошибок
        if (!$session['answers'][$questionId]['correct']) {
            $this->handleMistake($question);
        }
    
        // Переход к следующему вопросу или завершение
        if ($session['current_question'] < count($session['questions']) - 1) {
            $session['current_question']++;
            session()->put('current_test', $session);
            return redirect()->route('test.question');
        }
    
        return redirect()->route('test.finish');
    }

    private function handleMistake(Question $question)
    {
        Mistake::firstOrCreate([
            'user_id' => Auth::id(),
            'question_id' => $question->id
        ])->update(['correct_count' => 0]);
    }

    private function handleCorrectAnswer(Question $question)
    {
        $mistake = Mistake::where('user_id', Auth::id())
            ->where('question_id', $question->id)
            ->first();

        if ($mistake) {
            if ($mistake->correct_count >= 2) {
                $mistake->delete();
            } else {
                $mistake->increment('correct_count');
            }
        }
    }

    public function finish()
    {
        $session = session('current_test');
        $test = Test::findOrFail($session['test_id']);
        $answers = $session['answers'];
        
        // Подсчет результатов
        $correctCount = collect($answers)->filter(fn($a) => $a['correct'])->count();
        
        // Сохранение результатов (можно создать отдельную модель TestResult)
        
        return view('tests.results', [
            'test' => $test,
            'correctCount' => $correctCount,
            'total' => count($session['questions']),
            'wrongAnswers' => collect($answers)->filter(fn($a) => !$a['correct'])
        ]);
    }
}