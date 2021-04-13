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
    public $host='';//请求地址
    public $appKey='';//app_key
    public $appSecret='';//app_secret
    public $version='';//版本号
    private $http;

    public function __construct(ClientFactory $clientFactory)
    {
        $this->clientFactory = $clientFactory;
        // $options 等同于 GuzzleHttp\Client 构造函数的 $config 参数
        $options = [];
        // $client 为协程化的 GuzzleHttp\Client 对象
        $this->http = $this->clientFactory->create($options);
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
    function request($params,$type="GET"){
        $host = $this->host;
        $appKey = $this->appKey;
        $appSecret = $this->appSecret;
        $version = $this->version;
        if($host=='' || $appKey=='' || $appSecret == '' || $version==''){
            return json_encode(array('code'=>-10001,'msg'=>"请完善参数"));
        }
        $type = strtoupper($type);
        if(!in_array($type,array("GET","POST"))){
            return json_encode(array('code'=>-10001,'msg'=>"只支持GET/POST请求"));
        }
        //默认必传参数
        $data = [
            'appKey' => $appKey,
            'version' => $version,
        ];
        //加密的参数
        $data = array_merge($params, $data);
        $data['sign'] = self::makeSign($data, $appSecret);
            if($type == 'POST') {
                //执行请求获取数据
                $response = $this->http->post($host,$data);
                $res = $response->getBody()->getContents();
                $res = json_decode($res,true);
                return $res;
            }else{
                //拼接请求地址
                $url = $host . '?' . http_build_query($data);
                //执行请求获取数据
                $response = $this->http->get($url);
                $res = $response->getBody()->getContents();
                $res = json_decode($res,true);
                return $res;
            }
    }
}