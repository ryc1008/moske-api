<?php

declare(strict_types=1);

namespace App\Controller\App;

use App\Model\Vip;


class VipController extends CommonController
{

    public function config()
    {
        $params = [
            'status' => Vip::STATUS_1,
            'title' => true,
        ];
        $fields = ['id', 'name', 'money', 'price', 'title', 'time', 'welfare'];
        $list = Vip::app($params, $fields);
        $config = [
            'default_price' => setting('default_price'),
            'default_type' => setting('default_type'),
            'payment_wechat' => setting('payment_wechat'),
            'payment_alipay' => setting('payment_alipay'),
        ];
        return $this->returnJson(0, ['list' => $list, 'config' => $config]);
    }

    public function wallet()
    {
        $params = [
            'status' => Vip::STATUS_1,
            'diamond' => true,
        ];
        $fields = ['id', 'money', 'diamond', 'give'];
        $list = Vip::app($params, $fields);
        $user = $this->user();
        $config = [
            'default_price' => setting('default_price'),
            'default_type' => setting('default_type'),
            'payment_wechat' => setting('payment_wechat'),
            'payment_alipay' => setting('payment_alipay'),
        ];
        return $this->returnJson(0, ['list' => $list, 'config' => $config, 'user' => $user]);
    }

}
