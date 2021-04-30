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
 * Class PddController
 * @package App\Controller\Api
 * @AutoController()
 */
class PddController extends Controller
{
    /**
     * @var \Hyperf\Guzzle\ClientFactory
     */

    private $dataokeFactory;
    protected $request;
    protected $response;

    public function __construct(DataokeFactory $dataokeFactory, RequestInterface $request, ResponseInterface $response)
    {
        $this->dataokeFactory = $dataokeFactory;
        $this->request = $request;
        $this->response = $response;
    }

    public function index(RequestInterface $request, ResponseInterface $response)
    {
        $topicId = $this->request->input('name', 'marlon');
        return $response->raw('Hello' . $topicId);
    }

    /**
     * Note: pdd.ddk.goods.recommend.get多多进宝商品推荐API
     * User: Marlon
     * Date: 2021/4/15
     * Time: 21:35
     * @return false|mixed|string
     */
    public function pdd_recommend_get()
    {
        $params = array();
        $params['type'] = 'pdd.ddk.goods.recommend.get';
        $params['client_id'] = env('PDD_CLIENT_ID');
        $params['timestamp'] = time();
//        $params['cat_id'] = 20200;
        $sign = $this->dataokeFactory->makePddSign($params);
        $params['sign'] = $sign;
        $params['json'] = $params;
        $httpClient = $this->dataokeFactory->http();
        $response = $httpClient->request('POST', 'https://gw-api.pinduoduo.com/api/router', $params);
        $res = $response->getBody()->getContents();
        $res = json_decode($res, true);
        return $res;
    }

    /**
     * Note: pdd.ddk.goods.promotion.url.generate多多进宝推广链接生成
     * User: Marlon
     * Date: 2021/4/15
     * Time: 21:35
     * @return false|mixed|string
     */
    public function pdd_promotion_url_generate()
    {
        $params = array();
        $params['type'] = 'pdd.ddk.goods.promotion.url.generate';
        $params['client_id'] = env('PDD_CLIENT_ID');
        $params['timestamp'] = time();
        $params['p_id'] = '8141691_204492888';
        if ($this->request->input('goods_sign')) {
            $params['goods_sign'] = $this->request->input('goods_sign');
        } else {
            return $this->failed('goods_sign不能为空');
        }
        if ($this->request->input('search_id')) {
            $params['search_id'] = $this->request->input('search_id');
        }
        $params['generate_we_app'] = 'true';
        $params['generate_authority_url'] = 'true';
        $sign = $this->dataokeFactory->makePddSign($params);
        $params['sign'] = $sign;
        $params['json'] = $params;
        $httpClient = $this->dataokeFactory->http();
        $response = $httpClient->post('https://gw-api.pinduoduo.com/api/router', $params);
        $res = $response->getBody()->getContents();
        $res = json_decode($res, true);
        if (array_key_exists('goods_promotion_url_generate_response', $res)) {
            return $this->success($res['goods_promotion_url_generate_response']['goods_promotion_url_list']);
        } else {
            return $this->failed($res['error_response']['error_msg']);
        }
    }

    /**
     * Note: pdd.ddk.goods.pid.query查询已经生成的推广位信息
     * User: Marlon
     * Date: 2021/4/15
     * Time: 21:35
     * @return false|mixed|string
     */
    public function pdd_pid_query()
    {
        $params = array();
        $params['type'] = 'pdd.ddk.goods.pid.query';
        $params['client_id'] = env('PDD_CLIENT_ID');
        $params['timestamp'] = time();
        $sign = $this->dataokeFactory->makePddSign($params);
        $params['sign'] = $sign;
        $params['json'] = $params;
        $httpClient = $this->dataokeFactory->http();
        $response = $httpClient->post('https://gw-api.pinduoduo.com/api/router', $params);
        $res = $response->getBody()->getContents();
        $res = json_decode($res, true);
        return $res;
    }

    /**
     * Note: pdd.goods.cats.get商品标准类目接口
     * User: Marlon
     * Date: 2021/4/15
     * Time: 21:35
     * @return false|mixed|string
     */
    public function pdd_cats_get()
    {
        $params = array();
        $params['type'] = 'pdd.goods.cats.get';
        $params['client_id'] = env('PDD_CLIENT_ID');
        $params['timestamp'] = time();
        $params['parent_cat_id'] = '0';
        $sign = $this->dataokeFactory->makePddSign($params);
        $params['sign'] = $sign;
        $params['json'] = $params;
        $httpClient = $this->dataokeFactory->http();
        $response = $httpClient->post('https://gw-api.pinduoduo.com/api/router', $params);
        $res = $response->getBody()->getContents();
        $res = json_decode($res, true);
        return $res;
    }

    /**
     * Note: pdd.goods.opt.get查询商品标签列表
     * User: Marlon
     * Date: 2021/4/15
     * Time: 21:35
     * @return false|mixed|string
     */
    public function pdd_opt_get()
    {
        $params = array();
        $params['type'] = 'pdd.goods.opt.get';
        $params['client_id'] = env('PDD_CLIENT_ID');
        $params['timestamp'] = time();
        $params['parent_opt_id'] = '0';
        $sign = $this->dataokeFactory->makePddSign($params);
        $params['sign'] = $sign;
        $params['json'] = $params;
        $httpClient = $this->dataokeFactory->http();
        $response = $httpClient->post('https://gw-api.pinduoduo.com/api/router', $params);
        $res = $response->getBody()->getContents();
        $res = json_decode($res, true);
        if (array_key_exists('goods_opt_get_response', $res)) {
            return $this->success($res['goods_opt_get_response']['goods_opt_list']);
        } else {
            return $this->failed($res['error_response']['error_msg']);
        }

    }

    /**
     * Note: pdd.ddk.goods.search多多进宝商品查询
     * User: Marlon
     * Date: 2021/4/15
     * Time: 21:35
     * @return false|mixed|string
     */
    public function pdd_goods_search()
    {
        $params = array();
        $params['type'] = 'pdd.ddk.goods.search';
        $params['client_id'] = env('PDD_CLIENT_ID');
        $params['timestamp'] = time();
        $params['pid'] = '8141691_204492888';
//        $params['custom_parameters'] = '{"uid":"11111","sid":"22222"}';
//        $params['keyword'] = '饺子神器';
        $params['activity_tags'] = json_encode([10584]);
        $params['opt_id'] = $this->request->input('opt_id', '23010');
        $params['page'] = $this->request->input('page', 1);
        $params['page_size'] = 20;
        if ($this->request->input('list_id')) {
            $params['list_id'] = $this->request->input('list_id');
        }
        $sign = $this->dataokeFactory->makePddSign($params);
        $params['sign'] = $sign;
        $params['json'] = $params;
        $httpClient = $this->dataokeFactory->http();
        $response = $httpClient->post('https://gw-api.pinduoduo.com/api/router', $params);
        $res = $response->getBody()->getContents();
        $res = json_decode($res, true);
        if (array_key_exists('goods_search_response', $res)) {
            return $this->success($res['goods_search_response']);
        } else {
            return $this->failed($res['error_response']['error_msg']);
        }
    }

    /**
     * Note: pdd.ddk.cms.prom.url.generate生成商城-频道推广链接
     * User: Marlon
     * Date: 2021/4/15
     * Time: 21:35
     * @return false|mixed|string
     */
    public function pdd_prom_url_generate()
    {
        $params = array();
        $params['type'] = 'pdd.ddk.cms.prom.url.generate';
        $params['client_id'] = env('PDD_CLIENT_ID');
        $params['timestamp'] = time();
        $params['p_id_list'] = json_encode(["8141691_204492888"]);
//        $params['keyword'] = '饺子神器';
        $params['channel_type'] = '0';
        $params['generate_we_app'] = 'true';
        $sign = $this->dataokeFactory->makePddSign($params);
        $params['sign'] = $sign;
        $params['json'] = $params;
        $httpClient = $this->dataokeFactory->http();
        $response = $httpClient->post('https://gw-api.pinduoduo.com/api/router', $params);
        $res = $response->getBody()->getContents();
        $res = json_decode($res, true);
        return $res;
    }

    /**
     * Note: pdd.ddk.goods.detail多多进宝商品详情查询
     * User: Marlon
     * Date: 2021/4/29
     * Time: 21:35
     * @return false|mixed|string
     */
    public function pdd_ddk_goods_detail()
    {
        $params = array();
        $params['type'] = 'pdd.ddk.goods.detail';
        $params['client_id'] = env('PDD_CLIENT_ID');
        $params['timestamp'] = time();
        $params['pid'] = "8141691_204492888";
        if ($this->request->input('search_id')) {
            $params['search_id'] = $this->request->input('search_id');
        }
        if ($this->request->input('goods_sign')) {
            $params['goods_sign'] = $this->request->input('goods_sign');
        } else {
            return $this->failed('goods_sign不能为空');
        }
        $sign = $this->dataokeFactory->makePddSign($params);
        $params['sign'] = $sign;
        $params['json'] = $params;
        $httpClient = $this->dataokeFactory->http();
        $response = $httpClient->post('https://gw-api.pinduoduo.com/api/router', $params);
        $res = $response->getBody()->getContents();
        $res = json_decode($res, true);
        if (array_key_exists('goods_detail_response', $res)) {
            return $this->success($res['goods_detail_response']['goods_details'][0]);
        } else {
            return $this->failed($res['error_response']['error_msg']);
        }
    }
}
