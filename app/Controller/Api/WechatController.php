<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Factory\WeChatFactory;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;

/**
 * @AutoController()
 * Class AuthController
 * @package App\Controller\Api
 */
class WechatController
{
    /**
     * @Inject()
     * @var WeChatFactory
     */
    protected $factory;
    public function get_user()
    {
        $app = $this->factory->create();
        $tid = '423';     // 模板标题 id，可通过接口获取，也可登录小程序后台查看获取
        $kidList = [1, 3, 4];      // 开发者自行组合好的模板关键词列表，可以通过 `getTemplateKeywords` 方法获取
        $sceneDesc = '菜谱新增';    // 服务场景描述，非必填
//        return $app->subscribe_message->addTemplate($tid, $kidList, $sceneDesc);
    }
    /**
     * Note: 发送订阅消息
     * User: Marlon
     * Date: 2021/3/19
     * Time: 15:57
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function send(RequestInterface $request,ResponseInterface $response)
    {
//        $code = $request->input('code');
//        $encryptedData = $request->input('encrypted_data');
//        $iv = $request->input('iv');

        $app = $this->factory->create();
        $data = [
            'template_id' => 'aOi3w5Q44cSX0ZywMa_aTZ0gMdr5jquWIIYCiVvwz0w', // 所需下发的订阅模板id
            'touser' => 'oDOPy0IaklhUczMKwfbEuUJYr8Hg',     // 接收者（用户）的 openid
            'page' => 'pages/detail/detail',       // 点击模板卡片后的跳转页面，仅限本小程序内的页面。支持带参数,（示例index?foo=bar）。该字段不填则模板无跳转。
            'data' => [         // 模板内容，格式形如 { "key1": { "value": any }, "key2": { "value": any } }
                'phrase1' => [
                    'value' => "老干妈蒸排骨的做法",
                ],
                'date3' => [
                    'value' => '2021-03-19',
                ],
                'thing4' => [
                    'value' => '这道菜是我跟老婆脑力激荡出来的，我喜欢吃肉，老婆喜欢吃土豆，我们都喜欢吃老干妈，所以这样的一道菜就诞生了。香辣鲜香，蒜香四溢，满口生香！！！    老干妈蒸排骨用料工具    主料：猪',
                ],
            ],
        ];

        return $app->subscribe_message->send($data);
    }
    public function index(RequestInterface $request, ResponseInterface $response)
    {
        $app = $this->factory->create();
//        return $app->subscribe_message->getTemplates();
        return $app->subscribe_message->getTemplateKeywords('423');
    }
}
