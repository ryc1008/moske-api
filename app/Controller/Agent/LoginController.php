<?php

declare(strict_types=1);

namespace App\Controller\Agent;

use App\Controller\BaseController;
use App\Model\Canal;
use App\Until\RegionSearcher;
use Carbon\Carbon;
use Hyperf\HttpServer\Contract\RequestInterface;

class LoginController extends BaseController
{

    public function index(RequestInterface $request)
    {
        try {
            $data = $request->all();
            $header = $request->getHeaders();
            $user = $this->attempt($data);
            if (!$user) {
                return $this->returnJson(1, null, '用户名或者密码错误');
            }
            if($user['status'] == Canal::STATUS_2){
                return $this->returnJson(1, null, '账户已被锁定');
            }
            $token = auth('jwt:canal')->login($user);
            $mobile = is_mobile($header) ? Canal::MOBILE_2 : Canal::MOBILE_1;
            $ua = get_user_agent($header);
            $ip = get_real_ip($header);
            $searcher = new RegionSearcher();
            $address = $searcher->search($ip);
            $user->update([
                'login_ip' => $ip,
                'address' => $address,
                'user_agent' => $ua,
                'mobile' => $mobile,
                'login_at' => Carbon::now()
            ]);
            return $this->returnJson(0, [
                'token' => $token,
                'expires_in' => setting('jwt_canal_ttl'),
            ], '登录成功，页面跳转种...');
        } catch (\Throwable $e) {
            return $this->returnJson(1, null, $e->getMessage());
        }
    }

    /**
     * 登录验证
     * @param $login
     * @return false | object
     */
    protected function attempt($login)
    {
        if (isset($login['username']) && $login['password']) {
            $user = Canal::query()->where('username', $login['username'])->first();
            if ($user) {
                $verify = password_verify($login['password'], $user['password']);
                if ($verify) {
                    return $user;
                }
            }
        }
        return false;
    }

    //授权登录
    public function authorize(){
        try {
            $token = cache()->get('agent:token');
            if(!$token){
                return $this->returnJson(1, null, '授权失败');
            }
            cache()->delete('agent:token');
            return $this->returnJson(0, [
                'token' => $token,
                'expires_in' => setting('jwt_canal_ttl'),
            ], '登录成功，页面跳转种...');
        } catch (\Throwable $e) {
            return $this->returnJson(1, null, $e->getMessage());
        }
    }


}
