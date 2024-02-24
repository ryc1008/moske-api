<?php

declare(strict_types=1);

namespace App\Controller;


class BaseController extends AbstractController
{
    /**
     * @param $status 0 成功 1失败 2异常
     * @param $data
     * @param $message
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function returnJson($status = 0, $data = null, $message = null){
        return $this->response->json([
            "status" => $status,
            "data" => $data,//encrypt_data($data)
            "message" => $message ?? 'success'
        ]);
    }
}
