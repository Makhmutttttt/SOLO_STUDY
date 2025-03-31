@extends('layouts.app')
{{-- create.balade --}}
@section('content')
<div class="container py-4">
    <div class="card shadow-lg p-4">
        <h2 class="text-center mb-4">Создать тест</h2>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form id="test-form">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-bold">Предмет</label>
                <input type="text" name="subject" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Тема</label>
                <input type="text" name="topic" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Сложность</label>
                <select name="difficult_level" class="form-control">
                    <option value="easy">Легкий</option>
                    <option value="normal">Средний</option>
                    <option value="difficult">Сложный</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Количество вопросов</label>
                <input type="number" name="num_questions" class="form-control" min="1" max="50" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Создать тест</button>
        </form>

        <div id="test-status" class="alert alert-info mt-3 text-center" style="display: none;">
            ⏳ Ожидание ответа от AI...
        </div>
    </div>

    <!-- Секция для теста и вопросов -->
    <div id="test-container" style="display: none;" class="card shadow-lg p-4 mt-4"></div>
</div>

<script>
document.getElementById('test-form').addEventListener('submit', function(event) {
    event.preventDefault();

    document.getElementById('test-status').style.display = 'block';

    fetch("{{ route('tests.generate') }}", {
        method: 'POST',
        body: new FormData(this)
    })
    .then(response => response.json())
    .then(data => {
        if (data.test_id) {
            let interval = setInterval(() => {
                fetch(`/tests/status/${data.test_id}`, { cache: "no-store" })
                .then(res => res.json())
                .then(response => {
                    if (response.status === 'Ready') {
                        clearInterval(interval);
                        document.getElementById('test-status').style.display = 'none';

                        // ✅ Вызываем функцию для рендеринга вопросов без обновления страницы
                        renderTest(response.test, response.questions);
                    }
                });
            }, 5000);
        }
    });
});



function renderTest(test, questions) {
    console.log("Полученные вопросы:", questions); // Проверяем данные в консоли

    let container = document.getElementById('test-container');
    container.innerHTML = `
        <h3 class="text-primary text-center">📘 ${test.title}</h3>
        <p class="text-muted text-center">${test.description}</p>
        ${questions.map((question, index) => {
            try {
                let options = JSON.parse(question.options); // Парсим варианты
                return `
                    <div class="card mt-3 p-3">
                        <strong class="fs-5">${index + 1}. ${question.question || question.question_text}</strong>
                        <ul class="list-group mt-2">
                            ${options.map(option => `<li class="list-group-item">${option}</li>`).join('')}
                        </ul>
                        <p class="mt-2 text-success fw-bold">✅ Правильный ответ: ${question.correct || question.correct_answer}</p>
                        <p class="mt-2 text-info"><strong>ℹ️ Объяснение:</strong> ${question.explanation || "Нет объяснения"}</p>
                    </div>
                `;
            } catch (e) {
                console.error("Ошибка парсинга JSON вариантов ответа:", e, question);
                return `<p class="text-danger">Ошибка загрузки вопроса</p>`;
            }
        }).join('')}
    `;
    container.style.display = 'block';
}


</script>
@endsection
