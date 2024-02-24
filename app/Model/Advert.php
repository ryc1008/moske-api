<?php

declare(strict_types=1);

namespace App\Model;



use Hyperf\Database\Model\Relations\Relation;

/**
 * @property int $id 
 * @property string $title 名称
 * @property string $size 尺寸
 * @property array $matter 物料
 * @property int $group_id 应用ID
 * @property int $type_id 位置ID
 * @property int $status 状态：1正常2锁定
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 */
class Advert extends Base
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'adverts';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'title', 'size', 'matter', 'group_id', 'type_id', 'status', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'matter' => 'json', 'group_id' => 'integer', 'type_id' => 'integer', 'status' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function group(){
        return $this->belongsTo(Type::class,'group_id', 'id');
    }

    public function type(){
        return $this->belongsTo(Type::class,'type_id', 'id');
    }

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
                if (isset($params['group']) && $params['group']) {
                    $query->where('group_id', $params['group']);
                }
            })->with(['group', 'type'])
            ->orderBy('id')
            ->paginate($this->limit);
    }

}
