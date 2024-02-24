<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\BaseController;
use App\Model\Sound;
use App\Model\SoundChapter;
use App\Model\Type;
use Hyperf\HttpServer\Contract\RequestInterface;

class SoundController extends BaseController
{

    public function config(){
        try {
            $types = Type::tree(['status' => Type::STATUS_1, 'parent_id' => 10003]);
            $config =  [
                'status' => Sound::STATUS_TEXT,
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
            $lists = Sound::list($params);
            state_to_text($lists, [
                'status' => Sound::STATUS_TEXT,
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
            Sound::renew($data);
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
            Sound::store($ids, ['status' => Sound::STATUS_3]);
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
            Sound::store($ids, ['status' => Sound::STATUS_1]);
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
            Sound::store($ids, ['status' => Sound::STATUS_2]);
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
            $info = Sound::find($id);
            if (!$info) {
                return $this->returnJson(1, null, '数据不存在');
            }
            $chapter = SoundChapter::list($id);
            if (count($chapter)) {
                return $this->returnJson(1, null, '请先删除所有章节');
            }
            $info->delete();
            return $this->returnJson(0 , null, '操作成功');
        } catch (\Throwable $e) {
            return $this->returnJson(1, null, $e->getMessage());
        }
    }

    //章节
    public function chapter(RequestInterface $request){
        try {
            $id = $request->query('id');
            $chapter = SoundChapter::list($id);
            return $this->returnJson(0, $chapter);
        } catch (\Throwable $e) {
            return $this->returnJson(1, null, $e->getMessage());
        }
    }

    //章节更新
    public function save(RequestInterface $request)
    {
        try {
            $data = $request->all();
            SoundChapter::renew($data);
            //更新小说章节数
            if(!$data['id']){
                Sound::where('id', $data['sound_id'])->increment('chapter');
            }
            return $this->returnJson(0, null, $data['id'] ? '修改章节成功' : '新增章节成功');
        } catch (\Throwable $e) {
            return $this->returnJson(1, null, $e->getMessage());
        }
    }

    //删除章节
    public function delete(RequestInterface $request)
    {
        try {
            $id = $request->post('id');
            if (!$id) {
                return $this->returnJson(1, null, 'ID参数必须存在');
            }
            $info = SoundChapter::find($id);
            if (!$info) {
                return $this->returnJson(1, null, '数据不存在');
            }
            $info->delete();
            Sound::where('id', $info['sound_id'])->decrement('chapter');
            return $this->returnJson(0 , null, '操作成功');
        } catch (\Throwable $e) {
            return $this->returnJson(1, null, $e->getMessage());
        }
    }

}
