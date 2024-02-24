<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\BaseController;
use App\Model\User;
use App\Model\Vip;
use Hyperf\HttpServer\Contract\RequestInterface;

class UserController extends BaseController
{
    //配置
    public function config(){
        $vips = Vip::tree(['status' => Vip::STATUS_1, 'time' => 1]);
        $config =  [
            'status' => User::STATUS_TEXT,
            'systems' => User::SYSTEM_TEXT,
            'vips' => $vips
        ];
        return $this->returnJson(0, $config);
    }

    //列表
    public function list(RequestInterface $request)
    {
        $params = $request->all();
        $fields = ['id', 'uuid', 'username', 'mobile', 'code', 'balance', 'money', 'canal_id', 'vip_id', 'name', 'bank', 'card', 'app_system', 'app_vendor', 'app_version', 'app_model', 'app_release', 'app_network', 'login_ip', 'address', 'status', 'vip_at', 'login_at', 'created_at'];
        $lists = User::list($params, $fields);
        state_to_text($lists, [
            'status' => User::STATUS_TEXT,
        ]);
        return $this->returnJson(0, $lists);
    }

    //更新
    public function update(RequestInterface $request)
    {
        $data = $request->all();
        User::renew($data);
        return $this->returnJson(0, null, $data['id'] ? '修改信息成功' : '新增信息成功');
    }

    //锁定
    public function lock(RequestInterface $request)
    {
        $id = $request->post('id');
        $ids = is_array($id) ? $id : [$id];
        if (!count($ids)) {
            return $this->returnJson(1, null, 'ID参数必须存在');
        }
        User::store($ids, ['status' => User::STATUS_2]);
        return $this->returnJson(0 , null, '操作成功');
    }

    //激活
    public function active(RequestInterface $request)
    {
        $id = $request->post('id');
        $ids = is_array($id) ? $id : [$id];
        if (!count($ids)) {
            return $this->returnJson(1, null, 'ID参数必须存在');
        }
        User::store($ids, ['status' => User::STATUS_1]);
        return $this->returnJson(0 , null, '操作成功');
    }

}
