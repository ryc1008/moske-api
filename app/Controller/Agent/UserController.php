<?php

declare(strict_types=1);

namespace App\Controller\Agent;

use App\Controller\BaseController;
use App\Model\Canal;
use Hyperf\HttpServer\Contract\RequestInterface;

class UserController extends BaseController
{
    //登录用户
    public function index()
    {
        try {
            return $this->returnJson(0, [
                'user' => $this->user(),
            ]);
        } catch (\Throwable $e) {
            return $this->returnJson(1, null, $e->getMessage());
        }
    }

    //更新
    public function update(RequestInterface $request)
    {
        try {
            $data = $request->all();
            $id = auth('jwt:canal')->id();
            Canal::where('id', $id)->update($data);
            $info = Canal::find($id, ['id', 'username', 'nickname', 'name', 'contact', 'bank', 'card', 'apk', 'avatar', 'balance', 'profit', 'rebate', ]);
            return $this->returnJson(0, $info, '更新成功');
        } catch (\Throwable $e) {
            return $this->returnJson(1, null, $e->getMessage());
        }
    }

    //充值密码
    public function password(RequestInterface $request)
    {
        try {
            $data = $request->all();
            if(!$data['new_password']){
                return $this->returnJson(1, null, '请输入新密码');
            }
            if($data['old_password'] == $data['new_password']){
                return $this->returnJson(1, null, '新密码不能和旧密码一样');
            }
            if($data['new_password'] != $data['check_password']){
                return $this->returnJson(1, null, '新密码两次输入不一致');
            }
            $user = auth('jwt:canal')->user();
            if(!password_verify($data['old_password'], $user['password'])){
                return $this->returnJson(1, null, '旧密码输入错误');
            }
            Canal::where('id', $user['id'])->update(['password' => bcrypt($data['new_password'])]);
            return $this->returnJson(0, null, '更新成功');
        } catch (\Throwable $e) {
            return $this->returnJson(1, null, $e->getMessage());
        }
    }


    protected function user(){
        $user = auth('jwt:canal')->user();
        unset($user['password']);
        unset($user['login_ip']);
        unset($user['address']);
        unset($user['user_agent']);
        unset($user['agent_id']);
        unset($user['deduct_day']);
        unset($user['deduct_month']);
        unset($user['deduct_half']);
        unset($user['deduct_year']);
        unset($user['deduct_forever']);
        unset($user['deduct_reg']);
        unset($user['order_total']);
        unset($user['order_free']);
        unset($user['status']);
        unset($user['mobile']);
        unset($user['created_at']);
        unset($user['updated_at']);
        return $user;
    }
}
