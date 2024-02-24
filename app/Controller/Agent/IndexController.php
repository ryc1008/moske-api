<?php

declare(strict_types=1);

namespace App\Controller\Agent;

use App\Controller\AbstractController;
use function Hyperf\ViewEngine\view;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;

#[Controller(prefix: "")]
class IndexController extends AbstractController
{
    #[RequestMapping(path: "", methods: "get")]
    public function index()
    {
        $user = $this->request->input('user', 'Hyperf');
        $method = $this->request->getMethod();
//        $Captcha = config('captcha.characters');
//        var_dump($Captcha);

        return view('home.main.index', compact('user', 'method'));
    }
}
