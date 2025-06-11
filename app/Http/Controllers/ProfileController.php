<?php

// ProfileController.php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\TestResult;
use App\Models\Mistake;
use App\Models\Test;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Получаем уникальные тесты с лучшими результатами
        $bestResults = TestResult::where('user_id', $user->id)
            ->select('test_id')
            ->selectRaw('MAX(score) as best_score')
            ->groupBy('test_id')
            ->get();
        
        $totalTests = $bestResults->count();
        $totalPoints = $bestResults->sum('best_score');
        
        // Получаем общее количество вопросов по уникальным тестам
        $testIds = $bestResults->pluck('test_id');
        $totalQuestions = Test::whereIn('id', $testIds)->sum('num_questions');
        
        // Расчет среднего результата
        $averageScore = $totalQuestions > 0 
            ? round(($totalPoints / $totalQuestions) * 100, 2) 
            : 0;
        
        $mistakesCount = Mistake::where('user_id', $user->id)->count();
        
        // Прогресс по времени (лучшие результаты за последние 10 тестов)
        $progressData = TestResult::where('user_id', $user->id)
            ->orderBy('completed_at', 'desc')
            ->take(10)
            ->get()
            ->map(function($result) {
                return [
                    'date' => $result->completed_at->format('d.m.Y H:i'),
                    'score' => $result->total_questions > 0 
                        ? round(($result->score / $result->total_questions) * 100)
                        : 0
                ];
            })
            ->reverse()
            ->values()
            ->toArray(); // Преобразуем в массив

        // === Вставляем сюда ===
        $usedDates = [];
        foreach ($progressData as &$item) {
            $date = $item['date'];
            if (isset($usedDates[$date])) {
                $usedDates[$date]++;
                $item['date'] = $date . " #" . $usedDates[$date];
            } else {
                $usedDates[$date] = 1;
                $item['date'] = $date . " #1";
            }
        }
        unset($item);
        // 2. Результаты по темам
        $topicsData = TestResult::with('test')
            ->where('user_id', $user->id)
            ->get()
            ->groupBy('test.title')
            ->map(function($group) {
                return round($group->avg(function($result) {
                    return ($result->score / $result->total_questions) * 100;
                }));
            })
            ->toArray();

        // 3. Соотношение правильных/неправильных ответов
        $allResults = TestResult::where('user_id', $user->id)->get();
        $totalAllQuestions = $allResults->sum('total_questions');
        $correctAnswers = $allResults->sum('score');
        $incorrectAnswers = $totalAllQuestions - $correctAnswers;

        // 4. Теперь — если данных мало или нет, подставь тестовые значения:

        // Вот здесь — добавление тестовых данных, если мало данных:
        if (count($progressData) < 3) {
            $progressData = [
                ['date' => '01.05.2025 10:00 #1', 'score' => 75],
                ['date' => '02.05.2025 11:30 #2', 'score' => 85],
                ['date' => '03.05.2025 14:15 #3', 'score' => 90],
                ['date' => '04.05.2025 16:45 #4', 'score' => 100]
            ];
        }
        if (empty($topicsData)) {
            $topicsData = [
                'Математика - 2 класс Умножение' => 100,
                'Физика - Основы механики' => 85,
                'История - Древний мир' => 75
            ];
        }
        if (!isset($correctAnswers)) $correctAnswers = 10;
        if (!isset($incorrectAnswers)) $incorrectAnswers = 5;
        // Результаты по темам

        return view('profile.index', compact(
            'user',
            'totalTests',
            'totalPoints',
            'averageScore',
            'mistakesCount',
            'progressData',
            'topicsData',
            'correctAnswers',
            'incorrectAnswers'
        ));
    }
}