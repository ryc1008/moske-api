<?php

declare(strict_types=1);

namespace App\Controller\App;

use App\Job\LoginJob;
use App\Job\RegisterJob;

use App\Model\Setting;
use App\Model\User;
use App\Service\QueueService;
use App\Until\RegionSearcher;
use Carbon\Carbon;
use Hyperf\HttpServer\Contract\RequestInterface;


class AuthController extends CommonController
{

    public function test(){
        $setting = Setting::tree(['id' => 1000], 'value', 'name');
        foreach ($setting as $key => $val){
            if(in_array($key, ['white_login', 'channel_wechat', 'channel_alipay'])){
                $setting[$key] = explode("\n", trim($val));
            }
        }
        cache()->set('setting:default', $setting);
        return $this->returnJson(0, $setting);


    }



    public function login(RequestInterface $request)
    {
        $params = $request->all();
        $header = $request->getHeaders();
        $data = $this->filter($params, $header);
        //查找用户是否存在
        $user = User::where('uuid', $data['uuid'])->first();
        if($user){
            //登录用户
            //更新VIP
            $check = $this->check($user['vip_at'], $user['id']);
            if ($check) {
                $user['vip_id'] = 1;
                $user['vip_at'] = null;
            }
            //登录队列[更新用户登录数据]
            $data['userid'] = $user['id'];
            $data['login_at'] = $user['login_at'];
            $data['free_num'] = $user['free_num'];
            $data['time'] = time();//直接存时间对象貌似在队列中有问题
            QueueService::push(new LoginJob($data));
        }else{
            //创建用户
            $data['login_at'] = Carbon::now();
            $data['vip_id'] = 1;//默认为游客
            $data['code'] = invite_code();//生成邀请码
            $data['free_num'] = setting('free_video');//每日免费观影数量
            logger_debug($data);
            logger_debug(setting());
            //TODO 未做 查询邀请人ID
            $user = User::create($data);//create方法将返回保存的模型实例
            logger_debug($user);
            $data['userid'] = $user['id'];
            //注册队列[更新用户登录数据 | 生成流量记录 | 生成小时记录]
            QueueService::push(new RegisterJob($data));
        }
        $token = auth('jwt:user')->login($user);
        return $this->returnJson(0, [
            'token' => $token,
            'user' => [
                'id' => $user['id'],
                'uuid' => $user['uuid'],
                'username'=> $user['username'],
                'code' => $user['code'],
                'vip_id' => $user['vip_id'],
                'vip_at' => $user['vip_at'],
            ]
        ]);
    }

    public function register(RequestInterface $request)
    {
        //这里的注册只是为了拿到手机号和生成提现密码
        return $this->returnJson(0, Carbon::now()->subDays(10));
    }

    /**
     * 初始化参数
     * @param $params
     * @param $header
     * @return array
     */
    protected function filter($params, $header){
        $canalId = $params['canal_id'] ?? 1000;
        if(!is_numeric($canalId)){
            $canalId = 1000;
        }
        $ip = get_real_ip($header);
        return [
            'canal_id'   => $canalId,
            'login_ip'   => $ip,
            'uuid'       => $params['uuid'] ?? '',
            'username'   => $params['username'] ?? '',
            'app_release'=> $params['release'] ?? '',//系统版本
            'app_version'=> $params['version'] ?? '',//APP版本号
            'app_vendor' => $params['vendor'] ?? '',//手机品牌
            'app_model'  => $params['model'] ?? '',//手机型号
            'app_network'=> $params['network'] ?? '',//网络状态
            'app_system' => $params['system'] ?? '',//操作系统
        ];
    }
}
