<?php

declare(strict_types=1);

namespace App\Model;


/**
 * @property int $id 
 * @property int $novel_id 长篇小说ID
 * @property string $title 章节标题
 * @property string $content 内容
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 */
class NovelChapter extends Base
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'novel_chapters';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'novel_id', 'title', 'content', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'novel_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];


    protected function list($nid = 0, $fields = ['*']){
        return  $this->where('novel_id', $nid)->get($fields);
    }

}
