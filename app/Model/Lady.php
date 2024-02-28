<?php

declare(strict_types=1);

namespace App\Model;



use Hyperf\Database\Model\Relations\Relation;

/**
 * @property int $id 
 * @property string $title 标题
 * @property string $thumb 封面
 * @property string $content 图集
 * @property string $project 项目
 * @property string $time 营业时间
 * @property string $price 价格
 * @property string $age 年龄
 * @property string $number 数量
 * @property string $blurb 介绍
 * @property string $province 省份
 * @property string $city 城市
 * @property int $money 价格
 * @property int $sale 销售量
 * @property int $type_id 类目id
 * @property int $show 人气数
 * @property int $favor 收藏数
 * @property int $status 状态1正常2推荐3锁定
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 */
class Lady extends Base
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'ladys';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'title', 'thumb', 'content', 'project', 'time', 'price', 'age', 'number', 'blurb', 'province', 'city', 'money', 'sale', 'type_id', 'show', 'favor', 'status', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'money' => 'integer', 'sale' => 'integer', 'type_id' => 'integer', 'show' => 'integer', 'favor' => 'integer', 'status' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    const STATUS_1 = 1;
    const STATUS_2 = 2;
    const STATUS_3 = 3;

    const STATUS_TEXT = [
        self::STATUS_1 => '正常',
        self::STATUS_2 => '推荐',
        self::STATUS_3 => '锁定',
    ];

    const PROJECT_TEXT = ['全套', '口活', '胸推', '毒龙', '69式', '莞式', '鸳鸯浴', '爱爱', 'SM', '漫游', '制服', '指滑', '深喉', '冰火'];
    const TAG_TEXT = ['外围', '学生', '制服', '空姐', '萝莉', '车模', '少妇', '白领', '网红'];

    public function type(){
        return $this->belongsTo(Type::class,'type_id', 'id');
    }

    protected function list($params = [], $fields = ['*'], $limit = 0)
    {
        $this->limit =  $limit ?: $this->limit;
        return $this->select($fields)
            ->where(function ($query) use ($params) {
                if (isset($params['kwd']) && $params['kwd']) {
                    $query->where('title', 'like', '%' . $params['kwd'] . '%')
                        ->orWhere('province', 'like', '%' . $params['kwd'] . '%')
                        ->orWhere('city', 'like', '%' . $params['kwd'] . '%');
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
                if(isset($params['city']) && $params['city']){
                    $query->where('city', $params['city']);
                }
            })->orderBy($params['sort'], 'desc')
            ->paginate($limit);
    }
}
