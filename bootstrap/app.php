<?php

require __DIR__ . '/../vendor/autoload.php';

$app = new \App\Application(realpath(__DIR__ . '/../'));

// 初始化环境
$app->initEnv();

// 检查环境
$app->check();

// 注册Console
$app->singleton('console', \App\Consoles\Kernel::class);

// 注册Console语言参数
$app->make('lang')->load('consoles');

return $app;

