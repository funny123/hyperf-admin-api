<?php
declare(strict_types = 1);

namespace App\Controller\Auth;

use App\Model\User;
use Phper666\JWTAuth\JWT;
use App\Controller\Controller;
use Hyperf\Di\Annotation\Inject;

class LoginController extends Controller
{
    /**
     * @Inject
     *
     * @var JWT
     */
    protected $jwt;
    /**
     * 用户登录.
     *
     * @return array
     */
    public function login()
    {
//         $hash = password_hash($this->request->input('password'), PASSWORD_DEFAULT);
//         return $this->failed($hash);
        $user = User::query()->where('account', $this->request->input('username'))->first();
        //验证用户账户密码
        if  (!empty($user->password) && password_verify($this->request->input('password'), $user->password))  {
            $userData = [
                'uid'       => $user->uid,
                'account'  => $user->account,
            ];
            $token = $this->jwt->getToken($userData);
            $data  = [
                'token' => (string) $token,
                'exp'   => $this->jwt->getTTL(),
            ];
            return $this->success($data);
        }
        return $this->failed('登录失败');
    }
}