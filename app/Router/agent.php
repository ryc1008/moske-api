<?php
declare(strict_types=1);

use App\Middleware\AuthAgentMiddleware;
use Hyperf\HttpServer\Router\Router;


Router::addGroup('/agent',function (){
    Router::post('/login', 'App\Controller\Agent\LoginController@index');
    Router::post('/authorize', 'App\Controller\Agent\LoginController@authorize');

    Router::addGroup('',function (){
        Router::addGroup('/user',function () {
            Router::get('', 'App\Controller\Agent\UserController@index');
            Router::post('/update', 'App\Controller\Agent\UserController@update');
            Router::post('/password', 'App\Controller\Agent\UserController@password');
        });
    },['middleware' => [AuthAgentMiddleware::class]]);

});