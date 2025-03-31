<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = ['test_id', 'question_text', 'options', 'correct_answer', 'explanation'];

    protected $casts = [
        'options' => 'array', // Автоматическое преобразование JSON в массив
    ];

    public function test()
    {
        return $this->belongsTo(Test::class);
    }
}
