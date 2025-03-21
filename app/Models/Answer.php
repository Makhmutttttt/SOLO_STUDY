<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;
    protected $fillable = ['test_attempt_id', 'question_id', 'selected_answer', 'is_correct'];

    public function attempt()
    {
        return $this->belongsTo(TestAttempt::class);
    }
}
