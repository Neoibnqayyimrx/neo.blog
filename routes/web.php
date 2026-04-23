<?php

declare(strict_types=1);

use App\Core\Router;

/**
 * Route table.
 *
 * All application URLs live here. Each entry maps an HTTP verb + path to a
 * "Controller@method" string. Controllers live under App\Controllers\*.
 */

/** @var Router $router */

// ── Public site ───────────────────────────────────────────────────────────────
$router->get('/',         'HomeController@index');
$router->get('/blog',     'BlogController@index');
$router->get('/post',     'PostController@show');
$router->get('/category', 'CategoryController@show');
$router->get('/search',   'SearchController@index');

$router->get('/about',    'PageController@about');
$router->get('/services', 'PageController@services');

$router->get('/contact',  'ContactController@show');
$router->post('/contact', 'ContactController@send');

// ── Auth ─────────────────────────────────────────────────────────────────────
$router->get('/signin',  'AuthController@signinForm');
$router->post('/signin', 'AuthController@signin');
$router->get('/signup',  'AuthController@signupForm');
$router->post('/signup', 'AuthController@signup');
$router->any('/logout',  'AuthController@logout');

// ── Admin ────────────────────────────────────────────────────────────────────
$router->group('/admin', function (Router $r): void {
    $r->get('',  'Admin\DashboardController@index');
    $r->get('/', 'Admin\DashboardController@index');

    // Posts
    $r->get('/posts',          'Admin\PostController@index');
    $r->get('/posts/create',   'Admin\PostController@create');
    $r->post('/posts/store',   'Admin\PostController@store');
    $r->get('/posts/edit',     'Admin\PostController@edit');
    $r->post('/posts/update',  'Admin\PostController@update');
    $r->post('/posts/delete',  'Admin\PostController@delete');

    // Categories
    $r->get('/categories',          'Admin\CategoryController@index');
    $r->post('/categories/store',   'Admin\CategoryController@store');
    $r->post('/categories/update',  'Admin\CategoryController@update');
    $r->post('/categories/delete',  'Admin\CategoryController@delete');

    // Users
    $r->get('/users',              'Admin\UserController@index');
    $r->get('/users/edit',         'Admin\UserController@edit');
    $r->post('/users/update',      'Admin\UserController@update');
    $r->post('/users/toggle-admin','Admin\UserController@toggleAdmin');
    $r->post('/users/delete',      'Admin\UserController@delete');
});
