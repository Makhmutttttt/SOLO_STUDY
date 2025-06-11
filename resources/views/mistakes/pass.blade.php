@extends('layouts.app')

@section('title', 'Работа над ошибками')

@section('content')
<div class="container py-4">
    <div class="card shadow-lg border-danger">
        <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">🔁 Повтор ошибок</h4>
            <div class="text-end">
                <span class="badge bg-light text-dark fs-6">
                    Вопрос {{ session('mistake_test.current_question') + 1 }} / {{ count(session('mistake_test.questions')) }}
                </span>
            </div>
        </div>

        <div class="card-body">
            @php
                $question = App\Models\Question::find(
                    session('mistake_test.questions')[session('mistake_test.current_question')]
                );
                $options = json_decode($question->options, true);
            @endphp

            <h5 class="mb-4 text-primary fs-4"><i class="bi bi-question-circle-fill"></i> {{ $question->question_text }}</h5>

            <form method="POST" action="{{ route('mistakes.answer') }}">
                @csrf
                <div class="list-group mb-4">
                    @foreach($options as $index => $option)
                        <label class="list-group-item list-group-item-action d-flex align-items-center border rounded mb-2 shadow-sm">
                            <input 
                                type="radio" 
                                name="answer" 
                                value="{{ $index }}" 
                                class="form-check-input me-3"
                                required
                            >
                            <span class="fs-5">{{ $option }}</span>
                        </label>
                    @endforeach
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-danger btn-lg shadow">
                        🚀 Отправить ответ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
