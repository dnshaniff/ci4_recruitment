<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->group('', ['filter' => 'guest'], function ($routes) {
    $routes->get('login', 'AuthController::index');

    $routes->post('login', 'AuthController::login');
});

$routes->group('', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'DashboardController::index');

    $routes->get('/logout', 'AuthController::logout');

    $routes->group('users', function ($routes) {
        $routes->get('/', 'UserController::index');

        $routes->get('datatable', 'UserController::datatable');

        $routes->post('store', 'UserController::store');

        $routes->get('(:num)', 'UserController::show/$1');

        $routes->post('update/(:num)', 'UserController::update/$1');

        $routes->delete('delete/(:num)', 'UserController::delete/$1');

        $routes->post('restore/(:num)', 'UserController::restore/$1');

        $routes->delete('force/(:num)', 'UserController::force/$1');
    });

    $routes->group('employees', function ($routes) {
        $routes->get('/', 'EmployeeController::index');

        $routes->get('datatable', 'EmployeeController::datatable');

        $routes->post('store', 'EmployeeController::store');

        $routes->get('(:num)', 'EmployeeController::show/$1');

        $routes->post('update/(:num)', 'EmployeeController::update/$1');

        $routes->delete('delete/(:num)', 'EmployeeController::delete/$1');

        $routes->post('restore/(:num)', 'EmployeeController::restore/$1');

        $routes->delete('force/(:num)', 'EmployeeController::force/$1');
    });
});
