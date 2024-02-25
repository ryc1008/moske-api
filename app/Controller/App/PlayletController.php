<?php

declare(strict_types=1);

namespace App\Controller\App;

use App\Job\IncJob;
use App\Job\RegisterJob;
use App\Model\Playlet;
use App\Model\UserBuy;
use App\Model\UserFavor;
use App\Model\UserPraise;
use App\Service\QueueService;
use Hyperf\DbConnection\Db;
use Hyperf\HttpServer\Contract\RequestInterface;


class PlayletController extends CommonController
{

    protected $m = 'playlet';

    public function list(RequestInterface $request)
    {
        $data = $request->all();
        $params['page'] = $data['page'] ?? 1;
        if($data['tab'] == 'focus'){
            $params['type_id'] = [10040, 10041];
            $params['status'] = [Playlet::STATUS_1, Playlet::STATUS_2];
        }else{
            $params['status'] = [Playlet::STATUS_2];
        }
        $fields = ['id', 'title', 'thumb', 'target', 'type_id', 'time', 'tag', 'money', 'show', 'hits'];
        $list = Playlet::app($params, $fields, 5);

        //必须是VIP，免费才能看
        $user = $this->user();
        $model = $this->model($this->m);
        foreach ($list->items() as &$item){
            $user['is_buy'] = 0;
            if($item['money'] > 0){
                //必须是购买，钻石才能看
                $buy = $this->isBuy($user['id'], $item['id'], $model);
                if($buy){
                    $user['is_buy'] = 1;
                }
            }
            $favor = $this->isFavor($user['id'], $item['id'], $model);
            $user['is_favor'] = $favor ? 1 : 0;
            $praise = $this->isPraise($user['id'], $item['id'], $model);
            $user['is_praise'] = $praise ? 1 : 0;
            $item['user'] = $user;
            $item['guid'] = uuid();
            $item['state'] = 'pause';
            $item['playing'] = false;
            //这个写到进程中去吧，太慢了: 更新自身show值
            QueueService::push(new IncJob(['id' => $item['id'], 'model' => $this->m]));
        }
        return $this->returnJson(0, $list);
    }



    public function praise(RequestInterface $request)
    {
        $id = (int)$request->post('id', 0);
        $user = $this->user();
        $userid = $user['id'];
        if(!$userid){
            return $this->returnJson(1, null, '未登录');
        }
        $info = Playlet::where('status','<>', Playlet::STATUS_3)->find($id);
        //数据是否存在
        if(!$info){
            return $this->returnJson(1, null, '数据不存在');
        }
        $model = $this->model($this->m);
        //是否已经点过赞
        $praise = UserPraise::where('user_id', $userid)
            ->where('good_id', $id)
            ->where('model', $model)->first();
        if($praise){
            return $this->returnJson(1, null, '已点赞');
        }
        $insert = [
            'user_id' => $userid,
            'good_id' => $id,
            'model' => $model
        ];
        Db::transaction(function () use ($insert, $info){
            UserPraise::create($insert);
            $info->increment('hits');
        });
        return $this->returnJson();
    }

    public function favor(RequestInterface $request)
    {
        $id = (int)$request->post('id', 0);
        $user = $this->user();
        $userid = $user['id'];
        if(!$userid){
            return $this->returnJson(1, null, '未登录');
        }
        $info = Playlet::where('status','<>', Playlet::STATUS_3)->find($id);
        //数据是否存在
        if(!$info){
            return $this->returnJson(1, null, '数据不存在');
        }
        $model = $this->model($this->m);
        //是否已经收藏
        $praise = UserFavor::where('user_id', $userid)
            ->where('good_id', $id)
            ->where('model', $model)->first();
        if($praise){
            return $this->returnJson(1, null, '已收藏');
        }
        $insert = [
            'user_id' => $userid,
            'good_id' => $id,
            'model' => $model
        ];
        UserFavor::create($insert);
        return $this->returnJson();
    }

    public function focus(RequestInterface $request)
    {
        $id = (int)$request->query('id', 0);
        $fields = ['id', 'name', 'avatar', 'target', 'time', 'hour'];
        $info = Playlet::info($id, $fields);
        //是否是VIP
        //视频当前播放时间(开播时间 + 当前时间)
        return $this->returnJson(0, $info, $id);
    }
}

