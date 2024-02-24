<?php
declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Qbhy\HyperfAuth\AuthMiddleware;
use Qbhy\HyperfAuth\Exception\AuthException;
use Qbhy\HyperfAuth\Exception\UnauthorizedException;

class AuthUserMiddleware extends AuthMiddleware
{
    protected array $guards = ['jwt:user'];

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        try {
            $guard = $this->auth->guard($this->guards[0]);
            if($guard->user()){
                return $handler->handle($request);
            }
            throw new UnauthorizedException('without authorization');
        } catch (AuthException $exception) {
            throw new UnauthorizedException($exception->getMessage());
        }
    }
}
