<?php

declare(strict_types=1);

namespace App\Controller\App;

use App\Model\Advert;
use Hyperf\HttpServer\Contract\RequestInterface;


class AdvertController extends CommonController
{

    public function index(RequestInterface $request)
    {
        $tid = (int)$request->query('tid', 0);
        $info = Advert::where('type_id', $tid)->where('status', 1)->value('matter');
        return $this->returnJson(0, $info);
    }
}
