<?php

declare(strict_types=1);

use Crmleaf\Payroll\Tools\PfCalculator\Http\Controllers\PfCalculatorController;
use Illuminate\Support\Facades\Route;

/*
 * Loaded by PfCalculatorServiceProvider only when config('pf-calculator.route.enabled')
 * is true, so requiring the package never adds a URL on its own.
 */

/** @var \Illuminate\Contracts\Config\Repository $config */
$config = app('config');

Route::middleware((array) $config->get('pf-calculator.route.middleware', ['web']))
    ->prefix((string) $config->get('pf-calculator.route.prefix', 'tools'))
    ->group(static function () use ($config): void {
        Route::match(['get', 'post'], '/pf-calculator', PfCalculatorController::class)
            ->name((string) $config->get('pf-calculator.route.name', 'pf-calculator'));
    });
