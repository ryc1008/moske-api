<?php

declare(strict_types=1);

namespace App\Controller\App;

use App\Model\Playlet;
use App\Model\UserBuy;
use App\Model\UserFavor;
use App\Model\UserPraise;
use Hyperf\Collection\Collection;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Paginator\Paginator;


class PlayletController extends CommonController
{

    public function list(RequestInterface $request)
    {
        $data = $request->all();
        $params['page'] = $data['page'] ?? 1;
        if($data['tab'] == 'focus'){
            $params['type_id'] = [10039, 10041];
            $params['status'] = [Playlet::STATUS_1, Playlet::STATUS_2];
        }else{
            $params['status'] = [Playlet::STATUS_2];
        }
        $fields = ['id', 'title', 'thumb', 'target', 'type_id', 'time', 'tag', 'money', 'show', 'hits'];
        $list = Playlet::app($params, $fields, 5);

//        //必须是VIP，免费才能看
//        $user = $this->user();
//        foreach ($list->items() as &$item){
//            $user['is_buy'] = 0;
//            if($item['money'] > 0){
//                //必须是购买，钻石才能看
//                $buy = $this->isBuy($user['id'], $item['id'], 'playlet');
//                if($buy){
//                    $user['is_buy'] = 1;
//                }
//            }
//            $favor = $this->isFavor($user['id'], $item['id'], 'playlet');
//            $user['is_favor'] = $favor ? 1 : 0;
//            $praise = $this->isPraise($user['id'], $item['id'], 'playlet');
//            $user['is_praise'] = $praise ? 1 : 0;
//            $item['user'] = $user;
//            $item['guid'] = uuid();
//            $item['state'] = 'pause';
//            $item['playing'] = false;
//            //更新自身show值
//            Playlet::matic('show', $item['id']);
//        }
        
        return $this->returnJson(0, $list);
    }






    public function info(RequestInterface $request)
    {
        $id = (int)$request->query('id', 0);
        $fields = ['id', 'name', 'avatar', 'target', 'time', 'hour'];
        $info = Playlet::info($id, $fields);
        //是否是VIP
        //视频当前播放时间(开播时间 + 当前时间)
        return $this->returnJson(0, $info, $id);
    }
}

