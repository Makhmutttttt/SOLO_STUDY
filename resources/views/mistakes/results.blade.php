@extends('layouts.app')

@section('title', 'Результаты работы над ошибками')

@section('content')
<div class="container py-4">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">📊 Результаты работы с 20 вопросами</h4>
        </div>

        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="alert alert-success">
                        <h5>✅ Правильных ответов: {{ $correctCount }}</h5>
                        <h5>📝 Всего вопросов: {{ $total }}</h5> <!-- Динамическое значение -->
                        <div class="progress mt-3">
                            <div class="progress-bar bg-success" 
                                style="width: {{ ($correctCount/$total)*100 }}%">
                                {{ round(($correctCount/$total)*100) }}%
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    @if(count($wrongQuestions) > 0)
                        <div class="alert alert-danger">
                            <h5>❌ Неправильные ответы:</h5>
                            <ul class="list-group mt-3">
                                @foreach($wrongQuestions as $question)
                                    <li class="list-group-item">
                                        <div class="fw-bold">{{ $question->question_text }}</div>
                                        <div class="mt-2">
                                            <span class="text-success">Правильный ответ: 
                                                {{ json_decode($question->options)[$question->correct_index] }}
                                            </span>
                                        </div>
                                        
                                        @if(!empty($question->explanation))
                                            <div class="mt-2 text-secondary">
                                                <strong>Пояснение:</strong> {{ $question->explanation }}
                                            </div>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <h5>🎉 Отлично! Все ответы правильные!</h5>
                        </div>
                    @endif
                </div>
            </div>

            <div class="text-center">
                <a href="{{ route('tests.index') }}" class="btn btn-primary">
                    ← Вернуться к тестам
                </a>
            </div>
        </div>
    </div>
</div>
@endsection