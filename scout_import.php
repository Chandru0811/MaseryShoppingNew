<?php

// Include the Composer autoloader
require __DIR__ . '/vendor/autoload.php';

// Boot the Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';

// Set the application instance
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// Run the artisan command
$status = $kernel->call('scout:import', [
    'model' => 'App\Models\Inventory',
]);

// Output the result
echo $status ? 'Import successful' : 'Import failed';
