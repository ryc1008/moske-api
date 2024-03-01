<?php

declare(strict_types=1);

namespace App\Controller\App;

use App\Job\IncJob;
use App\Model\Game;
use App\Model\Live;
use App\Model\UserFollow;
use App\Model\UserPraise;
use App\Service\QueueService;
use Hyperf\HttpServer\Contract\RequestInterface;


class LiveController extends CommonController
{
    protected $m = 'live';

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
        $fields = ['id', 'name', 'title', 'avatar', 'target', 'time', 'hour', 'show', 'hits', 'work'];
        $info = Live::info($id, $fields);
        if(!$info){
            return $this->returnJson(1, null, '数据不存在');
        }
        $user = $this->user();
        $model = $this->model($this->m);
        //会员才能看
        $user['is_buy'] = 0;
        if($user['vip_id'] > 1){
            $user['is_buy'] = 1;
        }
        //是否关注主播
        $follow = $this->isFollow($user['id'], $info['id'], $model);
        $user['is_follow'] = $follow ? 1 : 0;
        //是否点赞
        $praise = $this->isPraise($user['id'], $info['id'], $model);
        $user['is_praise'] = $praise ? 1 : 0;
        $info['user'] = $user;
        //视频当前播放时间(开播时间 + 当前时间)
        $info['play'] = 0;
        if($info['work'] == Live::WORK_1){
            //当前时间
            $time = time();
            //计算上播时间
            $on = strtotime($info['hour']);
            $play = $time - $on;
            $info['play'] = $play;
        }
        //随机一个游戏
        $game = Game::where('status', Game::STATUS_1)->inRandomOrder()->first(['id', 'name', 'url', 'icon', 'kind_id']);
        $info['game'] = $game;
        //增加浏览量
        QueueService::push(new IncJob(['id' => $info['id'], 'model' => $this->m]));
        return $this->returnJson(0, $info);
    }


    public function praise(RequestInterface $request)
    {
        $id = (int)$request->post('id', 0);
        $user = $this->user();
        $userid = $user['id'];
        if(!$userid){
            return $this->returnJson(1, null, '未登录');
        }
        $info = Live::where('status','<>', Live::STATUS_3)->find($id);
        //数据是否存在
        if(!$info){
            return $this->returnJson(1, null, '数据不存在');
        }
        $model = $this->model($this->m);
        //是否已经点过赞
        $praise = $this->isPraise($userid, $id, $model);
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


    public function follow(RequestInterface $request)
    {
        $id = (int)$request->post('id', 0);
        $user = $this->user();
        $userid = $user['id'];
        if(!$userid){
            return $this->returnJson(1, null, '未登录');
        }
        $info = Live::where('status','<>', Live::STATUS_3)->find($id);
        //数据是否存在
        if(!$info){
            return $this->returnJson(1, null, '数据不存在');
        }
        $model = $this->model($this->m);
        //是否已经关注(这里关注的是类目的信息)
        $follow = UserFollow::where('user_id', $userid)
            ->where('good_id', $id)
            ->where('model', $model)->first();
        if($follow){
            return $this->returnJson(1, null, '已关注');
        }
        $insert = [
            'user_id' => $userid,
            'good_id' => $id,
            'model' => $model
        ];
        UserFollow::create($insert);
        return $this->returnJson();
    }
}
