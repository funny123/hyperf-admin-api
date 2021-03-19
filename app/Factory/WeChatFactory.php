<?php

declare(strict_types=1);

namespace App\Factory;

use EasyWeChat\Factory;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Guzzle\CoroutineHandler;
use Hyperf\Guzzle\HandlerStackFactory;
use Overtrue\Socialite\Providers\AbstractProvider;
use Psr\Container\ContainerInterface;
use Hyperf\Utils\ApplicationContext;
use Psr\SimpleCache\CacheInterface;

class WeChatFactory
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    private $config;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->config = $container->get(ConfigInterface::class)->get('wechat.mini_program.default');

        // 设置 OAuth 授权的 Guzzle 配置
        AbstractProvider::setGuzzleOptions([
            'http_errors' => false,
            'handler' => HandlerStack::create(new CoroutineHandler()),
        ]);
    }

    /**
     * @return \EasyWeChat\MiniProgram\Application
     */
    public function create()
    {
        $app = Factory::miniProgram($this->config);

        // 设置 HttpClient，当前设置没有实际效果，在数据请求时会被 guzzle_handler 覆盖，但不保证 EasyWeChat 后面会修改这里。
        $config = $app['config']->get('http', []);
        $config['handler'] = $this->container->get(HandlerStackFactory::class)->create();
        $app->rebind('http_client', new Client($config));

        // 重写 Handler
        $app['guzzle_handler'] = $this->container->get(HandlerStackFactory::class)->create();
        // 重新 Cache
        $app['cache'] = ApplicationContext::getContainer()->get(CacheInterface::class);
        return $app;
    }

}