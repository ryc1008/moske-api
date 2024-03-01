<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\BaseController;
use App\Model\MacArt;
use App\Model\Photo;
use App\Model\Type;
use Hyperf\HttpServer\Contract\RequestInterface;

class PhotoController extends BaseController
{

    public function config(){
        try {
            $types = Type::tree(['status' => Type::STATUS_1, 'parent_id' => 10004]);
            $config =  [
                'status' => Photo::STATUS_TEXT,
                'types' => $types
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
//            $lists = MacArt::list($params, ['*'], 100);
//            foreach ($lists as $item){
//                if($item['type_id'] = 118){
//                    $typeId = 10035;
//                }
//                if($item['type_id'] = 119){
//                    $typeId = 10036;
//                }
//                if($item['type_id'] = 134){
//                    $typeId = 10037;
//                }
//                $content = str_replace("https", "http", $item['art_content']);
//                $data = [
//                    'id' => 0,
//                    'title' => $item['art_name'],
//                    'thumb' => str_replace("https", "http", $item['art_pic']),
//                    'content' => str_replace("###", "\n", $content),
//                    'type_id' => $typeId,
//                    'show' => mt_rand(500, 9999)
//                ];
//                Photo::renew($data);
//            }
//            return $this->returnJson(0, $lists);
            $lists = Photo::list($params);
            state_to_text($lists, [
                'status' => Photo::STATUS_TEXT,
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
            Photo::renew($data);
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
            Photo::store($ids, ['status' => Photo::STATUS_3]);
            return $this->returnJson(0 , null, '操作成功');
        } catch (\Throwable $e) {
            return $this->returnJson(1, null, $e->getMessage());
        }
    }

    //推荐
    public function good(RequestInterface $request)
    {
        try {
            $id = $request->post('id');
            $ids = is_array($id) ? $id : [$id];
            if (!count($ids)) {
                return $this->returnJson(1, null, 'ID参数必须存在');
            }
            Photo::store($ids, ['status' => Photo::STATUS_2]);
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
            Photo::store($ids, ['status' => Photo::STATUS_1]);
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
            Photo::destroy($ids);
            return $this->returnJson(0 , null, '操作成功');
        } catch (\Throwable $e) {
            return $this->returnJson(1, null, $e->getMessage());
        }
    }

}
