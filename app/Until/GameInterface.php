<?php
declare(strict_types=1);

namespace App\Until;

use App\Service\ClientService;
use Carbon\Carbon;
use function Hyperf\Config\config;

class GameInterface{

    protected $config;

    function __construct($name = 'default'){
        $this->config = config('game.'.$name);
    }

    public function login($user){
        $data = [
            's' => 0,//操作子类型
            'account' => $user['account'],
            'money' => 0,
            'lineCode' => $this->config['line_code'],
            'KindID'=> $user['king_id'],
            'ip' => $user['ip'],
            'orderid' => $this->order($user),
            'lang' => 'zh-CN'
        ];
        return $this->request($data);
    }

    protected function order($user){
        $date = Carbon::now()->format('YmdHis');
        return $this->config['agent'] . $date . $user['account'];
    }

    protected function request($data){
        $url = $data['s'] == 6  ? $this->config['record_url'] : $this->config['api_url'];
        $timestamp = (int)(microtime(true) * 1000);
        $param = [
            'agent' => $this->config['agent'],
            'timestamp' => $timestamp,
            'param' => $this->opensslEncode($this->config['des_key'], http_build_query($data)),
            'key' => md5($this->config['agent'] . $timestamp . $this->config['md5_key'])
        ];
        $url .= '?' . http_build_query($param);
        $result = ClientService::request($url, $param);// post 第二个参数可能是这样写['form_params' => $param]
        $result = json_decode($result->getBody()->getContents(), true);
        if($result && $result['d']['code']){
            logger_debug('游戏登录失败：错误码：'.$result['d']['code']);
        }
        //jumpType=1 表示不显示返回到主页大厅，独立成游戏
        return $result['d']['url'].'&jumpType=1' ?? '';
    }


    function opensslEncode($key, $str)
    {
        $text = trim($str);
        $pad = 16 - (strlen($text) % 16);
        $str =  $text . str_repeat(chr($pad), $pad);
        $encrypt_str = openssl_encrypt($str, 'AES-128-ECB', $key, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING);
        return base64_encode($encrypt_str);
    }

    function opensslDecode($key, $str)
    {
        $str = base64_decode($str);
        $decrypt_str = openssl_decrypt($str, 'AES-128-ECB', $key, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING);
        $text = trim(pkcs5_unpad($decrypt_str));
        $pad = ord($text[strlen($text)-1]);
        if ($pad > strlen($text)){
            return false;
        }
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad){
            return false;
        }
        return substr($text, 0, -1 * $pad);
    }

































//    public static function __callStatic($func, $arguments)
//    {
//
//    }




















}