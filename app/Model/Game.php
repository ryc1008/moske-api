<?php

declare(strict_types=1);

namespace App\Model;



use Hyperf\Database\Model\Relations\Relation;

/**
 * @property int $id 
 * @property string $name 名称
 * @property string $url 链接
 * @property string $icon 图标
 * @property int $kind_id 开元KindID
 * @property int $type_id 类目ID
 * @property int $status 状态：1正常2锁定
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 * @property-read null|Type $type 
 */
class Game extends Base
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'games';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'name', 'url', 'icon', 'kind_id', 'type_id', 'status', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'kind_id' => 'integer', 'type_id' => 'integer', 'status' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function type(){
        return $this->belongsTo(Type::class,'type_id', 'id');
    }

    protected function list($params = [], $fields = ['*'], $limit = 0)
    {
        $this->limit =  $limit ?: $this->limit;
        return $this->select($fields)
            ->where(function ($query) use ($params) {
                if (isset($params['kwd']) && $params['kwd']) {
                    $query->where('name', 'like', '%' . $params['kwd'] . '%');
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

    protected function app($params = [], $fields = ['*']){
        return $this->select($fields)
            ->where(function ($query) use ($params) {
                if(isset($params['status']) && $params['status']){
                    $query->where('status', $params['status']);
                }
                if(isset($params['tid']) && $params['tid']){
                    $query->where('type_id', $params['tid']);
                }
            })->orderBy('id', 'desc')
            ->get();
    }

}
