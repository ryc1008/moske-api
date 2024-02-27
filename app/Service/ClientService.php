<?php
declare(strict_types=1);

namespace App\Service;

use Hyperf\Context\ApplicationContext;
use Hyperf\Guzzle\ClientFactory;

class ClientService
{
    public static function __callStatic($func, $arguments)
    {
        $container = ApplicationContext::getContainer();
        $method = $arguments[2] ?? 'GET';
        $url = $arguments[0] ?? '';
        $params = $arguments[1] ?? [];
        $options = ['base_uri' => $url];
        $container->get(ClientFactory::class)->create($options)->$func($method, $url, $params);
    }
}