<?php

declare(strict_types=1);

namespace App\Model;



/**
 * @property int $id 
 * @property string $title 名称
 * @property string $icon 图标
 * @property int $parent_id 上级id
 * @property int $sort 排序
 * @property int $status 状态：1正常2锁定
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 */
class Type extends Base
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'types';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'title', 'icon', 'parent_id', 'sort', 'status', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'parent_id' => 'integer', 'sort' => 'integer', 'status' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];


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

    protected function recursion($id = 0, $fields = ['*']){
        $data = $this->where('parent_id', $id)
            ->where('status', self::STATUS_1)
            ->orderBy('sort')
            ->get($fields)
            ->toArray();
        if(count($data)){
            foreach ($data as &$item){
                $child = $this->where('parent_id', $item['id'])->orderBy('sort')->get($fields)->toArray();
                $item['child'] = $child;
            }
        }
        return $data;
    }

}
