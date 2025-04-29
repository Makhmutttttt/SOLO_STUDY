<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'password'];

    // Один пользователь может иметь много тестов
    public function tests(): HasMany
    {
        return $this->hasMany(Test::class);
    }
    public function mistakes()
    {
        return $this->hasMany(Mistake::class);
    }
}
