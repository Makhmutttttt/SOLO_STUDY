<?php

namespace App\Http\Controllers;

use App\Models\TestAttempt;
use Illuminate\Http\Request;

class TestAttemptController extends Controller
{
    public function store(Request $request)
    {
        return TestAttempt::create($request->all());
    }
}
