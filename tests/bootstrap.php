<?php

declare(strict_types=1);

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Creating an application instance to allow the usage of facades, helpers,
 * and aliases before starting tests, including in data providers.
 */
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

# Ensuring the usage of the test database is mandatory.
$currentDbconn = config('database.default');

if ($currentDbconn !== 'test-db') {
    throw new Exception(sprintf("The database %s is not test-db", $currentDbconn)); // @phpcs:ignore
}

# Migrations
$console = app(ConsoleOutput::class);
$result = Artisan::call('migrate:fresh', ['--seed' => true], $console);

if ($result !== 0) {
    throw new Exception('Migrations failed');
}
