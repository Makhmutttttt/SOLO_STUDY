@extends('layouts.app')

@section('title', 'Мои ошибки')

@section('content')
<div class="container py-4">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-gradient bg-warning text-dark d-flex align-items-center justify-content-between">
            <h4 class="mb-0">
                📘 Мои ошибки — <small class="text-muted fs-6">учимся на них!</small>
            </h4>
        </div>

        <div class="card-body">
            @if($mistakes->count() > 0)
                <ul class="list-group list-group-flush">
                    @foreach($mistakes as $mistake)
                        @if($mistake->question)
                            <li class="list-group-item bg-light rounded mb-3 border-start border-4 border-danger shadow-sm">
                                <div class="mb-2">
                                    <span class="fw-semibold text-primary">❓ Вопрос:</span>
                                    <div class="fw-bold mt-1">{{ $mistake->question->question_text }}</div>
                                </div>

                                <div class="mb-2">
                                    <span class="fw-semibold text-success">✅ Правильный ответ:</span>
                                    <div class="mt-1">
                                        {{ json_decode($mistake->question->options)[$mistake->question->correct_index] }}
                                    </div>
                                </div>

                                @if(!empty($mistake->question->explanation))
                                    <div class="mb-2">
                                        <span class="fw-semibold text-secondary">💡 Пояснение:</span>
                                        <div class="mt-1">{{ $mistake->question->explanation }}</div>
                                    </div>
                                @endif
                            </li>
                        @endif
                    @endforeach
                </ul>
            @else
                <div class="alert alert-success text-center mt-3 fs-5">
                    🎉 У вас нет ошибок. Продолжайте в том же духе!
                </div>
            @endif

            <div class="text-center mt-4">
                <a href="{{ route('tests.index') }}" class="btn btn-outline-primary btn-lg px-4">← Назад к тестам</a>
            </div>
        </div>
    </div>
</div>
@endsection
