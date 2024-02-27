<?php
declare(strict_types=1);

use App\Middleware\AuthAdminMiddleware;
use Hyperf\HttpServer\Router\Router;

Router::addGroup('/admin',function (){
    Router::post('/login', 'App\Controller\Admin\LoginController@index');
    Router::addGroup('',function (){
        //系统管理
        Router::addGroup('',function (){
            //配置
            Router::addGroup('/setting', function () {
                Router::get('/config', 'App\Controller\Admin\SettingController@config');
                Router::get('/list', 'App\Controller\Admin\SettingController@list');
                Router::post('/update', 'App\Controller\Admin\SettingController@update');
                Router::post('/save', 'App\Controller\Admin\SettingController@save');
                Router::post('/destroy', 'App\Controller\Admin\SettingController@destroy');
            });
            //类目
            Router::addGroup('/type',function (){
                Router::get('/list', 'App\Controller\Admin\TypeController@list');
                Router::get('/config', 'App\Controller\Admin\TypeController@config');
                Router::get('/tree', 'App\Controller\Admin\TypeController@tree');
                Router::post('/update', 'App\Controller\Admin\TypeController@update');
                Router::post('/lock', 'App\Controller\Admin\TypeController@lock');
                Router::post('/active', 'App\Controller\Admin\TypeController@active');
                Router::post('/destroy', 'App\Controller\Admin\TypeController@destroy');
            });
            //标签
            Router::addGroup('/tag',function (){
                Router::get('/list', 'App\Controller\Admin\TagController@list');
                Router::post('/update', 'App\Controller\Admin\TagController@update');
                Router::post('/destroy', 'App\Controller\Admin\TagController@destroy');
            });
            //广告
            Router::addGroup('/advert',function (){
                Router::get('/list', 'App\Controller\Admin\AdvertController@list');
                Router::get('/config', 'App\Controller\Admin\AdvertController@config');
                Router::post('/update', 'App\Controller\Admin\AdvertController@update');
                Router::post('/lock', 'App\Controller\Admin\AdvertController@lock');
                Router::post('/active', 'App\Controller\Admin\AdvertController@active');
                Router::post('/destroy', 'App\Controller\Admin\AdvertController@destroy');
            });
        });
        //用户中心
        Router::addGroup('',function (){
            //用户
            Router::addGroup('/user',function (){
                Router::get('/config', 'App\Controller\Admin\UserController@config');
                Router::get('/list', 'App\Controller\Admin\UserController@list');
                Router::post('/update', 'App\Controller\Admin\UserController@update');
                Router::post('/lock', 'App\Controller\Admin\UserController@lock');
                Router::post('/active', 'App\Controller\Admin\UserController@active');
            });
            //会员
            Router::addGroup('/vip',function (){
                Router::get('/config', 'App\Controller\Admin\VipController@config');
                Router::get('/list', 'App\Controller\Admin\VipController@list');
                Router::post('/update', 'App\Controller\Admin\VipController@update');
                Router::post('/lock', 'App\Controller\Admin\VipController@lock');
                Router::post('/active', 'App\Controller\Admin\VipController@active');
                Router::post('/destroy', 'App\Controller\Admin\VipController@destroy');
            });
            //渠道
            Router::addGroup('/canal',function (){
                Router::get('/config', 'App\Controller\Admin\CanalController@config');
                Router::get('/list', 'App\Controller\Admin\CanalController@list');
                Router::get('/tree', 'App\Controller\Admin\CanalController@tree');
                Router::post('/update', 'App\Controller\Admin\CanalController@update');
                Router::post('/lock', 'App\Controller\Admin\CanalController@lock');
                Router::post('/active', 'App\Controller\Admin\CanalController@active');
                Router::post('/destroy', 'App\Controller\Admin\CanalController@destroy');
                Router::post('/login', 'App\Controller\Admin\CanalController@login');
            });

        });
        //数据报表
        Router::addGroup('',function (){
            //支付
            Router::addGroup('/payment',function (){
                Router::get('/config', 'App\Controller\Admin\PaymentController@config');
                Router::get('/list', 'App\Controller\Admin\PaymentController@list');
                Router::get('/tree', 'App\Controller\Admin\PaymentController@tree');
                Router::post('/update', 'App\Controller\Admin\PaymentController@update');
                Router::post('/lock', 'App\Controller\Admin\PaymentController@lock');
                Router::post('/active', 'App\Controller\Admin\PaymentController@active');
                Router::post('/destroy', 'App\Controller\Admin\PaymentController@destroy');
            });
        });
        //媒体管理
        Router::addGroup('',function (){
            //视频
            Router::addGroup('/video',function (){
                Router::get('/config', 'App\Controller\Admin\VideoController@config');
                Router::get('/list', 'App\Controller\Admin\VideoController@list');
                Router::post('/update', 'App\Controller\Admin\VideoController@update');
                Router::post('/lock', 'App\Controller\Admin\VideoController@lock');
                Router::post('/active', 'App\Controller\Admin\VideoController@active');
                Router::post('/good', 'App\Controller\Admin\VideoController@good');
                Router::post('/destroy', 'App\Controller\Admin\VideoController@destroy');
            });
            //短剧
            Router::addGroup('/playlet',function (){
                Router::get('/config', 'App\Controller\Admin\PlayletController@config');
                Router::get('/list', 'App\Controller\Admin\PlayletController@list');
                Router::post('/update', 'App\Controller\Admin\PlayletController@update');
                Router::post('/lock', 'App\Controller\Admin\PlayletController@lock');
                Router::post('/active', 'App\Controller\Admin\PlayletController@active');
                Router::post('/good', 'App\Controller\Admin\PlayletController@good');
                Router::post('/destroy', 'App\Controller\Admin\PlayletController@destroy');
            });
            //直播
            Router::addGroup('/live',function (){
                Router::get('/config', 'App\Controller\Admin\LiveController@config');
                Router::get('/list', 'App\Controller\Admin\LiveController@list');
                Router::post('/update', 'App\Controller\Admin\LiveController@update');
                Router::post('/lock', 'App\Controller\Admin\LiveController@lock');
                Router::post('/active', 'App\Controller\Admin\LiveController@active');
                Router::post('/good', 'App\Controller\Admin\LiveController@good');
                Router::post('/destroy', 'App\Controller\Admin\LiveController@destroy');
            });
            //有声小说
            Router::addGroup('/sound',function (){
                Router::get('/config', 'App\Controller\Admin\SoundController@config');
                Router::get('/list', 'App\Controller\Admin\SoundController@list');
                Router::get('/chapter', 'App\Controller\Admin\SoundController@chapter');
                Router::post('/save', 'App\Controller\Admin\SoundController@save');
                Router::post('/update', 'App\Controller\Admin\SoundController@update');
                Router::post('/lock', 'App\Controller\Admin\SoundController@lock');
                Router::post('/active', 'App\Controller\Admin\SoundController@active');
                Router::post('/destroy', 'App\Controller\Admin\SoundController@destroy');
                Router::post('/good', 'App\Controller\Admin\SoundController@good');
                Router::post('/delete', 'App\Controller\Admin\SoundController@delete');
            });
        });
        //内容管理
        Router::addGroup('',function (){
            //短篇小说
            Router::addGroup('/story',function (){
                Router::get('/config', 'App\Controller\Admin\StoryController@config');
                Router::get('/list', 'App\Controller\Admin\StoryController@list');
                Router::post('/update', 'App\Controller\Admin\StoryController@update');
                Router::post('/lock', 'App\Controller\Admin\StoryController@lock');
                Router::post('/active', 'App\Controller\Admin\StoryController@active');
                Router::post('/destroy', 'App\Controller\Admin\StoryController@destroy');
            });
            //长篇小说及章节
            Router::addGroup('/novel',function (){
                Router::get('/config', 'App\Controller\Admin\NovelController@config');
                Router::get('/list', 'App\Controller\Admin\NovelController@list');
                Router::get('/chapter', 'App\Controller\Admin\NovelController@chapter');
                Router::post('/save', 'App\Controller\Admin\NovelController@save');
                Router::post('/update', 'App\Controller\Admin\NovelController@update');
                Router::post('/lock', 'App\Controller\Admin\NovelController@lock');
                Router::post('/active', 'App\Controller\Admin\NovelController@active');
                Router::post('/destroy', 'App\Controller\Admin\NovelController@destroy');
                Router::post('/delete', 'App\Controller\Admin\NovelController@delete');
            });
            //漫画及章节
            Router::addGroup('/comic',function (){
                Router::get('/config', 'App\Controller\Admin\ComicController@config');
                Router::get('/list', 'App\Controller\Admin\ComicController@list');
                Router::get('/chapter', 'App\Controller\Admin\ComicController@chapter');
                Router::post('/save', 'App\Controller\Admin\ComicController@save');
                Router::post('/update', 'App\Controller\Admin\ComicController@update');
                Router::post('/lock', 'App\Controller\Admin\ComicController@lock');
                Router::post('/active', 'App\Controller\Admin\ComicController@active');
                Router::post('/destroy', 'App\Controller\Admin\ComicController@destroy');
                Router::post('/delete', 'App\Controller\Admin\ComicController@delete');
            });
            //图片
            Router::addGroup('/photo',function (){
                Router::get('/config', 'App\Controller\Admin\PhotoController@config');
                Router::get('/list', 'App\Controller\Admin\PhotoController@list');
                Router::post('/update', 'App\Controller\Admin\PhotoController@update');
                Router::post('/lock', 'App\Controller\Admin\PhotoController@lock');
                Router::post('/active', 'App\Controller\Admin\PhotoController@active');
                Router::post('/destroy', 'App\Controller\Admin\PhotoController@destroy');
            });
            //楼凤
            Router::addGroup('/lady',function (){
                Router::get('/config', 'App\Controller\Admin\LadyController@config');
                Router::get('/list', 'App\Controller\Admin\LadyController@list');
                Router::post('/update', 'App\Controller\Admin\LadyController@update');
                Router::post('/lock', 'App\Controller\Admin\LadyController@lock');
                Router::post('/active', 'App\Controller\Admin\LadyController@active');
                Router::post('/good', 'App\Controller\Admin\LadyController@good');
                Router::post('/destroy', 'App\Controller\Admin\LadyController@destroy');
            });
        });
        //游戏中心
        Router::addGroup('',function (){
            //游戏
            Router::addGroup('/game',function (){
                Router::get('/list', 'App\Controller\Admin\GameController@list');
                Router::get('/config', 'App\Controller\Admin\GameController@config');
                Router::post('/update', 'App\Controller\Admin\GameController@update');
                Router::post('/lock', 'App\Controller\Admin\GameController@lock');
                Router::post('/active', 'App\Controller\Admin\GameController@active');
                Router::post('/destroy', 'App\Controller\Admin\GameController@destroy');
            });
        });
        //权限管理
        Router::addGroup('',function (){
            //管理员
            Router::addGroup('/manager',function (){
                Router::get('/list', 'App\Controller\Admin\ManagerController@list');
                Router::get('/user', 'App\Controller\Admin\ManagerController@user');
                Router::get('/config', 'App\Controller\Admin\ManagerController@config');
                Router::post('/update', 'App\Controller\Admin\ManagerController@update');
                Router::post('/lock', 'App\Controller\Admin\ManagerController@lock');
                Router::post('/active', 'App\Controller\Admin\ManagerController@active');
                Router::post('/destroy', 'App\Controller\Admin\ManagerController@destroy');
            });
            //角色
            Router::addGroup('/role',function (){
                Router::get('/list', 'App\Controller\Admin\RoleController@list');
                Router::get('/tree', 'App\Controller\Admin\RoleController@tree');
                Router::get('/access', 'App\Controller\Admin\RoleController@access');
                Router::post('/update', 'App\Controller\Admin\RoleController@update');
                Router::post('/destroy', 'App\Controller\Admin\RoleController@destroy');
            });
            //权限
            Router::addGroup('/authen',function (){
                Router::get('/list', 'App\Controller\Admin\AuthenController@list');
                Router::get('/tree', 'App\Controller\Admin\AuthenController@tree');
                Router::post('/update', 'App\Controller\Admin\AuthenController@update');
                Router::post('/destroy', 'App\Controller\Admin\AuthenController@destroy');
            });
            //系统日志
            Router::addGroup('/logger',function (){
                Router::get('/list', 'App\Controller\Admin\LoggerController@list');
                Router::get('/config', 'App\Controller\Admin\LoggerController@config');
                Router::post('/destroy', 'App\Controller\Admin\LoggerController@destroy');
            });
            //登录日志
            Router::addGroup('/safe',function (){
                Router::get('/list', 'App\Controller\Admin\SafeController@list');
                Router::get('/config', 'App\Controller\Admin\SafeController@config');
                Router::post('/destroy', 'App\Controller\Admin\SafeController@destroy');
            });
        });
    },['middleware' => [AuthAdminMiddleware::class]]);
});