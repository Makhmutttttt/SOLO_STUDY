@extends('layouts.app')

@section('title', 'Мои тесты')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4 text-center">Мои тесты</h2>

    @if ($tests->isEmpty())
        <div class="alert alert-info text-center" role="alert">
            У вас пока нет тестов.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover shadow-sm">
                <thead class="table-dark">
                    <tr>
                        <th>Название</th>
                        <th>Описание</th>
                        <th class="text-center">Действие</th>
                    </tr>
                </thead>
                <tbody>




                    <a href="{{ route('mistakes.pass') }}" class="btn btn-warning mb-3">
                        🛠 Работа над ошибками
                    </a>
                    <div class="container mt-4">
                        <div class="row">
                            @foreach($tests as $test)
                                <div class="col-md-6 mb-4">
                                    <div class="card h-100 d-flex flex-column justify-content-between">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $test->title }}</h5>
                                            <p class="card-text">{{ $test->description }}</p>
                                        </div>

                                        <div class="card-footer bg-white border-0 text-center">
                                            <form action="{{ route('test.start', $test->id) }}" method="POST" class="mb-2">
                                                @csrf
                                                <button type="submit" class="btn btn-success w-50 mx-auto d-block">
                                                    ▶️ Начать тест
                                                </button>
                                            </form>

                                            <form action="{{ route('test.destroy', $test->id) }}" method="POST"
                                                onsubmit="return confirm('Удалить этот тест?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger w-50 mx-auto d-block">
                                                    🗑️ Удалить тест
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
