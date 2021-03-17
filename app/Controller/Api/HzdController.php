<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\Controller;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Guzzle\ClientFactory;
/**
 * Note:
 * User: Marlon
 * Date: 2021/3/16
 * Time: 18:10
 * Class HzdController
 * @package App\Controller\Api
 * @AutoController()
 */
class HzdController extends Controller
{
    /**
     * @var \Hyperf\Guzzle\ClientFactory
     */
    private $clientFactory;

    public function __construct(ClientFactory $clientFactory)
    {
        $this->clientFactory = $clientFactory;
    }

    public function bar(RequestInterface $request, ResponseInterface $response)
    {

        // $options 等同于 GuzzleHttp\Client 构造函数的 $config 参数
        $options = [];
        // $client 为协程化的 GuzzleHttp\Client 对象
        $client = $this->clientFactory->create($options);
        $response1 = $client->get('https://v2.api.haodanku.com/itemlist/apikey/super/nav/3/cid/0/back/10/min_id/1');
        $data = $response1->getBody()->getContents();
        return $data;
    }
    public function index(RequestInterface $request, ResponseInterface $response)
    {
        return $response->raw('Hello Hzd8!');
    }
}
