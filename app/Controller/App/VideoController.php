<?php

declare(strict_types=1);

namespace App\Controller\App;

use App\Job\IncJob;
use App\Job\RegisterJob;
use App\Model\Playlet;
use App\Model\Type;
use App\Model\User;
use App\Model\UserBuy;
use App\Model\UserFavor;
use App\Model\UserFollow;
use App\Model\UserPraise;
use App\Model\Video;
use App\Service\QueueService;
use Hyperf\DbConnection\Db;
use Hyperf\HttpServer\Contract\RequestInterface;


class VideoController extends CommonController
{

    protected $m = 'video';

    public function main(RequestInterface $request)
    {
        $data = $request->all();
        $params['page'] = $data['page'] ?? 1;
        $gid = $data['gid'] ?? 0;
        if($gid == 'good'){
            $gid = 10001;
        }
        if($gid == 'topic'){
            $gid = 10042;
        }


        if($gid == 'good'){
            $lists = Db::table(Db::raw('(SELECT *, ROW_NUMBER() OVER ( PARTITION BY `group_id` ORDER BY RAND()) AS number FROM `videos` ) AS r'))
                ->where('r.status', Video::STATUS_2)
                ->where('r.number', '<=', 5)
                ->get();
        }else{
            $types = Type::where('parent_id', $gid)
                ->where('status', Type::STATUS_1)
                ->orderBy('sort')
                ->get(['id', 'title', 'name', 'icon']);
            $ids = [];
            foreach ($types as $type){
                $ids[] = $type['id'];
            }
            $lists = Db::table(Db::raw('(SELECT *, ROW_NUMBER() OVER ( PARTITION BY `group_id` ORDER BY RAND()) AS number FROM `videos` ) AS r'))
                ->where('r.status', Video::STATUS_2)
                ->where('r.number', '<=', 5)
                ->get();
        }
        return $this->returnJson(0, ['types' => $types, 'lists' => $lists]);
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
        $favor = UserFavor::where('user_id', $userid)
            ->where('good_id', $id)
            ->where('model', $model)->first();
        if($favor){
            return $this->returnJson(1, null, '已收藏');
        }
        $insert = [
            'user_id' => $userid,
            'good_id' => $id,
            'model' => $model
        ];
        Db::transaction(function () use ($insert, $info){
            UserFavor::create($insert);
            $info->increment('favor');
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
        $info = Type::where('status',Type::STATUS_1)->find($id);
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

    public function buy(RequestInterface $request)
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
        //是否已经购买
        $buy = UserBuy::where('user_id', $userid)
            ->where('good_id', $id)
            ->where('model', $model)->first();
        if($buy){
            return $this->returnJson(1, null, '已购买');
        }
        //余额是否充足
        if($user['balance'] < $info['money']){
            return $this->returnJson(1, null, '您的余额不足');
        }
        $insert = [
            'user_id' => $userid,
            'good_id' => $id,
            'model' => $model,
            'money' => $info['money']
        ];
        Db::transaction(function () use ($insert, $info){
            UserBuy::create($insert);//购买记录
            $info->increment('sale');//销售数量
            User::where('id', $insert['user_id'])->decrement('balance', $insert['money']);//减少钻石
        });
        return $this->returnJson();
    }
}

