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
    Router::addGroup('/type',function (){
        Router::get('/list', 'App\Controller\App\TypeController@list');
        Router::get('/forum', 'App\Controller\App\TypeController@forum');
    });

    //楼凤
    Router::addGroup('/lady',function (){
        Router::get('/list', 'App\Controller\App\LadyController@list');
        Router::get('/info', 'App\Controller\App\LadyController@info');
        Router::post('/favor', 'App\Controller\App\LadyController@favor');
        Router::post('/buy', 'App\Controller\App\LadyController@buy');
    });

    //小说
    Router::addGroup('/story',function (){
        Router::get('/home', 'App\Controller\App\StoryController@home');
        Router::get('/list', 'App\Controller\App\StoryController@list');
        Router::get('/info', 'App\Controller\App\StoryController@info');
        Router::post('/favor', 'App\Controller\App\StoryController@favor');
    });

    //套图
    Router::addGroup('/photo',function (){
        Router::get('/home', 'App\Controller\App\PhotoController@home');
        Router::get('/list', 'App\Controller\App\PhotoController@list');
        Router::get('/info', 'App\Controller\App\PhotoController@info');
        Router::post('/favor', 'App\Controller\App\PhotoController@favor');
    });


    //短剧
    Router::addGroup('/playlet',function (){
        Router::get('/list', 'App\Controller\App\PlayletController@list');
        Router::post('/praise', 'App\Controller\App\PlayletController@praise');
        Router::post('/favor', 'App\Controller\App\PlayletController@favor');
        Router::post('/follow', 'App\Controller\App\PlayletController@follow');
        Router::post('/buy', 'App\Controller\App\PlayletController@buy');
    });

    //直播
    Router::addGroup('/live',function (){
        Router::get('/list', 'App\Controller\App\LiveController@list');
        Router::get('/info', 'App\Controller\App\LiveController@info');
        Router::post('/praise', 'App\Controller\App\LiveController@praise');
        Router::post('/follow', 'App\Controller\App\LiveController@follow');
        Router::post('/work', 'App\Controller\App\LiveController@work');
    });

    //游戏
    Router::addGroup('/game',function (){
        Router::get('/list', 'App\Controller\App\GameController@list');
        Router::post('/login', 'App\Controller\App\GameController@login');
    });

    //VIP
    Router::addGroup('/vip',function (){
        Router::get('/config', 'App\Controller\App\VipController@config');
        Router::get('/wallet', 'App\Controller\App\VipController@wallet');
    });

    //滚动消息
    Router::addGroup('/notice',function (){
        Router::get('/main', 'App\Controller\App\NoticeController@main');
        Router::get('/game', 'App\Controller\App\NoticeController@game');
    });


//    Router::addGroup('',function (){
//        Router::addGroup('/user',function () {
//            Router::get('', 'App\Controller\Agent\UserController@index');
//            Router::post('/update', 'App\Controller\Agent\UserController@update');
//            Router::post('/password', 'App\Controller\Agent\UserController@password');
//        });
//    },['middleware' => [AuthAgentMiddleware::class]]);

});