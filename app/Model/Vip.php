<?php

declare(strict_types=1);

namespace App\Model;



/**
 * @property int $id 
 * @property string $name 名称
 * @property decimal:2 $money 价格
 * @property decimal:2 $price 原价格
 * @property int $diamond 钻石
 * @property int $give 赠送钻石
 * @property string $title 标题
 * @property string $time 时间
 * @property string $welfare 福利
 * @property int $status 状态1正常2锁定
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 */
class Vip extends Base
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'vips';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'name', 'money', 'price', 'diamond', 'give', 'title', 'time', 'welfare', 'status', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'money' => 'decimal:2', 'price' => 'decimal:2', 'diamond' => 'integer', 'give' => 'integer', 'status' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];



    protected function list($params = [], $fields = ['*'], $limit = 0)
    {
        $this->limit =  $limit ?: $this->limit;
        return $this->select($fields)
            ->where(function ($query) use ($params) {
                if (isset($params['kwd']) && $params['kwd']) {
                    $query->where('name', 'like', '%' . $params['kwd'] . '%')->orWhere('title', 'like', '%' . $params['kwd'] . '%');
                }
                if (isset($params['status']) && $params['status']) {
                    $query->where('status', $params['status']);
                }
            })
            ->orderBy('id')
            ->paginate($this->limit);
    }


    protected function app($params = [], $fields = ['*']){
        return $this->where(function ($query) use ($params) {
                if (isset($params['title']) && $params['title']) {
                    $query->where('title', '<>', '');
                }
                if (isset($params['status']) && $params['status']) {
                    $query->where('status', $params['status']);
                }
                if (isset($params['diamond']) && $params['diamond']) {
                    $query->where('diamond', '<>', '');
                }
            })->orderBy('id')
            ->get($fields);
    }
}
