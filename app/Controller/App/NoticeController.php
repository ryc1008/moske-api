<?php

declare(strict_types=1);

namespace App\Controller\App;

class NoticeController extends CommonController
{

    public function main()
    {
        $message = setting('app_notice');
        return $this->returnJson(0, $message);
    }

    public function game()
    {
        //暂时写死
        $messages = [
            '首次充值立刻送三元',
            '充值成功赠送VIP无限观影',
            '更多优惠活动请关注最新公告',
        ];
        return $this->returnJson(0, $messages);
    }

}
