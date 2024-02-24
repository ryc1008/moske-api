<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\BaseController;
use App\Model\Setting;
use Hyperf\HttpServer\Contract\RequestInterface;

class SettingController extends BaseController
{

    //配置项
    public function config(){
        try {
            $config =  [
                'status' => Setting::STATUS_TEXT,
                'groups' => Setting::tree(['parent_id' => 1000]),
                'types' => Setting::TYPE_TEXT,
            ];
            return $this->returnJson(0, $config);
        } catch (\Throwable $e) {
            return $this->returnJson(1, null, $e->getMessage());
        }
    }

    //列表
    public function list()
    {
        try {
            $params = ['id' => 1000];
            $lists = Setting::list($params);
            state_to_text($lists, [
                'status' => Setting::STATUS_TEXT,
                'type' => Setting::TYPE_TEXT,
            ]);
            foreach ($lists as &$item){
                if($item['rule']){
                    $item['rule'] = text_to_array($item['rule']);
                }
            }
            $lists = list_to_tree($lists, 'id', 'parent_id', 'children', 1000);
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
            $repeat = Setting::repeat('name', $data);
            if($repeat){
                return $this->returnJson(1, null, '"'.$data['name']. '"信息已经存在');
            }
            Setting::renew($data);
            return $this->returnJson(0, null, $data['id'] ? '修改信息成功' : '新增信息成功');
        } catch (\Throwable $e) {
            return $this->returnJson(1, null, $e->getMessage());
        }
    }

    //设置
    public function save(RequestInterface $request){
        try {
            $data = $request->all();
            foreach ($data as $key => $val){
                Setting::where('name', $key)->update(['value' => $val]);
                if(in_array($key, ['white_login', 'channel_wechat', 'channel_alipay'])){
                    $data[$key] = explode("\n", trim($val));
                }
            }
            cache()->set('setting:default', $data);
            return $this->returnJson(0, $data, '修改信息成功');
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
            Setting::destroy($ids);
            return $this->returnJson(0 , null, '操作成功');
        } catch (\Throwable $e) {
            return $this->returnJson(1, null, $e->getMessage());
        }
    }

}
