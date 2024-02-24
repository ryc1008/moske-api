<?php

declare(strict_types=1);

namespace App\Controller\App;

use App\Model\Advert;
use App\Model\Type;
use Hyperf\HttpServer\Contract\RequestInterface;


class TypeController extends CommonController
{

    public function index(RequestInterface $request)
    {
        $pid = (int)$request->query('pid', 0);
        $pluck = Type::where('parent_id', $pid)
            ->where('status', 1)
            ->orderBy('sort')->get(['title as name', 'id']);
        return $this->returnJson(0, $pluck);
    }
}
