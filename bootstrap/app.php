<?php

require __DIR__ . '/../vendor/autoload.php';

$app = new \App\Application(realpath(__DIR__ . '/../'));

// Init environment
$app->initEnv();

// Check environment
$app->check();

// Register Console
$app->singleton('console', \App\Consoles\Kernel::class);

// Register language config of Console
$app->make('lang')->load('consoles');

return $app;

