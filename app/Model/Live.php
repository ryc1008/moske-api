<?php

declare(strict_types=1);

namespace App\Model;



use Hyperf\Database\Model\Relations\Relation;

/**
 * @property int $id
 * @property string $name 名称
 * @property string $title
 * @property string $avatar 头像
 * @property string $target 地址
 * @property string $time 时长
 * @property string $hour 每日开播时间
 * @property int $type_id 类目id
 * @property int $show 人气数
 * @property int $hits 点赞数
 * @property int $work 直播：0未播1上播2下播
 * @property int $status 状态1正常2推荐3锁定
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 */
class Live extends Base
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'lives';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'name', 'title', 'avatar', 'target', 'time', 'hour', 'type_id', 'show', 'hits', 'work', 'status', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'type_id' => 'integer', 'show' => 'integer', 'hits' => 'integer', 'work' => 'integer', 'status' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    const STATUS_1 = 1;
    const STATUS_2 = 2;
    const STATUS_3 = 3;

    const STATUS_TEXT = [
        self::STATUS_1 => '正常',
        self::STATUS_2 => '推荐',
        self::STATUS_3 => '锁定',
    ];

    const WORK_0 = 0;
    const WORK_1 = 1;
    const WORK_2 = 2;

    const WORK_TEXT = [
        self::WORK_0 => '未播',//每天24点重置成0
        self::WORK_1 => '上播',
        self::WORK_2 => '下播',
    ];

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


    protected function app($params = [], $fields = ['*'], $limit = 8){
        return $this->select($fields)
            ->where(function ($query) use ($params) {
                if(isset($params['status']) && $params['status']){
                    $query->whereIn('status', $params['status']);
                }
                if(isset($params['tid']) && $params['tid']){
                    $query->where('type_id', $params['tid']);
                }
                if(isset($params['id']) && $params['id']){
                    $query->whereIn('id', $params['id']);
                }
            })->orderBy('id', 'desc')
            ->paginate($limit);
    }


}
