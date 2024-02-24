<?php

declare(strict_types=1);

namespace App\Model;



/**
 * @property int $id 
 * @property string $title 标题
 * @property string $name 标识
 * @property string $value 默认值
 * @property string $remark 说明
 * @property string $unit 单位
 * @property string $icon fontawesome图标
 * @property string $type 类型
 * @property string $rule 规则
 * @property int $parent_id 上级ID
 * @property int $status 状态：1正常2锁定
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 * @property-read null|Type $group 
 */
class Setting extends Base
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'settings';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'title', 'name', 'value', 'remark', 'unit', 'icon', 'type', 'rule', 'parent_id', 'status', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'parent_id' => 'integer', 'status' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    const TYPE_1 = 'number';
    const TYPE_2 = 'string';
    const TYPE_3 = 'textarea';
    const TYPE_4 = 'upload';
    const TYPE_5 = 'boole';
    const TYPE_6 = 'select';
    const TYPE_7 = 'transfer';


    const TYPE_TEXT = [
        self::TYPE_1 => '数字',
        self::TYPE_2 => '字符',
        self::TYPE_3 => '文本',
        self::TYPE_4 => '上传',
        self::TYPE_5 => '布尔',
        self::TYPE_6 => '选择',
        self::TYPE_7 => '穿梭',
    ];

    protected function list($params = [],$fields = ['*'])
    {
        return $this->where(function ($query) use ($params) {
                if (isset($params['id']) && $params['id']) {
                    $query->where('id', '>', $params['id']);
                }
            })->orderBy('id')
            ->get($fields)
            ->toArray();
    }

}
