<?php

declare(strict_types=1);

namespace App\Controller\App;

use App\Controller\BaseController;
use App\Model\User;
use App\Model\UserBuy;
use App\Model\UserFavor;
use App\Model\UserFollow;
use App\Model\UserPraise;

class CommonController extends BaseController
{
    /**
     * 获取登录用户的VIP信息
     * @return array|null
     */
    protected function user(){
        try{
            $auth = User::find(auth('jwt:user')->id(), ['id', 'vip_id', 'balance', 'vip_at']);
//            $auth = auth('jwt:user')->user();//这个数据
            $check = $this->check($auth['vip_at'], $auth['id']);
            if ($check) {
                $auth['vip_id'] = 1;
                $auth['vip_at'] = null;
            }
            $user = ['id' => $auth['id'], 'vip_id' => $auth['vip_id'], 'balance' => $auth['balance']];
        }catch (\Throwable $e){
            $user = ['id' => 0, 'vip_id' => 0, 'balance' => 0];
        }
        return $user;
    }

    /**
     * 检测用户VIP时间是否到期，到期并更新
     * @param $date vip时间
     * @param $userid 用户ID
     * @return void
     */
    protected function check($date = '', $userid = 0){
        if($date){
            $now = time();
            $time = strtotime($date->toDateTimeString());
            if ($now > $time) {
                $update = ['vip_id' => 1, 'vip_at' => null];
                User::where('id', $userid)->update($update);
                return true;
            }
        }
        return false;
    }

    /**
     * 是否购买
     * @param $userid
     * @param $gid
     * @param $model
     * @return \Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Model|object|null
     */
    protected function isBuy($userid = 0, $gid = 0, $model = 0){
        return UserBuy::where('user_id', $userid)->where('good_id', $gid)->where('model', $model)->first();
    }

    /**
     * 是否收藏
     * @param $userid
     * @param $gid
     * @param $model
     * @return \Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Model|object|null
     */
    protected function isFavor($userid = 0, $gid = 0, $model = 0){
        return UserFavor::where('user_id', $userid)->where('good_id', $gid)->where('model', $model)->first();
    }

    /**
     * 是否点赞
     * @param $userid
     * @param $gid
     * @param $model
     * @return \Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Model|object|null
     */
    protected function isPraise($userid = 0, $gid = 0, $model = 0){
        return UserPraise::where('user_id', $userid)->where('good_id', $gid)->where('model', $model)->first();
    }

    /**
     * 是否关注
     * @param $userid
     * @param $gid
     * @param $model
     * @return \Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Model|object|null
     */
    protected function isFollow($userid = 0, $gid = 0, $model = 0){
        return UserFollow::where('user_id', $userid)->where('good_id', $gid)->where('model', $model)->first();
    }

    /**
     * 获取模型标识
     * @param $str
     * @return int
     */
    protected function model($str = ''){
        $model = [
            'playlet' => 1, //短剧
            'live' => 2, //直播
        ];
        return $model[$str];
    }




}
