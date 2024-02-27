<?php

declare(strict_types=1);

namespace App\Controller\App;

use App\Model\Game;
use App\Model\User;
use App\Until\GameInterface;
use Hyperf\HttpServer\Contract\RequestInterface;


class GameController extends CommonController
{

    public function list(RequestInterface $request)
    {
        $tid = $request->query('tid');
        $params['status'] = Game::STATUS_1;
        $params['tid'] = $tid;
        $fields = ['id', 'name', 'url', 'icon', 'king_id'];
        $list = Game::app($params, $fields);
        return $this->returnJson(0, $list);
    }


    public function login(RequestInterface $request){
        $kingId = $request->query('kid', 0);//游戏ID
        $platform = $request->query('platform', '');//平台
        $user = $this->user();
        if(!$user['id']){
            return $this->returnJson(1, null, '未登录');
        }
//        $user = User::find(10000011);
        $header = $request->getHeaders();
        $ip = get_real_ip($header);
        $game = new GameInterface();
        $target = $game->login([
            'account' => $user['id'],
            'platform' => $platform,
            'king_id' => $kingId,
            'ip' => '58.11.11.84' //58.11.11.84 $ip
        ]);
        return $this->returnJson(0, $target);




    }
}
