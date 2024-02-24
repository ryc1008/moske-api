<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\BaseController;
use App\Model\Canal;
use Hyperf\HttpServer\Contract\RequestInterface;

class CanalController extends BaseController
{
    //配置项
    public function config(){
        try {
            $config =  [
                'status' => Canal::STATUS_TEXT,
                'domain' => setting('domain_canal'),
            ];
            return $this->returnJson(0, $config);
        } catch (\Throwable $e) {
            return $this->returnJson(1, null, $e->getMessage());
        }
    }

    //列表
    public function list(RequestInterface $request)
    {
        try {
            $params = $request->all();
            $lists = Canal::list($params);
            state_to_text($lists, [
                'status' => Canal::STATUS_TEXT,
                'mobile' => Canal::MOBILE_TEXT
            ]);
            return $this->returnJson(0, $lists);
        } catch (\Throwable $e) {
            return $this->returnJson(1, null, $e->getMessage());
        }
    }

    //集合
    public function tree()
    {
        try {
            $tree = Canal::tree(['status' => Canal::STATUS_1], 'username');
            return $this->returnJson(0, $tree);
        } catch (\Throwable $e) {
            return $this->returnJson(1, null, $e->getMessage());
        }
    }

    //更新
    public function update(RequestInterface $request)
    {
        try {
            $data = $request->all();
            if ($data['id']) {
                $info = Canal::find($data['id']);
                if(!$info){
                    return $this->returnJson(1, null, '信息不存在');
                }
                if ($data['password']) {
                    $data['password'] = bcrypt($data['password']);
                } else {
                    unset($data['password']);
                }
                $info->update($data);
                return $this->returnJson(0, null, '修改信息成功');
            } else {
                $data['password'] = $data['password'] ? bcrypt($data['password']) : bcrypt('123456');
                $data['deduct_day'] = $data['deduct_day'] ?: 0;
                $data['deduct_month'] = $data['deduct_month'] ?: 0;
                $data['deduct_half'] = $data['deduct_half'] ?: 0;
                $data['deduct_year'] = $data['deduct_year'] ?: 0;
                $data['deduct_forever'] = $data['deduct_forever'] ?: 0;
                $data['deduct_reg'] = $data['deduct_reg'] ?: 0;
                $data['order_free'] = $data['order_free'] ?: 0;
                $data['order_total'] = $data['order_total'] ?: 0;
                $data['profit'] = $data['profit'] ?: 0;
                $data['rebate'] = $data['rebate'] ?: 0;
                Canal::create($data);
                return $this->returnJson(0, null, '新增信息成功');
            }
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
            Canal::store($ids, ['status' => Canal::STATUS_2]);
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
            Canal::store($ids, ['status' => Canal::STATUS_1]);
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
            Canal::destroy($ids);
            return $this->returnJson(0 , null, '操作成功');
        } catch (\Throwable $e) {
            return $this->returnJson(1, null, $e->getMessage());
        }
    }

    //授权登录
    public function login(RequestInterface $request){
        try {
            $id = $request->post('id');
            if (!$id) {
                return $this->returnJson(1, null, 'ID参数必须存在');
            }
            $user = Canal::where('status', Canal::STATUS_1)->find($id);
            if(!$user){
                return $this->returnJson(1, null, '用户信息不存在或者被锁定');
            }
            $token = auth('jwt:canal')->login($user);
            cache()->set('agent:token', $token);
            return $this->returnJson();
        } catch (\Throwable $e) {
            return $this->returnJson(1, null, $e->getMessage());
        }
    }
}
