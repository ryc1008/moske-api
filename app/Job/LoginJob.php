<?php
declare(strict_types=1);

namespace App\Job;

use App\Model\User;
use App\Until\RegionSearcher;
use Carbon\Carbon;
use Hyperf\AsyncQueue\Job;

class LoginJob extends Job
{
    public $params;

    protected int $maxAttempts = 2; //任务执行失败后的重试次数，即最大执行次数为 $maxAttempts+1 次

    public function __construct($params)
    {
        // 这里最好是普通数据，不要使用携带 IO 的对象，比如 PDO 对象
        $this->params = $params;
    }

    //该队列多进程执行
    public function handle()
    {
        $data = $this->params;
        if($data && $data['userid']){
            //地址库
            $searcher = new RegionSearcher();
            $address = $searcher->search($data['login_ip']);
            //更新免费观影数
            $free = setting('free_video');
            $now = Carbon::createFromTimestamp($data['time']);
            $isSameDay = $now->isSameDay($data['login_at']);
            $freeNum = $isSameDay ? $data['free_num'] : $free;
            $update = [
                'app_version' => $data['app_version'],
                'app_model' => $data['app_model'],
                'app_vendor' => $data['app_vendor'],
                'app_release' => $data['app_release'],
                'app_network' => $data['app_network'],
                'app_system' => $data['app_system'],
                'free_num' => $freeNum,
                'login_ip' => $data['login_ip'],
                'login_at' => $now->toDateTimeString(),
                'address' => $address
            ];
            User::where('id', $data['userid'])->update($update);
        }else{
            logger_write('job-login', $data, 'error', 'process');
        }
    }
}