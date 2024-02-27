<?php

declare(strict_types=1);

namespace App\Controller\App;

use App\Model\Game;
use Hyperf\HttpServer\Contract\RequestInterface;


class GameController extends CommonController
{

    public function list(RequestInterface $request)
    {
        $tid = $request->query('tid');
        $params['status'] = Game::STATUS_1;
        $params['tid'] = $tid;
        $fields = ['id', 'name', 'url', 'icon'];
        $list = Game::app($params, $fields);
        return $this->returnJson(0, $list);
    }
}
