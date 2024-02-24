<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\BaseController;
use App\Model\Authen;
use App\Model\Role;
use Hyperf\HttpServer\Contract\RequestInterface;

class RoleController extends BaseController
{

    //列表
    public function list(RequestInterface $request)
    {
        try {
            $params = $request->all();
            $fields = ['id', 'title', 'remark', 'created_at'];
            $lists = Role::list($params, $fields);
            return $this->returnJson(0, $lists);
        } catch (\Throwable $e) {
            return $this->returnJson(1, null, $e->getMessage());
        }
    }

    //集合
    public function tree()
    {
        try {
            $tree = Role::tree([],'title');
            return $this->returnJson(0, $tree);
        } catch (\Throwable $e) {
            return $this->returnJson(1, null, $e->getMessage());
        }
    }

    public function access(RequestInterface $request){
        try {
            $id = $request->query('id', 0);
            if (!$id) {
                return $this->returnJson(1, null, 'ID参数必须存在');
            }
            $info = Role::find($id, ['rules']);
            //所有权限列表
            $auths = Authen::list(['id' => 10000], ['id', 'name', 'access', 'parent_id']);
            $auths = list_to_tree($auths, 'id', 'parent_id', 'children', 10000);
            return $this->returnJson(0, [
                'auths' => $auths,
                'rules' => $info['rules']
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
            if(isset($data['title'])){
                $repeat = Role::repeat('title', $data);
                if($repeat){
                    return $this->returnJson(1, null, '"'.$data['title']. '"信息已经存在');
                }
                $data['rules'] = [];
                Role::renew($data);
                return $this->returnJson(0, null, $data['id'] ? '修改信息成功' : '新增信息成功');
            }else{
                if($data['id'] == 1){
                    return $this->returnJson(1, null, 'admin用户不能更新权限');
                }
                Role::renew($data);
                return $this->returnJson(0, null, '更新权限成功');
            }
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
                return $this->returnJson(1, null, '超级管理员不能删除');
            }
            Role::destroy($ids);
            return $this->returnJson(0 , null, '操作成功');
        } catch (\Throwable $e) {
            return $this->returnJson(1, null, $e->getMessage());
        }
    }

}
