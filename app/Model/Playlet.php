<?php

declare(strict_types=1);

namespace App\Model;



use Hyperf\Database\Model\Relations\Relation;

/**
 * @property int $id 
 * @property string $title 标题
 * @property string $thumb 封面
 * @property string $target 地址
 * @property string $time 时长
 * @property int $money 价格
 * @property int $sale 销售量
 * @property int $type_id 类目id
 * @property int $show 人气数
 * @property int $favor 收藏数
 * @property int $hits 点赞数
 * @property int $status 状态1正常2推荐3锁定
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 * @property mixed $tag 标签
 * @property-read null|Type $type 
 */
class Playlet extends Base
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'playlets';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'title', 'thumb', 'target', 'time', 'tag', 'money', 'sale', 'type_id', 'show', 'favor', 'hits', 'status', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'money' => 'integer', 'sale' => 'integer', 'type_id' => 'integer', 'show' => 'integer', 'favor' => 'integer', 'hits' => 'integer', 'status' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    const STATUS_1 = 1;
    const STATUS_2 = 2;
    const STATUS_3 = 3;

    const STATUS_TEXT = [
        self::STATUS_1 => '正常',
        self::STATUS_2 => '推荐',
        self::STATUS_3 => '锁定',
    ];

    public function setTagAttribute($value)
    {
        $this->attributes['tag'] = implode('-', $value);
    }

    public function getTagAttribute($value)
    {
        return explode('-', $value);
    }

    public function type(){
        return $this->belongsTo(Type::class,'type_id', 'id');
    }

    protected function list($params = [], $fields = ['*'], $limit = 0)
    {
        $order = $this->orderSort($params);
        $this->limit =  $limit ?: $this->limit;
        return $this->select($fields)
            ->where(function ($query) use ($params) {
                if (isset($params['kwd']) && $params['kwd']) {
                    $query->where('title', 'like', '%' . $params['kwd'] . '%');
                }
                if (isset($params['status']) && $params['status']) {
                    $query->where('status', $params['status']);
                }
                if (isset($params['tag']) && $params['tag']) {
                    $query->where('tag', 'like', '%' . $params['tag'] . '%');
                }
                if (isset($params['type_id']) && $params['type_id']) {
                    $query->where('type_id', $params['type_id']);
                }
            })->with(['type' => function (Relation $relation) {
                $relation->getQuery()->select(['id', 'title']);
            }])
            ->orderBy($order['sort'], $order['by'])
            ->paginate($this->limit);
    }

    protected function app($params = [], $fields = ['*'], $limit = 8){
        return $this->select($fields)
            ->where(function ($query) use ($params) {
                if(isset($params['status']) && $params['status']){
                    $query->whereIn('status', $params['status']);
                }
                if(isset($params['type_id']) && $params['type_id']){
                    $query->whereIn('type_id', $params['type_id']);
                }
                if(isset($params['id']) && $params['id']){
                    $query->whereIn('id', $params['id']);
                }
            })->with(['type' => function (Relation $relation) {
                $relation->getQuery()->select(['id', 'title', 'icon']);
            }])->orderBy('id', 'desc')
            ->paginate($limit);
    }


}
