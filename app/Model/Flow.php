<?php

declare(strict_types=1);

namespace App\Model;



/**
 * @property int $id 
 * @property string $date 日期
 * @property int $agent_id 代理ID
 * @property int $canal_id 渠道ID
 * @property int $install_number 安装量
 * @property int $install_deduct 安装扣量
 * @property int $order_number 订单量
 * @property int $order_deduct 订单扣量
 * @property decimal:2 $rebate_agent 代理佣金
 * @property decimal:2 $rebate_canal 渠道佣金
 * @property decimal:2 $expend 结算金额
 * @property decimal:2 $profit 利润
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 */
class Flow extends Base
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'flows';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'date', 'agent_id', 'canal_id', 'install_number', 'install_deduct', 'order_number', 'order_deduct', 'rebate_agent', 'rebate_canal', 'expend', 'profit', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'agent_id' => 'integer', 'canal_id' => 'integer', 'install_number' => 'integer', 'install_deduct' => 'integer', 'order_number' => 'integer', 'order_deduct' => 'integer', 'rebate_agent' => 'decimal:2', 'rebate_canal' => 'decimal:2', 'expend' => 'decimal:2', 'profit' => 'decimal:2', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
