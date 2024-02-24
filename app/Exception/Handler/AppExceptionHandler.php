<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Exception\Handler;

use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class AppExceptionHandler extends ExceptionHandler
{
    public function __construct(protected StdoutLoggerInterface $logger)
    {
    }

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
//        $message = '[ERROR] '.$throwable->getMessage().'['.$throwable->getLine().'] in '.$throwable->getFile();
//        logger_write('app throwable', $message, 'error');
//        $this->logger->error(sprintf('%s[%s] in %s', $throwable->getMessage(), $throwable->getLine(), $throwable->getFile()));
//        $this->logger->error($throwable->getTraceAsString());
//        // 格式化输出
//        $data = json_encode([
//            'status' => $throwable->getCode(),
//            'data' => null,
//            'message' => '[ERROR] '.$throwable->getMessage().'['.$throwable->getLine().'] in '.$throwable->getFile(),
//        ], JSON_UNESCAPED_UNICODE);
//
//        // 阻止异常冒泡
////        $this->stopPropagation();
////        return $response->withAddedHeader('Content-Type', 'application/json')->withStatus($throwable->getCode())->withBody(new SwooleStream($data));
//        return $response->withHeader('Server', 'Hyperf')->withStatus(500)->withBody(new SwooleStream('Internal Server Error.'));
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
