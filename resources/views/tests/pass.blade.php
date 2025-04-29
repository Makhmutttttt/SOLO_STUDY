@extends('layouts.app')

@section('title', 'Прохождение теста')

@section('content')
<div class="container py-4">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Вопрос {{ session('current_test.current_question') + 1 }} из {{ count(session('current_test.questions')) }}</h4>
                <div class="progress" style="width: 200px; height: 20px;">
                    <div class="progress-bar bg-warning" 
                         style="width: {{ (session('current_test.current_question') + 1)/count(session('current_test.questions'))*100 }}%">
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            @php
                $question = App\Models\Question::find(
                    session('current_test.questions')[session('current_test.current_question')]
                );
                $options = json_decode($question->options, true);
            @endphp

            <h5 class="card-title mb-4">{{ $question->question_text }}</h5>
            
            <form method="POST" action="{{ route('test.answer') }}">
                @csrf
                <div class="list-group">
                    @foreach($options as $index => $option)
                        <label class="list-group-item d-flex align-items-center">
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
                
                <div class="mt-4 d-grid">
                    <button type="submit" class="btn btn-success btn-lg">
                        📝 Ответить
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection