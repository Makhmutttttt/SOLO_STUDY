<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::table('tests', function (Blueprint $table) {
            $table->text('difficulty_level')->after('status')->nullable(); // Добавляем колонку
        });
    }

    public function down()
    {
        Schema::table('tests', function (Blueprint $table) {
            $table->dropColumn(columns: 'difficulty_level'); // Удаляем колонку при откате
        });
    }
};
