<?php

namespace App\Http\Controllers;

use App\Models\Mistake;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class MistakeController extends Controller
{
    public function index()
    {
        $mistakes = Mistake::where('user_id', auth()->id())
            ->with('question')
            ->get();

        return view('mistakes.index', compact('mistakes'));
    }

    public function start()
    {
        $mistakes = Mistake::where('user_id', auth()->id())->inRandomOrder()->get();
        $sessionData = [
            'questions' => $mistakes->pluck('question_id')->toArray(),
            'current_index' => 0,
            'answers' => [],
            'start_time' => now(),
        ];

        session(['mistake_session' => $sessionData]);

        return redirect()->route('mistakes.pass');
    }

    public function pass()
    {
        $session = session('mistake_session');

        if (!$session || $session['current_index'] >= count($session['questions'])) {
            return redirect()->route('mistakes.results');
        }

        $questionId = $session['questions'][$session['current_index']];
        $question = Question::findOrFail($questionId);

        $options = json_decode($question->options, true);
        $correctValue = $options[$question->correct_index];
        shuffle($options);
        $newCorrectIndex = array_search($correctValue, $options);

        return view('mistakes.pass', [
            'question' => $question,
            'options' => $options,
            'correct_index' => $newCorrectIndex
        ]);
    }

    public function answer(Request $request)
    {
        $request->validate([
            'answer' => 'required|integer',
        ]);

        $session = session('mistake_session');
        $questionId = $session['questions'][$session['current_index']];
        $question = Question::findOrFail($questionId);

        $isCorrect = $request->input('answer') == $question->correct_index;
        $session['answers'][$questionId] = $isCorrect;

        $mistake = Mistake::where('user_id', auth()->id())
            ->where('question_id', $questionId)
            ->first();

        if ($isCorrect) {
            if ($mistake) {
                if ($mistake->correct_count >= 1) {
                    $mistake->delete();
                } else {
                    $mistake->increment('correct_count');
                }
            }
        } else {
            if (!$mistake) {
                Mistake::create([
                    'user_id' => auth()->id(),
                    'question_id' => $questionId,
                    'correct_count' => 0,
                ]);
            } else {
                $mistake->update(['correct_count' => 0]);
            }
        }

        $session['current_index']++;
        session(['mistake_session' => $session]);

        return redirect()->route('mistakes.pass');
    }

    public function results()
    {
        $session = session('mistake_session');

        if (!$session) {
            return redirect()->route('mistakes.index');
        }

        $correctCount = collect($session['answers'])->filter()->count();
        $total = count($session['questions']);

        return view('mistakes.results', [
            'correctCount' => $correctCount,
            'total' => $total,
        ]);
    }
}
