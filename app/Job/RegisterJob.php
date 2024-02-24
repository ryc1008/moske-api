<?php
declare(strict_types=1);

namespace App\Job;

use App\Model\User;
use App\Until\RegionSearcher;
use Carbon\Carbon;
use Hyperf\AsyncQueue\Job;

class RegisterJob extends Job
{
    public $params;

    protected int $maxAttempts = 2; //任务执行失败后的重试次数，即最大执行次数为 $maxAttempts+1 次

    public function __construct($params)
    {
        // 这里最好是普通数据，不要使用携带 IO 的对象，比如 PDO 对象
        $this->params = $params;
    }

    public function handle()
    {
        $data = $this->params;
        if($data && $data['userid']){
            //更新流量记录
            //更新流量小时记录
            //更新IP地址库
            $searcher = new RegionSearcher();
            $address = $searcher->search($data['login_ip']);
            $update = ['address' => $address];
            User::where('id', $data['userid'])->update($update);
        }else{
            logger_write('job-register', $data, 'error', 'process');
        }
    }
}