<?php

declare(strict_types=1);

namespace App\Model;



/**
 * @property int $id 
 * @property int $comic_id 漫画ID
 * @property string $title 章节标题
 * @property array $content 内容
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 */
class ComicChapter extends Base
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'comic_chapters';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'comic_id', 'title', 'content', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'comic_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    protected function list($cid = 0, $fields = ['*']){
        return  $this->where('comic_id', $cid)->get($fields);
    }
}
