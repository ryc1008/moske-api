<?php

declare(strict_types=1);

namespace App\Model;



use Hyperf\Database\Model\Relations\Relation;
use Qbhy\HyperfAuth\Authenticatable;

/**
 * @property int $id 
 * @property int $inviter_id 上级邀请者ID
 * @property string $uuid UUID
 * @property string $username 用户名
 * @property string $password 密码
 * @property string $mobile 手机号
 * @property string $code 邀请码
 * @property decimal:2 $balance 余额
 * @property decimal:2 $money 总充值
 * @property int $avatar 头像
 * @property int $canal_id 渠道ID
 * @property int $vip_id 会员ID
 * @property string $name 联系人
 * @property string $bank 银行名称或者USDT
 * @property string $card 银行卡号或者钱包
 * @property string $app_system 系统
 * @property string $app_vendor 品牌
 * @property string $app_version 版本号
 * @property string $app_model 手机型号
 * @property string $app_release 系统版本
 * @property string $app_network 网络状态
 * @property string $login_ip IP地址
 * @property string $address IP解析地址
 * @property int $free_num 每日免费观影数
 * @property int $status 状态1正常2锁定
 * @property \Carbon\Carbon $vip_at VIP到期
 * @property \Carbon\Carbon $login_at 最近登录
 * @property \Carbon\Carbon $created_at 注册日期
 * @property \Carbon\Carbon $updated_at 更新时间
 */
class User extends Base implements Authenticatable
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'users';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'inviter_id', 'uuid', 'username', 'password', 'mobile', 'code', 'balance', 'money', 'avatar', 'canal_id', 'vip_id', 'name', 'bank', 'card', 'app_system', 'app_vendor', 'app_version', 'app_model', 'app_release', 'app_network', 'login_ip', 'address', 'free_num', 'status', 'vip_at', 'login_at', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'inviter_id' => 'integer', 'balance' => 'decimal:2', 'money' => 'decimal:2', 'avatar' => 'integer', 'canal_id' => 'integer', 'vip_id' => 'integer', 'free_num' => 'integer', 'status' => 'integer', 'vip_at' => 'datetime', 'login_at' => 'datetime', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    const SYSTEM_TEXT = [
        'Android' => '安卓系统',
        'iOS' => '苹果系统',
    ];


//    public function canal(){
//        return $this->belongsTo(Canal::class,'canal_id', 'id');
//    }
//
//    public function vip(){
//        return $this->belongsTo(Vip::class,'vip_id', 'id');
//    }

    protected function list($params = [], $fields = ['*'], $limit = 0)
    {
        $this->limit =  $limit ?: $this->limit;
        return $this->select($fields)
            ->where(function ($query) use ($params) {
                if (isset($params['kwd']) && $params['kwd']) {
                    $query->where('username', 'like', '%' . $params['kwd'] . '%')
                        ->orWhere('id', $params['kwd'])->orWhere('mobile', $params['kwd']);
                }
                if (isset($params['status']) && $params['status']) {
                    $query->where('status', $params['status']);
                }
                if (isset($params['aid']) && $params['aid']) {
                    $query->where('canal_id', $params['aid']);
                }
                if (isset($params['system']) && $params['system']) {
                    $query->where('app_system', $params['system']);
                }
            })->with(['canal' => function (Relation $relation) {
                $relation->getQuery()->select(['id', 'username']);
            },'vip' => function (Relation $relation) {
                $relation->getQuery()->select(['id', 'title']);
            }])
            ->orderBy('id', 'desc')
            ->paginate($this->limit);
    }



















    public function getId()
    {
        return $this->getKey();
    }

    public static function retrieveById($key): ?Authenticatable
    {
        return self::find($key);
    }
}
