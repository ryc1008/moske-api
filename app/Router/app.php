<?php
declare(strict_types=1);

use App\Middleware\AuthAgentMiddleware;
use Hyperf\HttpServer\Router\Router;


Router::addGroup('/app',function (){
    //登录注册
    Router::addGroup('/auth',function (){
        Router::post('/login', 'App\Controller\App\AuthController@login');
        Router::post('/register', 'App\Controller\App\AuthController@register');
        Router::get('/test', 'App\Controller\App\AuthController@test');
    });

    //广告
    Router::get('/advert', 'App\Controller\App\AdvertController@index');

    //类目
    Router::get('/type', 'App\Controller\App\TypeController@index');

    //短剧
    Router::addGroup('/playlet',function (){
        Router::get('/list', 'App\Controller\App\PlayletController@list');
        Router::post('/praise', 'App\Controller\App\PlayletController@praise');
        Router::post('/favor', 'App\Controller\App\PlayletController@favor');
        Router::post('/follow', 'App\Controller\App\PlayletController@follow');
    });

    //直播
    Router::addGroup('/live',function (){
        Router::get('/list', 'App\Controller\App\LiveController@list');
        Router::get('/info', 'App\Controller\App\LiveController@info');
    });




//    Router::addGroup('',function (){
//        Router::addGroup('/user',function () {
//            Router::get('', 'App\Controller\Agent\UserController@index');
//            Router::post('/update', 'App\Controller\Agent\UserController@update');
//            Router::post('/password', 'App\Controller\Agent\UserController@password');
//        });
//    },['middleware' => [AuthAgentMiddleware::class]]);

});