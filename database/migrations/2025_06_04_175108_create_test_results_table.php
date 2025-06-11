<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
public function up()
{
    Schema::create('test_results', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('test_id')->constrained()->onDelete('cascade');
        $table->integer('score'); // Количество правильных ответов
        $table->integer('total_questions'); // Всего вопросов в тесте
        $table->timestamp('completed_at');
        $table->timestamps();
    });
}

    public function down(): void {
        Schema::dropIfExists('test_results');
    }
};
