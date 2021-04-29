<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\Controller;
use App\Factory\DataokeFactory;
use App\Task\MongoTask;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Utils\ApplicationContext;

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

    private $dataokeFactory;
    protected $request;
    protected $response;

    public function __construct(DataokeFactory $dataokeFactory,RequestInterface $request, ResponseInterface $response)
    {
        $this->dataokeFactory = $dataokeFactory;
        $this->request = $request;
        $this->response = $response;
    }

    public function index(RequestInterface $request, ResponseInterface $response)
    {
        $topicId = $this->request->input('name','marlon');
        return $response->raw('Hello'.$topicId);
    }
    public function hdk(){
        $httpClient = $this->dataokeFactory->http();
        $response = $httpClient->get('http://v2.api.haodanku.com/sales_list/apikey/super/sale_type/1');
        $res = $response->getBody()->getContents();
        $res = json_decode($res,true);
        return $res;
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
    public function mongo_test()
    {
        $client = ApplicationContext::getContainer()->get(MongoTask::class);
        $result = $client->query('hyperf.hdk', [], [
            'sort' => ['id' => -1],
            'limit' => 50,
        ]);
        return ['msg' => 'ok', 'code' => 200, 'data' => $result,'time' => time()];
    }

    /**
     * Note: 轮播图
     * User: Marlon
     * Date: 2021/4/14
     * Time: 15:52
     * @return false|mixed|string
     */
    public function carouse_list()
    {
        //接口地址 必填
        $this->dataokeFactory->host = 'https://openapi.dataoke.com/api/goods/topic/carouse-list';
        //版本号  必填
        $this->dataokeFactory->version = 'v2.0.0';
        //其他请求参数 根据接口文档需求选填
        $params = array();
        $res = $this->dataokeFactory->request($params);
        if (array_key_exists('data',$res)) {
            return $this->success($res['data']);
        } else {
            return $this->failed($res['msg']);
        }
    }

    /**
     * Note: 专题商品
     * User: Marlon
     * Date: 2021/4/14
     * Time: 15:58
     * @return false|mixed|string
     */
    public function topic_goods_list()
    {
        //接口地址 必填
        $this->dataokeFactory->host = 'https://openapi.dataoke.com/api/goods/topic/goods-list';
        //版本号  必填
        $this->dataokeFactory->version = 'v1.2.2';

        //其他请求参数 根据接口文档需求选填
        $topicId = $this->request->input('name','marlon');
        $params = array();
        $params['pageSize'] = 100;
        $params['pageId'] = 1;
        $params['topicId'] = $topicId;
        $request = $this->dataokeFactory->request($params);
        return $request;
    }

    /**
     * Note: 淘宝官方活动会场转链
     * User: Marlon
     * Date: 2021/4/14
     * Time: 16:22
     * @return false|mixed|string
     */
    public function activity_link()
    {
        //接口地址 必填
        $this->dataokeFactory->host = 'https://openapi.dataoke.com/api/tb-service/activity-link';
        //版本号  必填
        $this->dataokeFactory->version = 'v1.0.0';

        //其他请求参数 根据接口文档需求选填
        $promotionSceneId = $this->request->input('promotionSceneId');
        $params = array();
        $params['pageSize'] = 100;
        $params['pageId'] = 1;
        $params['promotionSceneId'] = $promotionSceneId;
        $request = $this->dataokeFactory->request($params);
        return $request;
    }

    /**
     * Note: 京东商品转链
     * User: Marlon
     * Date: 2021/4/15
     * Time: 21:35
     * @return false|mixed|string
     */
    public function promotion_union_convert()
    {
        //接口地址 必填
        $this->dataokeFactory->host = 'https://openapi.dataoke.com/api/dels/jd/kit/promotion-union-convert';
        //版本号  必填
        $this->dataokeFactory->version = 'v1.0.0';
        //其他请求参数 根据接口文档需求选填
        $materialId = $this->request->input('materialId');
        $unionId = $this->request->input('unionId','256717289');
        $params = array();
        $params['materialId'] = $materialId;
        $params['unionId'] = $unionId;
        $request = $this->dataokeFactory->request($params);
        return $request;
    }
}
