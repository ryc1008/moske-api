<?php

declare(strict_types=1);

namespace App\Model;



/**
 * @property int $id 
 * @property string $name 权限名称
 * @property string $mark 权限标识
 * @property string $access 权限节点
 * @property int $parent_id 上级id
 * @property int $sort 排序
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 */
class Authen extends Base
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'authens';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'name', 'mark', 'access', 'parent_id', 'sort', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'parent_id' => 'integer', 'sort' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];


    protected function list($params = [], $fields = ['*'])
    {
        return $this->where(function ($query) use ($params) {
                if (isset($params['id']) && $params['id']) {
                    $query->where('id', '>', $params['id']);
                }
            })
            ->orderBy('sort')
            ->get($fields)
            ->toArray();
    }








}
