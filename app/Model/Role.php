<?php

declare(strict_types=1);

namespace App\Model;



/**
 * @property int $id 
 * @property string $title 角色名称
 * @property string $remark 描述
 * @property array $rules 角色权限
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 */
class Role extends Base
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'roles';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'title', 'remark', 'rules', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'rules' => 'json', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    protected function list($params = [], $fields = ['*'], $limit = 0)
    {
        $this->limit =  $limit ?: $this->limit;
        return $this->select($fields)
            ->where(function ($query) use ($params) {
                if (isset($params['kwd']) && $params['kwd']) {
                    $query->where('title', 'like', '%' . $params['kwd'] . '%');
                }
            })
            ->orderBy('id')
            ->paginate($this->limit);
    }
}
