<?php namespace F9\Providers;

/**
 * @package Nine
 * @version 0.4.2
 * @author  Greg Truesdell <odd.greg@gmail.com>
 */

return [

    'files'       => 'OS::Folders',
    'views'       => [
        'home'    => 'HomeViewController',
        'contact' => 'ContactViewController',
    ],
    'controllers' => [
        'login'     => 'LoginHttpController',
        'dashboard' => 'DashboardHttpController',
    ],
    'title'       => 'Blade Test Template',
    'blade'       => [
        'cache'          => __DIR__ . '/cache',
        'template_paths' => [
            __DIR__ . '/templates',
        ],
    ],
    'providers' =>
        [
            'ErrorHandlingServiceProvider::class',
            'DebugServiceProvider::class',
            'ConfigServiceProvider::class',
            'CoreServicesProvider::class',
            'SessionServiceProvider::class',
            'DatabaseServiceProvider::class',
            'MiddlewareServiceProvider::class',
            'RoutingServiceProvider::class',
        ],
];
