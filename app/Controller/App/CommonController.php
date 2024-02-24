<?php

declare(strict_types=1);

namespace App\Controller\App;

use App\Controller\BaseController;
use App\Model\User;

class CommonController extends BaseController
{
    protected $userid = 0;


    public function __construct()
    {
        $this->userid = 10000001;
//        logger_debug($this->userid);
    }

    protected function user(){

    }

    /**
     * 检测用户VIP时间是否到期，到期并更新
     * @param $date vip时间
     * @param $userid 用户ID
     * @return void
     */
    protected function check($date = '', $userid = 0){
        if($date){
            $userid = $userid ?: $this->userid;
            $now = time();
            $time = strtotime($date);
            if ($now > $time) {
                $update = ['vip_id' => 1, 'vip_at' => null];
                User::where('id', $userid)->update($update);
                return true;
            }
        }
        return false;
    }





}
