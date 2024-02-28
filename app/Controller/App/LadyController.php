<?php

declare(strict_types=1);

namespace App\Controller\App;

use App\Job\IncJob;
use App\Model\Lady;
use App\Model\User;
use App\Model\UserBuy;
use App\Model\UserFavor;
use App\Service\QueueService;
use Hyperf\DbConnection\Db;
use Hyperf\HttpServer\Contract\RequestInterface;


class LadyController extends CommonController
{

    protected $m = 'lady';

    public function list(RequestInterface $request)
    {
        $data = $request->all();
        $kwd = $data['kwd'] ?? '';
        $city = $data['city'] ?? '';
        $params['status'] = [Lady::STATUS_1, Lady::STATUS_2];
        $params['sort'] = 'id';
        $params['city'] = $city == '全国' ? null : $city;
        if($kwd == 'default'){
            $params['sort'] = 'show';
        }
        if($kwd == 'good'){
            $params['status'] = [Lady::STATUS_2];
        }
        $fields = ['id', 'title', 'thumb', 'money', 'show', 'favor', 'price'];
        $list = Lady::app($params, $fields, 8);
        return $this->returnJson(0, $list);
    }

    public function info(RequestInterface $request){
        $id = (int)$request->query('id', 0);
        if(!$id){
            return $this->returnJson(1, null, '参数错误');
        }
        $fields = ['id', 'title', 'thumb', 'content', 'project', 'time', 'price', 'age', 'number', 'blurb', 'city', 'money', 'favor'];
        $info = Lady::where('status','<>', Lady::STATUS_3)->find($id, $fields);
        if(!$info){
            return $this->returnJson(1, null, '数据不存在');
        }
        $user = $this->user();
        $model = $this->model($this->m);
        //需要钻石的必须是购买才能看
        $user['is_buy'] = 0;
        $buy = $this->isBuy($user['id'], $info['id'], $model);
        if($buy){
            $user['is_buy'] = 1;
        }
        //是否收藏
        $favor = $this->isFavor($user['id'], $info['id'], $model);
        $user['is_favor'] = $favor ? 1 : 0;
        $info['user'] = $user;
        $info['content'] = explode("\n", trim($info['content']));
        $info['content'] = array_merge([$info['thumb']], $info['content']);
            //增加浏览量
        QueueService::push(new IncJob(['id' => $info['id'], 'model' => $this->m]));
        return $this->returnJson(0, $info, $id);

    }


    public function favor(RequestInterface $request)
    {
        $id = (int)$request->post('id', 0);
        $user = $this->user();
        $userid = $user['id'];
        if(!$userid){
            return $this->returnJson(1, null, '未登录');
        }
        $info = Lady::where('status','<>', Lady::STATUS_3)->find($id);
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

    public function buy(RequestInterface $request)
    {
        $id = (int)$request->post('id', 0);
        $user = $this->user();
        $userid = $user['id'];
        if(!$userid){
            return $this->returnJson(1, null, '未登录');
        }
        $info = Lady::where('status','<>', Lady::STATUS_3)->find($id);
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

