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
                    @foreach($tests as $test)
                        <div class="card mt-3 p-3 {{ $test->status === 'pending' ? 'text-muted' : '' }}">
                            <h3>{{ $test->title }}</h3>
                            <p>{{ $test->description }}</p>
                            
                            @if($test->status === 'pending')
                                <span class="badge bg-warning">⏳ Ожидает генерации</span>
                            @else
                            <form action="{{ route('test.start', $test->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary w-100">
                                    ▶️ Начать тест
                                </button>
                            </form>
                            @endif

                            <form action="{{ route('test.destroy', $test->id) }}" method="POST" onsubmit="return confirm('Удалить этот тест?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger w-100">
                                    🗑️ Удалить тест
                                </button>
                            </form>       

                        </div>
                    @endforeach

                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
