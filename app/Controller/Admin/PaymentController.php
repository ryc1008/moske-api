<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\BaseController;
use App\Model\Payment;
use Hyperf\HttpServer\Contract\RequestInterface;

class PaymentController extends BaseController
{
    //配置项
    public function config(){
        try {
            $config =  [
                'status' => Payment::STATUS_TEXT,
                'ways' => Payment::WAY_TEXT,
                'types' => Payment::TYPE_TEXT,
                'methods' => Payment::METHOD_TEXT,
                'channels' => Payment::CHANNEL_TEXT,
                'codes' => Payment::CODE_TEXT,
                'notify' => setting('domain_notify'),
            ];
            return $this->returnJson(0, $config);
        } catch (\Throwable $e) {
            return $this->returnJson(1, null, $e->getMessage());
        }
    }

    public function tree(){
        try {
            //所有支付
            $data  = Payment::where('status',Payment::STATUS_1)->get(['title', 'name', 'type', 'channel', 'code'])->toArray();
            $weiPays = [];
            $aliPays = [];
            //不管是话费还是原生还是属于扫码,每次只能设置一个微信和支付宝通道编码
            foreach ($data as $item){
                if($item['type'] == Payment::TYPE_1){
                    $weiPays[] = $item;
                }
                if($item['type'] == Payment::TYPE_2){
                    $aliPays[] = $item;
                }
                if($item['type'] == Payment::TYPE_3){
                    $weiPays[] = $item;
                    $aliPays[] = $item;
                }
            }
            return $this->returnJson(0, ['wei' => $weiPays, 'ali' => $aliPays]);
        } catch (\Throwable $e) {
            return $this->returnJson(1, null, $e->getMessage());
        }
    }

    //列表
    public function list(RequestInterface $request)
    {
        try {
            $params = $request->all();
            $lists = Payment::list($params);
            state_to_text($lists, [
                'status' => Payment::STATUS_TEXT,
                'way' => Payment::WAY_TEXT,
                'type' => Payment::TYPE_TEXT,
                'method' => Payment::METHOD_TEXT,
                'channel' => Payment::CHANNEL_TEXT,
                'code' => Payment::CODE_TEXT,
            ]);
            return $this->returnJson(0, $lists);
        } catch (\Throwable $e) {
            return $this->returnJson(1, null, $e->getMessage());
        }
    }

    //更新
    public function update(RequestInterface $request)
    {
        try {
            $data = $request->all();
            $repeat = Payment::repeat('name', $data);
            if($repeat){
                return $this->returnJson(1, null, '"'.$data['name']. '"信息已经存在');
            }
            Payment::renew($data);
            return $this->returnJson(0, null, $data['id'] ? '修改信息成功' : '新增信息成功');
        } catch (\Throwable $e) {
            return $this->returnJson(1, null, $e->getMessage());
        }
    }

    //锁定
    public function lock(RequestInterface $request)
    {
        try {
            $id = $request->post('id');
            $ids = is_array($id) ? $id : [$id];
            if (!count($ids)) {
                return $this->returnJson(1, null, 'ID参数必须存在');
            }
            Payment::store($ids, ['status' => Payment::STATUS_2]);
            return $this->returnJson(0 , null, '操作成功');
        } catch (\Throwable $e) {
            return $this->returnJson(1, null, $e->getMessage());
        }
    }

    //激活
    public function active(RequestInterface $request)
    {
        try {
            $id = $request->post('id');
            $ids = is_array($id) ? $id : [$id];
            if (!count($ids)) {
                return $this->returnJson(1, null, 'ID参数必须存在');
            }
            Payment::store($ids, ['status' => Payment::STATUS_1]);
            return $this->returnJson(0 , null, '操作成功');
        } catch (\Throwable $e) {
            return $this->returnJson(1, null, $e->getMessage());
        }
    }

    //删除
    public function destroy(RequestInterface $request)
    {
        try {
            $id = $request->post('id');
            $ids = is_array($id) ? $id : [$id];
            if (!count($ids)) {
                return $this->returnJson(1, null, 'ID参数必须存在');
            }
            Payment::destroy($ids);
            return $this->returnJson(0 , null, '操作成功');
        } catch (\Throwable $e) {
            return $this->returnJson(1, null, $e->getMessage());
        }
    }
}
