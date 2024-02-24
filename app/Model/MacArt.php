<?php

declare(strict_types=1);

namespace App\Model;



use Hyperf\Database\Model\Relations\Relation;

/**
 * @property int $id 
 * @property string $title 标题
 * @property string $thumb 封面
 * @property array $content 角色权限
 * @property int $type_id 类目id
 * @property int $show 人气数
 * @property int $status 状态1正常2锁定
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 */
class MacArt extends Base
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'art_82';

    protected ?string $connection = 'alibaba';


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
                    $query->where('type_id_1', $params['type_id']);
                }
            })
            ->orderBy('art_id', 'desc')
            ->paginate($this->limit);
    }
}
