<?php

// app/Models/TestResult.php

// app/Models/TestResult.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'test_id',
        'score',
        'total_questions',
        'completed_at'
    ];

    // Добавляем преобразование атрибутов
    protected $casts = [
        'completed_at' => 'datetime'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function test()
    {
        return $this->belongsTo(Test::class);
    }
}