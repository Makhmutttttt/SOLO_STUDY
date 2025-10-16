<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Http\Request;

class QueueController extends Controller
{
    // public function start()
    // {
    //     $scriptPath = base_path('artisan');
    //     $command = "nohup /usr/local/bin/php8.2 {$scriptPath} queue:work --once --queue=ai,default --timeout=400 --tries=3 > /dev/null 2>&1 &";
        
    //     // Проверяем доступность функции
    //     if (function_exists('shell_exec')) {
    //         shell_exec($command);
    //         return back()->with('success', 'Фоновая обработка запущена!');
    //     }
        
    //     return back()->with('error', 'Запуск невозможен: функция shell_exec отключена');
    // }


    public function start()
    {
        // Помещаем задачу в очередь для фонового выполнения
        dispatch(function () {
            Artisan::call('queue:work', [
                '--once' => true,
                '--queue' => 'ai,default',
                '--timeout' => 400,
                '--tries' => 3
            ]);
        })->onQueue('ai'); // Важно: другая очередь!

        return back()->with('success', 'Команда поставлена в очередь!');
    }
}
