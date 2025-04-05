<?php

// Автозагрузка классов
spl_autoload_register(function ($className) {
    $classFile = __DIR__ . '/services/' . $className . '.php';
    if (file_exists($classFile)) {
        require_once $classFile;
    }
});

// Загрузка конфигурации
$config = require_once 'config.php';

// Запуск приложения
$app = new Application($config);
$app->run();

echo "Скрипт выполнен. Проверьте лог-файл в папке {$config['log_dir']} \n";