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
        $params['city'] = $data['city'] ?? '';
        $params['status'] = [Lady::STATUS_1, Lady::STATUS_2];
        $params['sort'] = 'id';
        if($kwd == 'default'){
            $params['sort'] = 'show';
        }
        if($kwd == 'good'){
            $params['status'] = [Lady::STATUS_2];
        }
        $fields = ['id', 'title', 'thumb', 'city', 'money', 'show'];
        $list = Lady::app($params, $fields, 8);
        return $this->returnJson(0, $list);
    }

    public function info(){
        //必须是VIP，免费才能看
//        $user = $this->user();
//        $model = $this->model($this->m);
//        $user['is_buy'] = 0;//不是会员,不管免不免费，都是未购买
//        if($user['vip_id'] > 1){
//            $user['is_buy'] = 1;//是会员, 免费设置成已购买
//        }
//        //不管是不是会员，购买了的肯定都能看（这里不建议把需要花钱的改成免费的，可能造成别人不是VIP了，之前花了钱不能再继续看了）
//        if($item['money'] > 0){
//            //需要钻石的必须是购买才能看
//            $buy = $this->isBuy($user['id'], $item['id'], $model);
//            if(!$buy){
//                $user['is_buy'] = 0;
//            }
//        }
//
//        //是否收藏
//        $favor = $this->isFavor($user['id'], $item['id'], $model);
//        $user['is_favor'] = $favor ? 1 : 0;
//        //是否点赞
//        $praise = $this->isPraise($user['id'], $item['id'], $model);
//        $user['is_praise'] = $praise ? 1 : 0;
//        //是否关注人物（类目）
//        $follow = $this->isFollow($user['id'], $item['type_id'], $model);
//        $user['is_follow'] = $follow ? 1 : 0;
//        $item['user'] = $user;
//        $item['guid'] = uuid();
//        $item['state'] = 'pause';
//        $item['playing'] = false;
        //这个写到进程中去吧，太慢了: 更新自身show值
        QueueService::push(new IncJob(['id' => $item['id'], 'model' => $this->m]));
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

