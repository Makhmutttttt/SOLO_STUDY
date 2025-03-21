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
                    {{-- @foreach($tests as $test)
                        <tr>
                            <td>{{ $test->title }}</td>
                            <td>{{ $test->description }}</td>
                            <td class="text-center">
                                <a href="{{ url('/tests/' . $test->id . '/start') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-play"></i> Пройти тест
                                </a>
                            </td>
                        </tr>
                    @endforeach --}}
                    @foreach($tests as $test)
                        <div class="card mt-3 p-3 {{ $test->status === 'pending' ? 'text-muted' : '' }}">
                            <h3>{{ $test->title }}</h3>
                            <p>{{ $test->description }}</p>
                            
                            @if($test->status === 'pending')
                                <span class="badge bg-warning">⏳ Ожидает генерации</span>
                            @else
                                <a href="{{ route('tests.show', $test->id) }}" class="btn btn-primary">Пройти тест</a>
                            @endif
                        </div>
                    @endforeach

                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
