<?php

declare(strict_types=1);

namespace App\Model;



/**
 * @property int $id 
 * @property string $username 登录用户名
 * @property string $password 登录密码
 * @property string $login_ip IP地址
 * @property string $address IP解析地址
 * @property string $user_agent UA信息
 * @property string $remark 状态说明
 * @property int $mobile 状态1电脑2手机
 * @property int $status 状态1正常2异常
 * @property \Carbon\Carbon $login_at 登录时间
 */
class Safe extends Base
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'safes';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'username', 'password', 'login_ip', 'address', 'user_agent', 'remark', 'mobile', 'status', 'login_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'mobile' => 'integer', 'status' => 'integer', 'login_at' => 'datetime'];

    public bool $timestamps = false;

    const MOBILE_1 = 1;
    const MOBILE_2 = 2;

    const MOBILE_TEXT = [
        self::MOBILE_1 => '电脑',
        self::MOBILE_2 => '手机',
    ];

    const STATUS_TEXT = [
        self::STATUS_1 => '正常',
        self::STATUS_2 => '异常',
    ];

    protected function list($params = [], $fields = ['*'], $limit = 0)
    {
        $this->limit =  $limit ?: $this->limit;
        return $this->select($fields)
            ->where(function ($query) use ($params) {
                if (isset($params['kwd']) && $params['kwd']) {
                    $query->where('username', 'like', '%' . $params['kwd'] . '%')
                        ->orWhere('login_ip', 'like', '%' . $params['kwd'] . '%');
                }
                if (isset($params['status']) && $params['status']) {
                    $query->where('status', $params['status']);
                }
            })
            ->orderBy('id', 'desc')
            ->paginate($this->limit);
    }
}
