<?php

declare(strict_types=1);

namespace App\Model;



use Hyperf\Database\Model\Relations\Relation;

/**
 * @property int $id 
 * @property string $title 标题
 * @property string $thumb 封面
 * @property string $target 资源地址
 * @property string $quality 清晰度
 * @property string $time 时长
 * @property string $tag 标签
 * @property int $money 价格
 * @property int $sale 销售量
 * @property int $group_id 分组id
 * @property int $type_id 类目id
 * @property int $topic_id 专题id
 * @property int $show 人气数
 * @property int $hits 点赞数
 * @property int $status 状态1正常2推荐3锁定
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 */
//cast(json_extract(`tag`,'$.tag') as unsigned)
class Video extends Base
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'videos';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'title', 'thumb', 'target', 'quality', 'time', 'tag', 'money', 'sale', 'group_id', 'type_id', 'topic_id', 'show', 'hits', 'status', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'money' => 'integer', 'sale' => 'integer', 'group_id' => 'integer', 'type_id' => 'integer', 'topic_id' => 'integer', 'show' => 'integer', 'hits' => 'integer', 'status' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    const STATUS_1 = 1;
    const STATUS_2 = 2;
    const STATUS_3 = 3;

    const STATUS_TEXT = [
        self::STATUS_1 => '正常',
        self::STATUS_2 => '推荐',
        self::STATUS_3 => '锁定',
    ];

    const QUALITY_1 = 1;
    const QUALITY_2 = 2;
    const QUALITY_3 = 3;
    const QUALITY_4 = 4;

    const QUALITY_TEXT = [
        self::QUALITY_1 => '高清',
        self::QUALITY_2 => '超清',
        self::QUALITY_3 => '蓝光',
        self::QUALITY_4 => 'HD',
    ];

    public function setTagAttribute($value)
    {
        $this->attributes['tag'] = implode('-', $value);
    }

    public function getTagAttribute($value)
    {
        return explode('-', $value);
    }

    public function group(){
        return $this->belongsTo(Type::class,'group_id', 'id');
    }

    public function type(){
        return $this->belongsTo(Type::class,'type_id', 'id');
    }

    public function topic(){
        return $this->belongsTo(Type::class,'topic_id', 'id');
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
                if (isset($params['tag']) && $params['tag']) {
                    $query->where('tag', 'like', '%' . $params['tag'] . '%');
                }
                if (isset($params['group_id']) && $params['group_id']) {
                    $query->where('group_id', $params['group_id']);
                }
                if (isset($params['type_id']) && $params['type_id']) {
                    $query->where('type_id', $params['type_id']);
                }
                if (isset($params['topic_id']) && $params['topic_id']) {
                    $query->where('topic_id', $params['topic_id']);
                }
            })->with(['type' => function (Relation $relation) {
                $relation->getQuery()->select(['id', 'title']);
            },'group' => function (Relation $relation) {
                $relation->getQuery()->select(['id', 'title']);
            },'topic' => function (Relation $relation) {
                $relation->getQuery()->select(['id', 'title']);
            }])
            ->orderBy('id', 'desc')
            ->paginate($this->limit);
    }



}
