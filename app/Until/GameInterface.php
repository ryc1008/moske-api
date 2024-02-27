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
//        switch ($data['s']){
//            case 0: // login
//                $params = http_build_query($data);
//                break;
//        }
        $url = $data['s'] == 6  ? $this->config['record_url'] : $this->config['api_url'];
//        $result = ClientService::request($url, $data);
        $timestamp = (int)(microtime(true) * 1000);
        $url .= '?' . http_build_query([
                'agent' => $this->config['agent'],
                'timestamp' => $timestamp,
                'param' => $this->opensslEncode($this->config['des_key'], http_build_query($data)),
                'key' => md5($this->config['agent'] . $timestamp . $this->config['md5_key'])
            ]);
        $result = $this->curl_get_content($url);
        return $result;
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

    protected function curl_get_content($url, $conn_timeout=7, $timeout=15, $user_agent=null)
    {
        logger_debug($url);
        $headers = array(
            "Accept: application/json",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "Accept-Charset: utf-8;q=1"
        );
        if ($user_agent === null) {
            $user_agent = 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.57 Safari/537.36';
        }
        $headers[] = $user_agent;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $conn_timeout);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

        $res = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_errno($ch);
        curl_close($ch);

//        if (($err) || ($httpcode !== 200)) {
//            return $err;
//        }
        return $res;
    }


































//    public static function __callStatic($func, $arguments)
//    {
//
//    }




















}