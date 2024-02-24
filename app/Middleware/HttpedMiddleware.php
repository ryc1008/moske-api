<?php

declare(strict_types=1);

namespace App\Middleware;

use Hyperf\Context\Context;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class HttpedMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

        try {
            return $handler->handle($request);
        } catch (\Throwable $e) {
            logger_write('http_throwable', [str_replace(BASE_PATH, "", $e->getMessage()), '[' . $request->getMethod() . '] ' . $request->getUri(), str_replace(BASE_PATH, "", $e->getFile()), $e->getLine()], 'error', 'http');
            $response = Context::get(ResponseInterface::class);
            $xRequestedWith = $request->getHeader('X-Requested-With');
            if (is_array($xRequestedWith) && count($xRequestedWith) && $xRequestedWith[0] == 'XMLHttpRequest') {
                $data = json_encode([
                    'status' => 1,
                    'data' => null,
                    'message' => 'Internal Server Error.',
                ], JSON_UNESCAPED_UNICODE);
                return $response->withAddedHeader('Content-Type', 'application/json')
                    ->withStatus($e->getCode())
                    ->withBody(new SwooleStream($data));
            }
            return $response->withAddedHeader('content-type', 'text/html; charset=utf-8')->withStatus($e->getCode())
                ->withBody(new SwooleStream($this->html()));
        }
        return $handler->handle($request);
    }


    protected function html()
    {
        return '<html>
<head>
    <title>错误</title>
    <meta name="description" content="">
    <meta name="keyword" content="">
    <meta http-equiv=”pragma” content=”no-cache” />
    <meta http-equiv=X-UA-Compatible content="IE=edge">
    <meta name=viewport content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no">
    <style>
        body{
            box-sizing: border-box;
            -moz-box-sizing: border-box;
            -webkit-box-sizing: border-box;
            padding: 0;margin: 0;
            width: 100%;
            height: 100%;
            color: #fff;
            background: #05084B;
        }
        .container{
            margin: auto;width: 450px;text-align: center;
        }
        .image{
            width: 450px;margin-top: calc(10vh);
        }
        .message{
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="http://moske.go/image/404.png" class="image" alt="">
        <div class="message">Internal Server Error.</div>
    </div>
</body>
</html>';
    }
}