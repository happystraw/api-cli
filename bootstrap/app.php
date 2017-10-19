<?php

require __DIR__ . '/../vendor/autoload.php';

$app = new \App\Application(realpath(__DIR__ . '/../'));

// Init environment
$app->initEnv();

// Check environment
$app->check();

// Register Console
$app->singleton('console', \App\Consoles\Kernel::class);

// You can register other configuration or constant
// $app->make('config')->load('other_config');
// $app->make('config')->loadConst('other_const');

// Register language config of Console
$app->make('lang')->load('consoles');

return $app;

