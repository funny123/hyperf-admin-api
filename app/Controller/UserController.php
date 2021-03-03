<?php
declare(strict_types=1);

namespace App\Controller;

use Phper666\JWTAuth\JWT;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Elasticsearch\ClientBuilderFactory;
class UserController extends Controller
{

    /**
     * @Inject()
     * @var JWT
     */
    protected $jwt;

    /**
     * 获取用户信息
     * @return [type] [description]
     */
    public function info()
    {
        //获取用户数据
        $user = $this->request->getAttribute('user');
        unset($user['password']);
        return $this->success($user);

    }

    /**
     * 用户退出
     * @return [type] [description]
     */
    public function logout()
    {
        if  ($this->jwt->logout())  {
            return $this->success('','退出登录成功');
        };
        return $this->failed('退出登录失败');
    }

    /**
     * User: Marlon
     * Date: 2021/3/3
     * Time: 16:39
     * @return array|callable
     */
    public function elasticsearch()
    {
        // 如果在协程环境下创建，则会自动使用协程版的 Handler，非协程环境下无改变
        $builder = $this->container->get(ClientBuilderFactory::class)->create();

        $client = $builder->setHosts(['http://192.168.33.10:9200'])->build();

        $info = $client->info();

        return $info;
    }


}