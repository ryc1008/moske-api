<?php

declare(strict_types=1);

namespace App\Model;


use Hyperf\Database\Model\Relations\Relation;
use Qbhy\HyperfAuth\Authenticatable;

/**
 * @property int $id 
 * @property string $username 用户名
 * @property string $password 密码
 * @property string $nickname 昵称
 * @property int $avatar 头像
 * @property int $role_id 角色id
 * @property int $status 状态1正常2锁定
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 * @property-read null|Role $role 
 */
class Manager extends Base implements Authenticatable
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'managers';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'username', 'password', 'nickname', 'avatar', 'role_id', 'status', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'avatar' => 'integer', 'role_id' => 'integer', 'status' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];


    public function role(){
        return $this->belongsTo(Role::class,'role_id', 'id');
    }

    protected function list($params = [], $fields = ['*'], $limit = 0)
    {
        $this->limit =  $limit ?: $this->limit;
        return $this->select($fields)
            ->where(function ($query) use ($params) {
                if (isset($params['kwd']) && $params['kwd']) {
                    $query->where('username', 'like', '%' . $params['kwd'] . '%')
                        ->orWhere('nickname', 'like', '%' . $params['kwd'] . '%');
                }
                if (isset($params['status']) && $params['status']) {
                    $query->where('status', $params['status']);
                }
                if (isset($params['role_id']) && $params['role_id']) {
                    $query->where('role_id', $params['role_id']);
                }
            })->with(['role' => function (Relation $relation) {
                $relation->getQuery()->select(['id', 'title', 'rules']);
            }])
            ->orderBy('id', 'desc')
            ->paginate($this->limit);
    }






    public function getId()
    {
        return $this->getKey();
    }

    public static function retrieveById($key): ?Authenticatable
    {
        return self::find($key);
    }
}
