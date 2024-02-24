<?php

declare(strict_types=1);

namespace App\Model;



/**
 * @property int $id 
 * @property int $sound_id 有声小说ID
 * @property string $title 章节标题
 * @property string $target 地址
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 */
class SoundChapter extends Base
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'sound_chapters';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'sound_id', 'title', 'target', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'sound_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    protected function list($cid = 0, $fields = ['*']){
        return  $this->where('sound_id', $cid)->get($fields);
    }
}
