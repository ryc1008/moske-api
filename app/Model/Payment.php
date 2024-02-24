<?php

declare(strict_types=1);

namespace App\Model;



/**
 * @property int $id 
 * @property string $title 支付名称
 * @property string $name 支付标识
 * @property string $number 商户号
 * @property string $secret 秘钥
 * @property string $address 通道地址
 * @property string $code_wechat 微信通道编码
 * @property string $code_alipay 支付宝通道编码
 * @property string $notify_name 回调标识
 * @property string $order_field 订单号字段
 * @property string $return_msg 回调输出
 * @property string $success_field 成功字段
 * @property string $success_msg 成功返回
 * @property string $link 支付网址
 * @property string $account 支付账户
 * @property string $password 支付密码
 * @property int $type 支付通道：1微信2支付宝3双端
 * @property int $channel 支付类型：1原生2话费3双通道
 * @property int $method 请求方式：1POST2GET3JSON
 * @property int $way 请求类型：1JSON2FORM3CURL
 * @property int $code 扫码通道：0否1是
 * @property int $status 状态：1正常2锁定
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 */
class Payment extends Base
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'payments';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'title', 'name', 'number', 'secret', 'address', 'code_wechat', 'code_alipay', 'notify_name', 'order_field', 'return_msg', 'success_field', 'success_msg', 'link', 'account', 'password', 'type', 'channel', 'method', 'way', 'code', 'status', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'type' => 'integer', 'channel' => 'integer', 'method' => 'integer', 'way' => 'integer', 'code' => 'integer', 'status' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    const CODE_1 = 1;
    const CODE_0 = 0;
    const CODE_TEXT = [
        self::CODE_1 => '是',
        self::CODE_0 => '否',
    ];

    const TYPE_1 = 1;
    const TYPE_2 = 2;
    const TYPE_3 = 3;
    const TYPE_TEXT = [
        self::TYPE_1 => '微信宝',
        self::TYPE_2 => '支付宝',
        self::TYPE_3 => '双付端',
    ];

    const CHANNEL_1 = 1;
    const CHANNEL_2 = 2;
    const CHANNEL_3 = 3;
    const CHANNEL_TEXT = [
        self::CHANNEL_1 => '原生',
        self::CHANNEL_2 => '话费',
        self::CHANNEL_3 => '双通',
    ];

    const METHOD_1 = 1;
    const METHOD_2 = 2;
    const METHOD_3 = 3;
    const METHOD_TEXT = [
        self::METHOD_1 => 'POST',
        self::METHOD_2 => 'GET',
        self::METHOD_3 => 'JSON',
    ];


    const WAY_1 = 1;
    const WAY_2 = 2;
    const WAY_3 = 3;
    const WAY_TEXT = [
        self::WAY_1 => 'JSON',
        self::WAY_2 => 'FORM',
        self::WAY_3 => 'CURL',
    ];


    protected function list($params = [], $fields = ['*'], $limit = 0)
    {
        $this->limit =  $limit ?: $this->limit;
        return $this->select($fields)
            ->where(function ($query) use ($params) {
                if (isset($params['kwd']) && $params['kwd']) {
                    $query->where('title', 'like', '%' . $params['kwd'] . '%');
                }
                if (isset($params['status']) && $params['status']) {
                    $query->where('status', $params['status']);
                }
                if (isset($params['account']) && $params['account']) {
                    $query->where('account', $params['account']);
                }
            })
            ->orderBy('id', 'desc')
            ->paginate($this->limit);
    }




}
