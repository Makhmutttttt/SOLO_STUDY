<?php
// ProcessAiRequest
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\AIService;
use App\Models\Test;
use App\Models\Question;
use Illuminate\Support\Facades\Log;

class ProcessAiRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $testId;
    protected $subject;
    protected $topic;
    protected $numQuestions;

    public function __construct($testId, $subject, $topic, $numQuestions)
    {
        $this->testId = $testId;
        $this->subject = $subject;
        $this->topic = $topic;
        $this->numQuestions = $numQuestions;
    }

    public function handle(AIService $aiService)
    {
        $test = Test::find($this->testId);
        if (!$test) {
            Log::error("Тест с ID {$this->testId} не найден.");
            return;
        }

        // Генерация теста с помощью AI
        $testData = $aiService->generateTest($this->subject, $this->topic, $this->numQuestions);

        if (!$testData) {
            Log::error("AI не сгенерировал вопросы для теста ID {$this->testId}");
            return;
        }

        // Записываем вопросы в БД
        foreach ($testData as $questionData) {
            Question::create([
                'test_id' => $test->id,
                'question_text' => $questionData['question'],
                'options' => json_encode($questionData['options']),
                'correct_answer' => $questionData['correct'],
            ]);
        }



        $test->status = 'Ready';
        $test->save();

        Log::info("✅ Статус теста ID {$test->id} обновлен на 'ready'");    
    }
}
