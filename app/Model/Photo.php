<?php

declare(strict_types=1);

namespace App\Model;



use Hyperf\Database\Model\Relations\Relation;

/**
 * @property int $id 
 * @property string $title 标题
 * @property string $thumb 封面
 * @property string $content 角色权限
 * @property int $type_id 类目id
 * @property int $show 人气数
 * @property int $status 状态1正常2锁定
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 * @property-read null|Type $type 
 */
class Photo extends Base
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'photos';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'title', 'thumb', 'content', 'type_id', 'show', 'status', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'type_id' => 'integer', 'show' => 'integer', 'status' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

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
                if (isset($params['type_id']) && $params['type_id']) {
                    $query->where('type_id', $params['type_id']);
                }
            })->with(['type' => function (Relation $relation) {
                $relation->getQuery()->select(['id', 'title']);
            }])
            ->orderBy('id', 'desc')
            ->paginate($this->limit);
    }
}
