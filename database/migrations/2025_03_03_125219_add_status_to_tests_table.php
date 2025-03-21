<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('tests', function (Blueprint $table) {
            $table->integer('num_questions')->default(10)->after('title'); // Сначала добавляем num_questions
            $table->string('status')->default('pending')->after('num_questions'); // Затем status
        });
    }

    public function down() {
        Schema::table('tests', function (Blueprint $table) {
            $table->dropColumn(['status', 'num_questions']); // Удаляем обе колонки
        });
    }
};
