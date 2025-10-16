@extends('layouts.app')

@section('title', 'Мой профиль')

@section('content')
<div class="container py-4">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h2 class="mb-0">👤 Мой профиль</h2>
        </div>
        
        <div class="card-body">
            <!-- Основная информация -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h4 class="text-primary">Личная информация</h4>
                    <div class="list-group">
                        <div class="list-group-item">
                            <strong>Имя:</strong> {{ $user->name }}
                        </div>
                        <div class="list-group-item">
                            <strong>Email:</strong> {{ $user->email }}
                        </div>
                        <div class="list-group-item">
                            <strong>Дата регистрации:</strong> {{ $user->created_at->format('d.m.Y') }}
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-danger mt-3">
                            🚪 Выйти из аккаунта
                        </button>
                    </form>
                    
                    <!-- Кнопка в профиле -->
                    {{-- <form action="/start-queue" method="POST">
                        @csrf
                        <button type="submit">Запустить обработчик</button>
                    </form> --}}



                </div>


                
                <div class="col-md-6">
                    <h4 class="text-primary">📊 Статистика</h4>
                    <div class="list-group">
                        <div class="list-group-item d-flex justify-content-between">
                            <span>✅ Пройдено тестов:</span>
                            <span class="badge bg-primary rounded-pill">{{ $totalTests }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between">
                            <span>🧠 Общие баллы:</span>
                            <span class="badge bg-success rounded-pill">{{ $totalPoints }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between">
                            <span>📈 Средний результат:</span>
                            <span class="badge bg-info rounded-pill">{{ $averageScore }}%</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between">
                            <span>⚠️ Ошибок для работы:</span>
                            <span class="badge bg-danger rounded-pill">{{ $mistakesCount }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Графики -->
            <div class="row mt-5">
                <div class="col-xl-6 col-lg-6 mb-4">
                    <div class="card shadow h-100 py-2">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Прогресс по времени</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-container" style="position: relative; height:400px;">
                                <canvas id="progressChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6 col-lg-6 mb-4">
                    <div class="card shadow h-100 py-2">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Результаты по темам</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-container" style="position: relative; height:400px;">
                                <canvas id="topicsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6 col-lg-6 mb-4">
                    <div class="card shadow h-100 py-2">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Соотношение ответов</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-container" style="position: relative; height:400px;">
                                <canvas id="answersChart"></canvas>
                            </div>
                            <div class="mt-4 text-center small">
                                <span class="mr-2">
                                    <i class="fas fa-circle text-success"></i> Правильные
                                </span>
                                <span class="mr-2">
                                    <i class="fas fa-circle text-danger"></i> Неправильные
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Отладочная информация -->
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h4>Отладочная информация</h4>
            </div>
            <div class="card-body">
                <h5>ProgressData:</h5>
                <pre>{{ json_encode($progressData, JSON_PRETTY_PRINT) }}</pre>
                
                <h5>TopicsData:</h5>
                <pre>{{ json_encode($topicsData, JSON_PRETTY_PRINT) }}</pre>
                
                <h5>Correct Answers: {{ $correctAnswers }}</h5>
                <h5>Incorrect Answers: {{ $incorrectAnswers }}</h5>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOMContentLoaded event fired');

        // 1. Прогресс по времени
        const progressCtx = document.getElementById('progressChart');
        if (progressCtx) {
            try {
                let progressData = @json($progressData);
                
                // Уникализируем даты
                const dateCounts = {};
                progressData = progressData.map(item => {
                    if (!dateCounts[item.date]) dateCounts[item.date] = 1;
                    else dateCounts[item.date] += 1;
                    return {
                        date: item.date + ' #' + dateCounts[item.date],
                        score: item.score
                    };
                });

                const progressLabels = progressData.map(item => item.date);
                const progressScores = progressData.map(item => item.score);

                new Chart(progressCtx, {
                    type: 'line',
                    data: {
                        labels: progressLabels,
                        datasets: [{
                            label: 'Результат (%)',
                            data: progressScores,
                            borderColor: '#4e73df',
                            backgroundColor: 'rgba(78, 115, 223, 0.05)',
                            pointRadius: 5,
                            pointHoverRadius: 8,
                            tension: 0.3,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                ticks: {
                                    callback: function(value) {
                                        return value + '%';
                                    }
                                }
                            },
                            x: {
                                ticks: {
                                    autoSkip: false,
                                    maxRotation: 45,
                                    minRotation: 45
                                }
                            }
                        }
                    }
                });
            } catch (e) {
                console.error('Progress chart error:', e);
                // Создаем простой график для теста
                createTestChart(progressCtx, 'line', ['A', 'B', 'C'], [10, 40, 20]);
            }
        }

        // 2. Результаты по темам
        const topicsCtx = document.getElementById('topicsChart');
        if (topicsCtx) {
            try {
                const topicsData = @json($topicsData);
                const topicNames = Object.keys(topicsData);
                const topicScores = Object.values(topicsData);
                
                new Chart(topicsCtx, {
                    type: 'bar',
                    data: {
                        labels: topicNames,
                        datasets: [{
                            label: 'Результат (%)',
                            data: topicScores,
                            backgroundColor: '#36b9cc',
                            borderColor: 'transparent'
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                beginAtZero: true,
                                max: 100,
                                ticks: {
                                    callback: function(value) {
                                        return value + '%';
                                    }
                                }
                            }
                        }
                    }
                });
            } catch (e) {
                console.error('Topics chart error:', e);
                // Создаем простой график для теста
                createTestChart(topicsCtx, 'bar', ['Тема A', 'Тема B', 'Тема C'], [75, 85, 90]);
            }
        }

        // 3. Соотношение ответов
        const answersCtx = document.getElementById('answersChart');
        if (answersCtx) {
            try {
                new Chart(answersCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Правильные ответы', 'Неправильные ответы'],
                        datasets: [{
                            data: [@json($correctAnswers), @json($incorrectAnswers)],
                            backgroundColor: ['#1cc88a', '#e74a3b'],
                            hoverBackgroundColor: ['#17a673', '#be2617'],
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            } catch (e) {
                console.error('Answers chart error:', e);
                // Создаем простой график для теста
                createTestChart(answersCtx, 'doughnut', ['Правильные', 'Неправильные'], [70, 30]);
            }
        }

        // Функция для создания тестового графика
        function createTestChart(ctx, type, labels, data) {
            new Chart(ctx, {
                type: type,
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Тестовые данные',
                        data: data,
                        backgroundColor: type === 'doughnut' ? 
                            ['#1cc88a', '#e74a3b'] : '#36b9cc',
                        borderColor: '#4e73df'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                }
            });
        }
    });
</script>

<style>
    .chart-container {
        min-height: 300px;
        height: 400px;
        width: 100%;
    }
    .card-body {
        overflow-x: auto;
    }
</style>
@endsection