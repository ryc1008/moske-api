<?php

declare(strict_types=1);

namespace App\Controller\App;

use App\Model\Advert;
use App\Model\Type;
use Hyperf\HttpServer\Contract\RequestInterface;


class TypeController extends CommonController
{

    public function list(RequestInterface $request)
    {
        $pid = (int)$request->query('pid', 0);
        $pluck = Type::where('parent_id', $pid)
            ->where('status', Type::STATUS_1)
            ->orderBy('sort')
            ->get(['title as name', 'id', 'icon']);
        return $this->returnJson(0, $pluck);
    }

    public function forum(){
        //这里写死吧
        return $this->returnJson(0, [
            ['id' => 'lady',  'name' => '楼凤'],
            ['id' => 'story', 'name' => '小说'],
            ['id' => 'photo', 'name' => '套图'],
            ['id' => 'comic', 'name' => '漫画'],
            ['id' => 'novel', 'name' => '长篇'],
            ['id' => 'sound', 'name' => '有声'],
        ]);
    }
}
