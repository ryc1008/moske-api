<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\BaseController;
use App\Model\Safe;
use App\Model\Manager;
use Hyperf\HttpServer\Contract\RequestInterface;

class ManagerController extends BaseController
{
    //配置项
    public function config(){
        try {
            $config =  [
                'status' => Manager::STATUS_TEXT,
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
            $fields = ['id', 'username', 'nickname', 'avatar', 'role_id', 'status', 'created_at'];
            $lists = Manager::list($params, $fields);
            state_to_text($lists, [
                'status' => Manager::STATUS_TEXT,
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
            if ($data['id']) {
                $info = Manager::find($data['id']);
                if(!$info){
                    return $this->returnJson(1, null, '信息不存在');
                }
                if($data['id'] == 1 && $data['status'] == Manager::STATUS_2){
                    return $this->returnJson(1, null, 'admin账户不能被锁定');
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
                Manager::create($data);
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
            if(in_array(1, $ids)){
                return $this->returnJson(1, null, 'admin账户不能被锁定');
            }
            Manager::store($ids, ['status' => Manager::STATUS_2]);
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
            Manager::store($ids, ['status' => Manager::STATUS_1]);
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
            if(in_array(1, $ids)){
                return $this->returnJson(1, null, 'admin账户不能被删除');
            }
            Manager::destroy($ids);
            return $this->returnJson(0 , null, '操作成功');
        } catch (\Throwable $e) {
            return $this->returnJson(1, null, $e->getMessage());
        }
    }


    //登录用户
    public function user()
    {
        try {
            $user = auth('jwt:manager')->user();
            unset($user['password']);
            $user->role;
            //最近一次登录
            $record = Safe::where('username', $user['username'])->latest('login_at')->first('login_at');
            $user['login_at'] = $record['login_at'];
            return $this->returnJson(0, [
                'user' => $user,
            ]);
        } catch (\Throwable $e) {
            return $this->returnJson(1, null, $e->getMessage());
        }
    }
}
