<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\BaseController;
use App\Model\Manager;
use Hyperf\Database\Model\Relations\Relation;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;

#[Controller(prefix: "")]
class IndexController extends BaseController
{

    #[RequestMapping(path: "", methods: "get")]
    public function index()
    {
        try {
            $username = 'admin';
            $password = '123456';
//            $password = password_hash('123456', PASSWORD_BCRYPT);
            $user = Manager::query()->where('username', 'admin')->with(['role' => function (Relation $relation) {
                $relation->getQuery()->select(['id', 'title', 'rules']);
            }])->first();
//            $user->update(['password' => $password]);
//            $verify = password_verify($password, $user['password']);

            return $this->returnJson(0, [
                'user' => $user,
            ]);
        } catch (\Throwable $e) {
            return $this->returnJson(1, null, $e->getMessage());
        }
    }
}
