<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->get('auth', 'Auth::index');
$routes->post('auth/auth_login', 'Auth::auth_login');
$routes->get('auth/auth_logout', 'Auth::auth_logout');

// Staff dashboard
$routes->get('staff/dashboard', 'Staff\Dashboard::index');
// Admin dashboard
$routes->get('admin/dashboard', 'Dashboard::index');
