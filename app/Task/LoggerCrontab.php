<?php

declare(strict_types=1);

namespace App\Task;


use App\Until\LogFilePaginate;
use Carbon\Carbon;

class LoggerCrontab
{
    //每天24点执行
    public function clear()
    {
        //只保留最近10天的日志记录
        $logfiles = (new LogFilePaginate())->list();
        $time = Carbon::now()->subDays(11)->timestamp;
        foreach ($logfiles as $name){
            $date = str_replace(['hyperf-', '.log'], '', $name);
            if(strtotime($date) < $time){
                $file = BASE_PATH . "/runtime/logs/".$name;
                if (!is_dir($file) && file_exists($file)) {
                    unlink($file);
                }
            }
        }
        logger_write('crontab_task', 'logger-clear is running', 'warning', 'crontab');
    }

}