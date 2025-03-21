@extends('layouts.app')
{{-- show.blade --}}
@section('content')
<div class="container py-4">
    <div class="card shadow-lg p-4">
        <h2 class="text-center mb-4 text-primary">📘 {{ $test->title }}</h2>
        <p class="text-muted text-center">{{ $test->description }}</p>

        @if($questions->isEmpty())
            <p class="text-center text-danger">⚠️ Вопросы ещё не загружены. Пожалуйста, обновите страницу позже.</p>
        @else
            @foreach($questions as $index => $question)
                <div class="card mt-3 p-3">
                    <strong class="fs-5">{{ $index + 1 }}. {{ $question->question_text }}</strong>
                    <ul class="list-group mt-2">
                        @foreach(json_decode($question->options, true) as $option)
                            <li class="list-group-item">{{ $option }}</li>
                        @endforeach
                    </ul>
                    <p class="mt-2 text-success fw-bold">✅ Правильный ответ: {{ $question->correct_answer }}</p>
                </div>
            @endforeach
        @endif
    </div>
</div>
@endsection
