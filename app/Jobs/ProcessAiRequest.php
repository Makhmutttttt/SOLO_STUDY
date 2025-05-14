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
    protected $difficult_level;
    protected $numQuestions;
    protected $content;
    protected $ready_test;


    public function __construct($testId, $subject, $topic, $difficult_level, $numQuestions, $content = null, $ready_test = null)
    {
        $this->testId = $testId;
        $this->subject = $subject;
        $this->topic = $topic;
        $this->difficult_level = $difficult_level;
        $this->numQuestions = $numQuestions;
        $this->content = $content;
        $this->ready_test = $ready_test;

        // Указываем, что задача будет обрабатываться в очереди 'ai'
        $this->onQueue('ai');
    }

    public function handle(AIService $aiService)
    {
        $test = Test::find($this->testId);
        if (!$test) {
            Log::error("Тест с ID {$this->testId} не найден.");
            return;
        }
        // Генерация теста с помощью AI
        $testData = $aiService->generateTest(
            $this->subject,
            $this->topic,
            $this->difficult_level,
            $this->numQuestions,
            $this->content, // 👈 Передаем content
            $this->ready_test // 👈 передаём сюда

        );
        if (!$testData) {
            Log::error("AI не сгенерировал вопросы для теста ID {$this->testId}");
            return;
        }

        // Записываем вопросы в БД
        foreach ($testData as $questionData) {
            $options = $questionData['options'];
            $correctLetter = strtoupper(trim($questionData['correct']));
            $correctIndex = array_search($correctLetter, ['A', 'B', 'C', 'D']);

            Question::create([
                'test_id' => $test->id,
                'question_text' => $questionData['question'],
                'options' => json_encode($questionData['options']),
                'correct_index' => $correctIndex,
                'explanation' => $questionData['explanation'], // объяснение
            ]);
        }



        $test->status = 'Ready';
        $test->save();

        Log::info("✅ Статус теста ID {$test->id} обновлен на 'ready'");    
    }
}
