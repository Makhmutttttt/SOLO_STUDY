<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Test extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'status', 'user_id', 'num_questions', 'difficulty_level', 'content', 'ready_test'];


    // Один тест принадлежит одному пользователю
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
