<?php
declare(strict_types=1);

namespace App\Middleware;

use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Container\ContainerInterface;
use Qbhy\HyperfAuth\AuthMiddleware;
use Qbhy\HyperfAuth\Exception\AuthException;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;
use Qbhy\HyperfAuth\Exception\UnauthorizedException;
use Hyperf\HttpServer\Contract\RequestInterface;

class AuthAdminMiddleware extends AuthMiddleware
{
    protected array $guards = ['jwt:manager'];

    protected ContainerInterface $container;
    protected RequestInterface $request;
    protected HttpResponse $response;

    public function __construct(ContainerInterface $container, HttpResponse $response, RequestInterface $request)
    {
        $this->container = $container;
        $this->response = $response;
        $this->request = $request;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        try {
            $guard = $this->auth->guard($this->guards[0]);
            $user = $guard->user();
            if($user){
                $path = $this->request->getPathInfo();
                $path = explode('/', trim($path, '/'));
                //操作日志
//                $handle = ['*.update', '*.active', '*.lock', '*.destroy'];
//                if(in_array('*.'.$path[2], $handle)){
//                    logger_write('$handle', $this->request->all());
//                }
                //超级管理员拥有所有权限
                if($user['role_id'] == 1){
                    return $handler->handle($request);
                }
                $rules = $user->role->rules;
                $except = [
                    'manager.user', '*.config', '*.tree'
                ];
                if(in_array($path[1].'.'.$path[2], $except) || in_array('*.'.$path[2], $except)){
                    return $handler->handle($request);
                }
                if(in_array($path[1].'.'.$path[2], $rules)){
                    return $handler->handle($request);
                }
                return $this->response->json([
                    "status" => 1,
                    "data" => null,
                    "message" => '抱歉，您的权限不足'
                ]);
            }
            throw new UnauthorizedException('without authorization');
        } catch (AuthException $exception) {
            throw new UnauthorizedException($exception->getMessage());
        }
    }
}
