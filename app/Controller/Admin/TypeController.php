<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\BaseController;
use App\Model\Type;
use Hyperf\HttpServer\Contract\RequestInterface;

class TypeController extends BaseController
{
    //配置项
    public function config(){
        try {
            $config =  [
                'status' => Type::STATUS_TEXT,
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
            $params = ['id' => 10000];
            $lists = Type::list($params,['id', 'title', 'icon', 'parent_id', 'sort', 'status', 'name']);
            state_to_text($lists, [
                'status' => Type::STATUS_TEXT,
            ]);
            $lists = list_to_tree($lists, 'id', 'parent_id', 'children', 10000);
            return $this->returnJson(0, $lists);
        } catch (\Throwable $e) {
            return $this->returnJson(1, null, $e->getMessage());
        }
    }

    //集合
    public function tree(RequestInterface $request)
    {
        try {
            $params = $request->all();
            $lists = Type::list($params);
            $tree = Type::toFormatTree($lists, 'title', 'id', 'parent_id');
            //特殊处理，去掉操作，保留两级即可
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
            Type::renew($data);
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
            Type::store($ids, ['status' => Type::STATUS_2]);
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
            Type::store($ids, ['status' => Type::STATUS_1]);
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
            if (!$id) {
                return $this->returnJson(1, null, 'ID参数必须存在');
            }
            //如果有子节点，不能删除
            if(Type::child($id)){
                return $this->returnJson(1 , null, '当前节点下有子集，不能执行删除操作');
            }
            $ids = is_array($id) ? $id : [$id];
            Type::destroy($ids);
            return $this->returnJson(0 , null, '操作成功');
        } catch (\Throwable $e) {
            return $this->returnJson(1, null, $e->getMessage());
        }
    }

}
