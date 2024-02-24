<?php

declare(strict_types=1);

namespace App\Model;



use Hyperf\Database\Model\Relations\Relation;

/**
 * @property int $id 
 * @property string $title 标题
 * @property string $thumb 封面
 * @property string $blurb 简介
 * @property int $type_id 类目id
 * @property int $show 阅读数
 * @property int $money 价格
 * @property int $sale 销售量
 * @property int $free 免费章节数
 * @property int $status 状态1正常2锁定
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 * @property-read null|Type $type 
 * @property-read null|\Hyperf\Database\Model\Collection|ComicChapter[] $chapter 章节数
 */
class Comic extends Base
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'comics';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'title', 'thumb', 'blurb', 'type_id', 'show', 'money', 'sale', 'chapter', 'free', 'status', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'type_id' => 'integer', 'show' => 'integer', 'money' => 'integer', 'sale' => 'integer', 'chapter' => 'integer', 'free' => 'integer', 'status' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function type(){
        return $this->belongsTo(Type::class,'type_id', 'id');
    }

    public function chapter(){
        return $this->hasMany(ComicChapter::class,'comic_id', 'id');
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
