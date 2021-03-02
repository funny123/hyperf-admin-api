<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
use Hyperf\HttpServer\Router\Router;

Router::addRoute(['GET', 'POST', 'HEAD'], '/', 'App\Controller\IndexController@index');

Router::get('/favicon.ico', function () {
    return '';
});

Router::post('/user/login', 'App\Controller\Auth\LoginController@login');
Router::post('/user/register', 'App\Controller\Auth\RegisterController@register');
//个人资料
Router::addGroup('/user/', function () {
    Router::get('info','App\Controller\UserController@info');
    Router::post('logout', 'App\Controller\UserController@logout');
    Router::get('elasticsearch', 'App\Controller\UserController@elasticsearch');
}, [
    'middleware' => [App\Middleware\JwtAuthMiddleware::class]
]);
