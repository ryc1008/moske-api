<?php

declare(strict_types=1);

namespace App\Model;



use Hyperf\Database\Model\Relations\Relation;
use Qbhy\HyperfAuth\Authenticatable;

/**
 * @property int $id 
 * @property string $username 用户名
 * @property string $nickname 昵称
 * @property string $password 密码
 * @property string $name 联系人
 * @property string $contact 联系方式
 * @property string $bank 银行名称或者USDT
 * @property string $card 银行卡号或者钱包
 * @property string $login_ip IP地址
 * @property string $address IP解析地址
 * @property string $apk APK名称
 * @property string $user_agent UA信息
 * @property int $avatar 头像
 * @property int $agent_id 代理ID
 * @property string $balance 余额
 * @property int $deduct_day 日卡扣量
 * @property int $deduct_month 月卡扣量
 * @property int $deduct_half 半年卡扣量
 * @property int $deduct_year 年卡扣量
 * @property int $deduct_forever 终生卡扣量
 * @property int $deduct_reg 注册扣量
 * @property int $order_free 免单订单数
 * @property int $order_total 总订单数：总订单数>免单订单数则开始扣量
 * @property int $profit 分利：充值分成比例
 * @property int $rebate 佣金：代理分成比例
 * @property int $status 状态1正常2锁定
 * @property int $mobile 状态1电脑2手机
 * @property \Carbon\Carbon $login_at 最近登录
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 * @property-read null|Canal $agent
 */
class Canal extends Base implements Authenticatable
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'canals';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'username', 'nickname', 'password', 'name', 'contact', 'bank', 'card', 'login_ip', 'address', 'apk', 'user_agent', 'avatar', 'agent_id', 'balance', 'deduct_day', 'deduct_month', 'deduct_half', 'deduct_year', 'deduct_forever', 'deduct_reg', 'order_free', 'order_total', 'profit', 'rebate', 'status', 'mobile', 'login_at', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'avatar' => 'integer', 'agent_id' => 'integer', 'deduct_day' => 'integer', 'deduct_month' => 'integer', 'deduct_half' => 'integer', 'deduct_year' => 'integer', 'deduct_forever' => 'integer', 'deduct_reg' => 'integer', 'order_free' => 'integer', 'order_total' => 'integer', 'profit' => 'integer', 'rebate' => 'integer', 'status' => 'integer', 'mobile' => 'integer', 'login_at' => 'datetime', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    const MOBILE_1 = 1;
    const MOBILE_2 = 2;

    const MOBILE_TEXT = [
        self::MOBILE_1 => '电脑',
        self::MOBILE_2 => '手机',
    ];

    public function agent(){
        return $this->belongsTo(Canal::class,'agent_id', 'id');
    }

    protected function list($params = [], $fields = ['*'], $limit = 0)
    {
        $this->limit =  $limit ?: $this->limit;
        return $this->select($fields)
            ->where(function ($query) use ($params) {
                if (isset($params['kwd']) && $params['kwd']) {
                    $query->where('username', 'like', '%' . $params['kwd'] . '%')
                        ->orWhere('nickname', 'like', '%' . $params['kwd'] . '%')
                        ->orWhere('id', $params['kwd']);
                }
                if (isset($params['status']) && $params['status']) {
                    $query->where('status', $params['status']);
                }
                if (isset($params['agent_id']) && $params['agent_id']) {
                    $query->where('agent_id', $params['agent_id']);
                }
            })
//            ->with(['agent' => function (Relation $relation) {
//                $relation->getQuery()->select(['id', 'username']);
//            }])
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
