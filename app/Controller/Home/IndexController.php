<?php

declare(strict_types=1);

namespace App\Controller\Home;

use App\Controller\BaseController;


class IndexController extends BaseController
{

    public function index()
    {
        //ab -c 10 -n 1000 http://denghong.go/
        //定时清理长时间不登陆的用户数据
        //定时清理无效的日志数据
    }
}
