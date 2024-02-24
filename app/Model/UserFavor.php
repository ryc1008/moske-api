<?php

declare(strict_types=1);

namespace App\Model;



/**
 * @property int $id 
 * @property int $user_id 用户ID
 * @property int $good_id 资源ID
 * @property int $model 模块
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 */
class UserFavor extends Base
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'user_favors';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'user_id', 'good_id', 'model', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'user_id' => 'integer', 'good_id' => 'integer', 'model' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
