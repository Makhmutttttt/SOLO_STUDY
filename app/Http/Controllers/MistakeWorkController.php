<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mistake;
use App\Models\Question;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class MistakeWorkController extends Controller
{
    public function pass()
    {
        // Если сессия уже существует
        if (Session::has('mistake_test')) {
            $data = Session::get('mistake_test');
            
            // Если вопросы закончились
            if ($data['current_question'] >= 20) {
                return redirect()->route('mistakes.results');
            }
            
            return $this->showQuestion($data);
        }

        // Инициализация новой сессии
        $mistakes = Mistake::where('user_id', auth()->id())
            ->inRandomOrder()
            ->take(20)
            ->pluck('question_id')
            ->toArray();

        $totalQuestions = count($mistakes);

        // Если нет ошибок
        if ($totalQuestions == 0) {
            return redirect()->route('tests.index')->with('info', 'У вас нет ошибок для работы');
        }

        Session::put('mistake_test', [
            'questions' => $mistakes,
            'current_question' => 0,
            'total_questions' => $totalQuestions, // Сохраняем реальное количество
            'correct_answers' => [],
            'start_time' => now()
        ]);

        return $this->showQuestion(Session::get('mistake_test'));
    }

    private function showQuestion($data)
    {
        $questionId = $data['questions'][$data['current_question']];
        $question = Question::findOrFail($questionId);

        return view('mistakes.pass', [
            'question' => $question,
            'options' => json_decode($question->options, true),
            'progress' => (($data['current_question'] + 1) / 20) * 100
        ]);
    }

    public function submitAnswer(Request $request)
    {
        $data = Session::get('mistake_test');
        $questionId = $data['questions'][$data['current_question']];
        $question = Question::findOrFail($questionId);

        $isCorrect = $request->input('answer') == $question->correct_index;
        $data['correct_answers'][$questionId] = $isCorrect;
        $data['current_question']++;

        // Обновляем счетчик правильных ответов в базе
        $this->updateMistakeCounter($questionId, $isCorrect);

        Session::put('mistake_test', $data);

        // Проверяем по реальному количеству вопросов
        if ($data['current_question'] >= $data['total_questions']) {
            return redirect()->route('mistakes.results');
        }

        return redirect()->route('mistakes.pass');
    }

    private function updateMistakeCounter($questionId, $isCorrect)
    {
        $mistake = Mistake::where([
            'user_id' => Auth::id(),
            'question_id' => $questionId
        ])->first();


        if ($isCorrect) {
            if ($mistake) {
                // Удаляем если 2 правильных ответа
                if ($mistake->correct_count >= 1) { // +1 текущий = 2
                    $mistake->delete();
                } else {
                    $mistake->increment('correct_count');
                }
            }
        } else {
            if (!$mistake) {
                Mistake::create([
                    'user_id' => Auth::id(),
                    'question_id' => $questionId,
                    'correct_count' => 0
                ]);
            } else {
                // Сбрасываем счетчик при ошибке
                $mistake->update(['correct_count' => 0]);
            }
        }
    }

    public function results()
    {
        $data = Session::get('mistake_test');
        
        if (!$data || $data['current_question'] < $data['total_questions']) {
            return redirect()->route('mistakes.pass');
        }

        $correctCount = count(array_filter($data['correct_answers']));
        $wrongAnswers = array_keys(array_filter($data['correct_answers'], fn($v) => !$v));

        Session::forget('mistake_test');

        return view('mistakes.results', [
            'correctCount' => $correctCount,
            'total' => $data['total_questions'], // Используем реальное количество
            'wrongQuestions' => Question::findMany($wrongAnswers)
        ]);
    }
}