<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\Controller;
use App\Factory\DataokeFactory;
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
    private $dataokeFactory;

    public function __construct(ClientFactory $clientFactory, DataokeFactory $dataokeFactory)
    {
        $this->clientFactory = $clientFactory;
        $this->dataokeFactory = $dataokeFactory;
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
    public function test(RequestInterface $request, ResponseInterface $response)
    {
        //接口地址 必填
        $this->dataokeFactory->host = 'https://openapi.dataoke.com/api/goods/get-goods-list';

        //appKey  必填
        $this->dataokeFactory->appKey = '5e8f473ab9f33';

        //appSecret  必填
        $this->dataokeFactory->appSecret = '6863d83b63065f29826c820344eccacb';

        //版本号  必填
        $this->dataokeFactory->version = 'v1.2.4';

        //其他请求参数 根据接口文档需求选填
        $params = array();
        $params['pageSize'] = 100;
        $params['pageId'] = 1;
        $request = $this->dataokeFactory->request($params);
        return $request;
    }
}
