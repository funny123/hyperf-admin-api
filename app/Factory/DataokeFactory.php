<?php

declare(strict_types=1);

namespace App\Factory;

use Hyperf\Guzzle\ClientFactory;

class DataokeFactory
{
    /**
     * @var \Hyperf\Guzzle\ClientFactory
     */
    private $clientFactory;
    public $host = '';//请求地址
    public $appKey = '';//app_key
    public $appSecret = '';//app_secret
    public $version = '';//版本号
    private $http;

    public function __construct(ClientFactory $clientFactory)
    {
        $this->clientFactory = $clientFactory;
        // $options 等同于 GuzzleHttp\Client 构造函数的 $config 参数
        $options = [];
        // $client 为协程化的 GuzzleHttp\Client 对象
        $this->http = $this->clientFactory->create($options);
        $this->appKey = env('DTK_APPKEY');
        $this->appSecret = env('DTK_APPSECRET');
    }

    public function http()
    {
        return $this->http;
    }

    /**参数加密
     * @param $data
     * @param $appSecret
     * @return string
     */
    function makeSign($data, $appSecret)
    {
        ksort($data);
        $str = '';
        foreach ($data as $k => $v) {
            $str .= '&' . $k . '=' . $v;
        }
        $str = trim($str, '&');
        $sign = strtoupper(md5($str . '&key=' . $appSecret));
        return $sign;
    }

    /**拼多多进宝参数加密
     * @param $data
     * @param $appSecret
     * @return string
     */
    function makePddSign($params)
    {
        ksort($params);
        $str = '';
        foreach ($params as $k => $v) {
            $str .= $k . $v;
        }
        $str = env('PDD_CLIENT_SECRET') . $str . env('PDD_CLIENT_SECRET');
        return strtoupper(md5($str));
    }

    function request($params, $type = "GET")
    {
        $host = $this->host;
        $appKey = $this->appKey;
        $appSecret = $this->appSecret;
        $version = $this->version;
        if ($host == '' || $appKey == '' || $appSecret == '' || $version == '') {
            return json_encode(array('code' => -10001, 'msg' => "请完善参数"));
        }
        $type = strtoupper($type);
//        if (!in_array($type, array("GET", "POST"))) {
//            return json_encode(array('code' => -10001, 'msg' => "只支持GET/POST请求"));
//        }
        //默认必传参数
//        https://api.weixin.qq.com/sns/jscode2session?appid=wxf419d6d149bcfbe8&secret=029c8d7f52163f22e3b543a79629e6c1&js_code=041Gsn000OcwGL1NXY200lfxek2Gsn0N&grant_type=authorization_code
        $data = [
            'appKey' => $appKey,
            'version' => $version,
        ];
        //加密的参数
        $data = array_merge($params, $data);
        $data['sign'] = self::makeSign($data, $appSecret);
        if ($type == 'POST') {
            //执行请求获取数据
            $response = $this->http->post($host, $data);
            $res = $response->getBody()->getContents();
            $res = json_decode($res, true);
            return $res;
        } else {
            //拼接请求地址
            $url = $host . '?' . http_build_query($data);
            //执行请求获取数据
            $response = $this->http->get($url);
            $res = $response->getBody()->getContents();
            $res = json_decode($res, true);
            return $res;
        }
    }
    /**showapi参数加密
     * @param $data
     * @param $appSecret
     * @return string
     */
    function makeShowapiSign($params)
    {
        ksort($params);
        $str = '';
        foreach ($params as $k => $v) {
            $str .= $k . $v;
        }
        $str .= env('SHOWAPI_SECRET');
        return md5($str);
    }
}