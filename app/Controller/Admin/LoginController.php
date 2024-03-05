<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\BaseController;
use App\Model\Safe;
use App\Model\Manager;
use App\Until\RegionSearcher;
use Carbon\Carbon;
use Hyperf\HttpServer\Contract\RequestInterface;
use function Hyperf\Config\config;

class LoginController extends BaseController
{

    /**
     * 登录
     * @param RequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function index(RequestInterface $request)
    {
        try {
            $data = $request->all();
            $header = $request->getHeaders();
            $user = $this->attempt($data);
            $ip = get_real_ip($header);
            $data['ip'] = $ip;
            $data['success'] = false;
//            //IP白名单,需要后台管理
//            $whiteList = setting('white_login');
//            if(!in_array($ip, $whiteList)){
//                $this->logger($data, $header, 'IP非法不在白名单内');
//                return $this->returnJson(1, null, '非法操作');
//            }
            if (!$user) {
                $this->logger($data, $header, '用户名或者密码错误');
                return $this->returnJson(1, null, '用户名或者密码错误，请联系管理员');
            }
            if($user['status'] == Manager::STATUS_2){
                $this->logger($data, $header, '账户已被锁定');
                return $this->returnJson(1, null, '账户已被锁定，请联系管理员');
            }
            $token = auth('jwt:manager')->login($user);
            $data['success'] = true;
            $this->logger($data, $header, '登录成功');
            return $this->returnJson(0, [
                'token' => $token,
                'expires_in' => setting('jwt_manager_ttl'),
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
            $user = Manager::query()->where('username', $login['username'])->first();
            if ($user) {
                $verify = password_verify($login['password'], $user['password']);
                if ($verify) {
                    return $user;
                }
            }
        }
        return false;
    }

    /**
     * 登录日志
     * @param $login
     * @param $header
     * @param $remark
     * @return void
     */
    protected function logger($login, $header, $remark = '')
    {
        $mobile = is_mobile($header) ? Safe::MOBILE_2 : Safe::MOBILE_1;
        $ua = get_user_agent($header);
        $status = $login['success'] ? Manager::STATUS_1 : Manager::STATUS_2;
        $searcher = new RegionSearcher();
        $address = $searcher->search($login['ip']);
        Safe::query()->create([
            'username' => $login['username'],
            'password' => $login['password'],
            'login_ip' => $login['ip'],
            'address' => $address,
            'user_agent' => $ua,
            'remark' => $remark,
            'mobile' => $mobile,
            'status' => $status,
            'login_at' => Carbon::now(),
        ]);
    }


}
