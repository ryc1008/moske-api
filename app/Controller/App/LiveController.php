<?php

declare(strict_types=1);

namespace App\Controller\App;

use App\Model\Live;
use Hyperf\HttpServer\Contract\RequestInterface;


class LiveController extends CommonController
{

    public function list(RequestInterface $request)
    {
        $tid = $request->query('tid');
        $params['status'] = [Live::STATUS_1, Live::STATUS_2];
        $params['tid'] = $tid;
        if($tid == 'good') {
            $params['tid'] = 0;
            $params['status'] = [Live::STATUS_2];
        }
        if($tid == 'focus') {
            $params['tid'] = 0;
            $params['id'] = [10000, 10002];
        }
        $fields = ['id', 'name', 'title', 'avatar', 'show', 'hits', 'work'];
        $list = Live::app($params, $fields);
        return $this->returnJson(0, $list);
    }


    public function info(RequestInterface $request)
    {
        $id = (int)$request->query('id', 0);
        $fields = ['id', 'name', 'avatar', 'target', 'time', 'hour'];
        $info = Live::info($id, $fields);
        //是否是VIP
        //视频当前播放时间(开播时间 + 当前时间)
        return $this->returnJson(0, $info, $id);
    }
}
