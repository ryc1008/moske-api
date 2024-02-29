<?php
declare(strict_types=1);

namespace App\Job;

use App\Model\Lady;
use App\Model\Playlet;
use App\Model\Story;
use App\Model\User;
use App\Until\RegionSearcher;
use Carbon\Carbon;
use Hyperf\AsyncQueue\Job;

class IncJob extends Job
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
        /**
         * id 资源ID
         * model 模型
         * ?number 看是否有需要
         */
        $data = $this->params;
        if($data && $data['model']){
            switch ($data['model']){
                case 'playlet':
                    Playlet::where('id', $data['id'])->increment('show');
                    break;
                case 'lady':
                    Lady::where('id', $data['id'])->increment('show');
                    break;
                case 'story':
                    Story::where('id', $data['id'])->increment('show');
                    break;

            }
        }else{
            logger_write('job-increment', $data, 'error', 'process');
        }
    }
}