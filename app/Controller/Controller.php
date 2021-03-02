<?php

declare(strict_types = 1);

namespace App\Controller;
use Hyperf\Di\Annotation\Inject;
use Psr\Container\ContainerInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;

class Controller
{
    /**
     * @Inject
     *
     * @var ContainerInterface
     */
    protected $container;
    /**
     * @Inject
     *
     * @var RequestInterface
     */
    protected $request;
    /**
     * @Inject
     *
     * @var ResponseInterface
     */
    protected $response;
    /**
     * 请求成功
     *
     * @param        $data
     * @param string $message
     *
     * @return array
     */
    public function success($data, $message = 'success')
    {
        $code = $this->response->getStatusCode();
        return ['msg' => $message, 'code' => $code, 'data' => $data];
    }
    /**
     * 请求失败.
     *
     * @param string $message
     *
     * @return array
     */
    public function failed($message = 'Request format error!')
    {
        return ['msg' => $message, 'code' => 500, 'data' => ''];
    }
}