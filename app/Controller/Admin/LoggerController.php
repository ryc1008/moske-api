<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\BaseController;
use App\Until\LogFilePaginate;
use Hyperf\HttpServer\Contract\RequestInterface;

class LoggerController extends BaseController
{
    //配置
    public function config(){
        $logfiles = (new LogFilePaginate())->list();
        $levels = ['debug', 'error', 'warning', 'info'];
        $models = ['http', 'sql', 'crontab', 'process', 'log'];
        $config =  [
            'logs' => $logfiles,
            'levels' => $levels,
            'models' => $models,
        ];
        return $this->returnJson(0, $config);
    }

    //列表
    public function list(RequestInterface $request)
    {
        $list = (new LogFilePaginate())->paginate();
        return $this->returnJson(0, $list);
    }

    //删除
    public function destroy(RequestInterface $request)
    {
        $name = $request->post('file');
        if (!$name) {
            return $this->returnJson(1, null, 'FILE参数必须存在');
        }
        $file = BASE_PATH . "/runtime/logs/".$name;
        if (!is_dir($file) && file_exists($file)) {
            unlink($file);
            return $this->returnJson(0, null, '操作成功');
        }
        return $this->returnJson(1, null, '文件不存在');
    }
}
