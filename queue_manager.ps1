# Установка UTF-8 для корректного вывода
[Console]::OutputEncoding = [System.Text.UTF8Encoding]::new()

# Конфигурация
$Workers = 4
$LogsDir = ".\queue_logs"
$PhpPath = "C:\OSPanel_new\modules\php\PHP_8.1\php.exe"
$ArtisanPath = ".\artisan"
$QueueCommand = "queue:work"

# Создание директории логов
if (!(Test-Path $LogsDir)) {
    New-Item -ItemType Directory -Path $LogsDir -Force | Out-Null
}


function Start-Workers {
    Write-Host "Запуск $Workers воркеров..."

    for ($i = 1; $i -le $Workers; $i++) {
        $logFile = Join-Path $LogsDir "worker_$i.log"
        # Экранируем кавычки для cmd.exe и формируем команду
        $command = """$PhpPath"" ""$ArtisanPath"" $QueueCommand *> ""$logFile"" 2>&1"

        # Запускаем через cmd /c
        Start-Process -FilePath "cmd.exe" -ArgumentList "/c $command" -NoNewWindow -WorkingDirectory $PWD
    }

    Write-Host "Готово! Логи в $LogsDir"
}


function Stop-Workers {
    Write-Host "Остановка воркеров..."
    Get-Process php | Where-Object { 
        $_.CommandLine -like "*$ArtisanPath $QueueCommand*" 
    } | Stop-Process -Force
    Write-Host "Все процессы остановлены"
}

# Обработка аргументов
switch ($args[0]) {
    "start"  { Start-Workers }
    "stop"   { Stop-Workers }
    default  {
        Write-Host "Использование:"
        Write-Host "  .\queue_manager.ps1 start  - Запустить очередь"
        Write-Host "  .\queue_manager.ps1 stop   - Остановить очередь"
    }
}