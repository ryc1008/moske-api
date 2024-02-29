<?php

declare(strict_types=1);

namespace App\Controller\App;

use App\Job\IncJob;
use App\Model\Story;
use App\Model\User;
use App\Model\UserBuy;
use App\Model\UserFavor;
use App\Service\QueueService;
use Hyperf\DbConnection\Db;
use Hyperf\HttpServer\Contract\RequestInterface;


class StoryController extends CommonController
{

    protected $m = 'story';

    public function home(){
        $fields = ['id', 'title', 'thumb', 'show', 'favor'];
        $latest = Story::lately($fields);
        $good = Story::good($fields);
        return $this->returnJson(0, ['latest' => $latest, 'good' => $good]);
    }

    public function list(RequestInterface $request)
    {
        $data = $request->all();
        $kwd = $data['kwd'] ?? '';
        $params['tid'] = $data['tid'] ?? 0;
        $params['status'] = [Story::STATUS_1, Story::STATUS_2];
        $params['sort'] = 'id';
        if($kwd == 'default'){
            $params['sort'] = 'show';
        }
        if($kwd == 'good'){
            $params['status'] = [Story::STATUS_2];
        }
        $fields = ['id', 'title', 'thumb', 'show', 'favor'];
        $list = Story::app($params, $fields);
        return $this->returnJson(0, $list);
    }

    public function info(RequestInterface $request){
        $id = (int)$request->query('id', 0);
        if(!$id){
            return $this->returnJson(1, null, '参数错误');
        }
        $fields = ['id', 'title', 'thumb', 'content', 'type_id', 'show', 'favor'];
        $info = Story::where('status','<>', Story::STATUS_3)->find($id, $fields);
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
        //是否收藏
        $favor = $this->isFavor($user['id'], $info['id'], $model);
        $user['is_favor'] = $favor ? 1 : 0;
        $info['user'] = $user;
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
        $info = Story::where('status','<>', Story::STATUS_3)->find($id);
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
}

