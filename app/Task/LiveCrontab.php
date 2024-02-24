<?php

declare(strict_types=1);

namespace App\Task;


use App\Model\Live;

class LiveCrontab
{
    //每天24点执行
    public function reset()
    {
        //更新所有主播数据为未播状态
        Live::query()->update(['work' => Live::WORK_0]);
        logger_write('crontab_task', 'live-reset is work status reset 0', 'warning', 'crontab');
    }

    //每分钟执行
    public function work()
    {
        //下播的不用管, 正常状态下的主播轮询上下状态
        $lives = Live::where('status', '<', Live::STATUS_3)
            ->where('work', '<', Live::WORK_2)
            ->get();
        foreach ($lives as $info){
            $time = time();
            //计算上播时间
            $on = strtotime($info['hour']);
            //计算下播时间
            $off = strtotime($info['hour']) + str_to_second($info['time']);
            //未播状态
            if($info['work'] == Live::WORK_0){
                if($time >= $on){
                    $info->update(['work' => Live::WORK_1]);
                }
            }
            //上播状态
            if($info['work'] == Live::WORK_1){
                if($time >= $off){
                    $info->update(['work' => Live::WORK_2]);
                }
            }
        }
    }

}