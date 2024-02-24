<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\BaseController;
use App\Model\Tag;
use Hyperf\HttpServer\Contract\RequestInterface;

class TagController extends BaseController
{
    //列表
    public function list(RequestInterface $request)
    {
        try {
            $params = $request->all();
            $lists = Tag::list($params);
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
            $names =  explode("\n", trim($data['name']));
            foreach ($names as $name){
                Tag::create(['name' => $name]);
            }
            return $this->returnJson(0, null, '新增信息成功');
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
            Tag::destroy($id);
            return $this->returnJson(0 , null, '操作成功');
        } catch (\Throwable $e) {
            return $this->returnJson(1, null, $e->getMessage());
        }
    }

}
