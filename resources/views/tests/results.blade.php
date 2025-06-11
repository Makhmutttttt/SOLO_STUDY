@extends('layouts.app')

@section('title', 'Результаты теста')

@section('content')
<div class="container py-4">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h2 class="mb-0">Результаты теста: {{ $test->title }}</h2>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="text-success">✅ Правильных ответов: {{ $correctCount }}</h4>
                    <h4 class="text-danger">❌ Ошибок: {{ $total - $correctCount }}</h4>
                    <h4 class="text-primary">📝 Всего вопросов: {{ $total }}</h4>
                    <div class="progress mt-3" style="height: 30px;">
                        <div class="progress-bar bg-success" 
                             style="width: {{ ($correctCount/$total)*100 }}%">
                            {{ number_format(($correctCount/$total)*100, 1) }}%
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    @if($wrongAnswers->isNotEmpty())
                        <h4 class="text-danger mb-3">Ошибочные ответы:</h4>
                        @foreach($wrongAnswers as $questionId => $answer)
                            @php
                                $question = App\Models\Question::find($questionId);
                                $options = json_decode($question->options, true);
                            @endphp
                            <div class="card mb-3 border-danger">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $question->question_text }}</h5>
                                    <p class="text-danger">
                                        Ваш ответ: {{ Arr::get($options, $answer['selected'] ?? 'none', 'Нет ответа') }}
                                    </p>
                                    
                                    <p class="text-success">
                                        Правильный ответ: {{ Arr::get($options, $question->correct_index, 'Ошибка системы') }}
                                    </p>
                                    @if($question->explanation)
                                        <div class="alert alert-info mt-2">
                                            <strong>📚 Объяснение:</strong> 
                                            {{ $question->explanation }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="alert alert-success">
                            🎉 Поздравляем! Вы ответили правильно на все вопросы!
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-4 text-center">
                <a href="{{ route('tests.index') }}" class="btn btn-primary btn-lg">
                    ← Вернуться к списку тестов
                </a>
            </div>
        </div>
    </div>
</div>
@endsection