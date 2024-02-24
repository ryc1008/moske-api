<?php

declare(strict_types=1);

namespace App\Model;



/**
 * @property int $id 
 * @property string $name 名称
 */
class Tag extends Base
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'tags';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'name'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer'];

    public bool $timestamps = false;

    protected function list($params = [], $fields = ['*']){
        return  $this->where(function ($query) use ($params) {
            if (isset($params['kwd']) && $params['kwd']) {
                $query->where('name', 'like', '%' . $params['kwd'] . '%');
            }
        })->orderBy('id')->get($fields)->toArray();
    }
}
