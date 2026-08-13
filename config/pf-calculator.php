<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Route
    |--------------------------------------------------------------------------
    |
    | Off by default. Installing a package should not add a public URL to your
    | application without you asking for one. Turn it on here, or with
    | PF_CALCULATOR_ROUTE=true, and the tool mounts at
    | /<prefix>/pf-calculator answering both GET and POST.
    |
    */

    'route' => [
        'enabled' => env('PF_CALCULATOR_ROUTE', false),
        'prefix' => env('PF_CALCULATOR_PREFIX', 'tools'),
        'name' => 'pf-calculator',
        // Throttled by default. This route is public when enabled: the form
        // request authorises every caller, and a PDF request runs a full Dompdf
        // render, which is orders of magnitude more expensive than the
        // arithmetic behind it. Thirty a minute per IP is generous for a human
        // filling in a form and useless to anyone trying to exhaust the box.
        // Raise it behind authentication if you have some.
        'middleware' => ['web', 'throttle:30,1'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Presentation
    |--------------------------------------------------------------------------
    */

    'view' => [
        'title' => 'PF Calculator',
        'tagline' => 'EPF employee, employer and EPS shares with the wage ceiling applied.',
    ],

    'assets' => [
        // This tool needs no server, so the browser build is loaded by default.
        'script' => true,
        // Optional CDN for the browser build. When set, the view loads it
        // ahead of the asset published into your own public directory. When
        // null, the published asset is used on its own and the page makes no
        // third-party request at all - which is the better default for a page
        // that handles salary figures.
        //
        // A hosted build is coming soon. To use it instead, set this to:
        // https://cdn.jsdelivr.net/npm/@crmleaf/payroll-js@<major>/dist/payroll.min.js
        'cdn' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Defaults
    |--------------------------------------------------------------------------
    |
    | Pre-fill values for the form and the Blade component. These are
    | presentation only - they are never passed to the calculator, so changing
    | one cannot change a statutory answer.
    |
    */

    'defaults' => [
        'basic_salary' => 30000,
        'employer_restricts_to_ceiling' => true,
        'eps_eligible' => true,
        'reduced_rate' => false,
        'include_admin_charges' => true,
    ],

];
