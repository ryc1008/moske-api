<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\BaseController;
use App\Model\Authen;
use Hyperf\HttpServer\Contract\RequestInterface;

class AuthenController extends BaseController
{
    //列表
    public function list(RequestInterface $request)
    {
        try {
            $params = ['id' => 10000];
            $lists = Authen::list($params);
            $lists = list_to_tree($lists, 'id', 'parent_id', 'children', 10000);
            return $this->returnJson(0, $lists);
        } catch (\Throwable $e) {
            return $this->returnJson(1, null, $e->getMessage());
        }
    }

    //集合
    public function tree()
    {
        try {
            $lists = Authen::list();
            $tree = Authen::toFormatTree($lists, 'name', 'id', 'parent_id');
            //特殊处理，去掉操作，保留三级即可
            $tree = array_filter($tree, function ($item){
                return $item['level'] < 3;
            });
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
            $nameArr = explode('|', $data['name']);
            $accessArr = explode('|', $data['access']);
            $markArr = explode('|', $data['mark']);
            $sortArr = explode('|', $data['sort']);
            foreach ($nameArr as $k => $name){
                Authen::renew([
                    'id' => $data['id'],
                    'parent_id' => $data['parent_id'],
                    'name' => $name,
                    'access' => $accessArr[$k],
                    'mark' => $markArr[$k],
                    'sort' => $sortArr[$k],
                ]);
            }
            return $this->returnJson(0, null, $data['id'] ? '修改信息成功' : '新增信息成功');
        } catch (\Throwable $e) {
            return $this->returnJson(1, null, $e->getMessage());
        }
    }

    //删除
    public function destroy(RequestInterface $request)
    {
        try {
            $id = $request->post('id');
            if (!$id) {
                return $this->returnJson(1, null, 'ID参数必须存在');
            }
            //如果有子节点，不能删除
            if(Authen::child($id)){
                return $this->returnJson(1 , null, '当前节点下有子集，不能执行删除操作');
            }
            $ids = is_array($id) ? $id : [$id];
            Authen::destroy($ids);
            return $this->returnJson(0 , null, '操作成功');
        } catch (\Throwable $e) {
            return $this->returnJson(1, null, $e->getMessage());
        }
    }

}
