<?php
declare(strict_types=1);

use App\Model\Setting;
use Hyperf\Context\ApplicationContext;
use App\Service\LoggerService;
use Psr\SimpleCache\CacheInterface;
use function Hyperf\Support\env;

if (! function_exists('encrypt_data')) {
    /**
     * php加密用于js解密
     * @param mixed $data 加密的数据
     * @return string
     */
    function encrypt_data($data = null)
    {
        $key = env('TOKEN_KEY');
        $iv = env('TOKEN_IV');
        return base64_encode(openssl_encrypt(json_encode($data), "aes-128-cbc", $key, OPENSSL_RAW_DATA, $iv));
    }
}

if (! function_exists('decrypt_data')) {
    /**
     * php解密js加密字符串
     * @param $data string 加密数据字符串
     * @return string
     */
    function decrypt_data($data = '')
    {
        $key = env('TOKEN_KEY');
        $iv = env('TOKEN_IV');
        return json_decode(trim(openssl_decrypt($data, "AES-128-CBC", $key, OPENSSL_ZERO_PADDING, $iv)), true);
    }
}

if (! function_exists('logger_write')) {
    /**
     * 打印日志
     * @param $name string 日志名称
     * @param $data mixed 日志数据 打印请求参数需要使用
     * @param $level string 日志级别 debug info notice warning error critical alert emergency
     * @return mixed
     */
    function logger_write($message = '', $data = [], $level = 'info', $name = 'log')
    {
        return LoggerService::$level($message, $data, $name);
    }
}

if (! function_exists('logger_debug')) {
    /**
     * 日志：打印数据用
     * @param $data mixed 日志数据 打印请求参数需要使用
     */
    function logger_debug($data = [])
    {
        logger_write('http_debug', $data);
    }
}



if (! function_exists('load_router')) {
    /**
     * 配置多路由文件
     * @return void
     */
    function load_router()
    {
        $path = BASE_PATH . '/app/Router';
        $files = scandir($path);
        foreach ($files as $file) {
            if (strpos($file, ".php")) {
                require $path . '/' . $file;
            }
        }
    }
}

if (! function_exists('is_mobile')) {
    /**
     * 判断是否是手机端
     * @param $header
     * @return bool
     */
    function is_mobile($header): bool
    {
        $allHttp = isset($header['all_http']) ? $header['all_http'][0] : '';
        $wapProfile = isset($header['http_x_wap_profile']) ? $header['http_x_wap_profile'][0] : '';
        $httpProfile = isset($header['http_profile']) ? $header['http_profile'][0] : '';
        $userAgent = isset($header['user-agent']) ? $header['user-agent'][0] : '';
        $accept = isset($header['accept']) ? $header['accept'][0] : '';
        $isMobile = false;
        if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower($userAgent))) {
            $isMobile = true;
        }
        //小写转化然后查找第一次出现的位置
        if (strpos(strtolower($accept), 'application/vnd.wap.xhtml+xml') !== false) {
            $isMobile = true;
        }
        if ($wapProfile) {
            $isMobile = true;
        }
        if ($httpProfile) {
            $isMobile = true;
        }
        $mobileUserAgent = strtolower(substr($userAgent, 0, 4));
        $agentArray = [
            'w3c ', 'acs-', 'alav', 'alca', 'amoi', 'audi', 'avan', 'benq', 'bird', 'blac',
            'blaz', 'brew', 'cell', 'cldc', 'cmd-', 'dang', 'doco', 'eric', 'hipt', 'inno',
            'ipaq', 'java', 'jigs', 'kddi', 'keji', 'leno', 'lg-c', 'lg-d', 'lg-g', 'lge-',
            'maui', 'maxo', 'midp', 'mits', 'mmef', 'mobi', 'mot-', 'moto', 'mwbp', 'nec-',
            'newt', 'noki', 'oper', 'palm', 'pana', 'pant', 'phil', 'play', 'port', 'prox',
            'qwap', 'sage', 'sams', 'sany', 'sch-', 'sec-', 'send', 'seri', 'sgh-', 'shar',
            'sie-', 'siem', 'smal', 'smar', 'sony', 'sph-', 'symb', 't-mo', 'teli', 'tim-',
            'tosh', 'tsm-', 'upg1', 'upsi', 'vk-v', 'voda', 'wap-', 'wapa', 'wapi', 'wapp',
            'wapr', 'webc', 'winw', 'winw', 'xda', 'xda-'
        ];
        if (in_array($mobileUserAgent, $agentArray)) {
            $isMobile = true;
        }
        if (strpos(strtolower($allHttp), 'operamini') !== false) {
            $isMobile = true;
        }
        if (strpos(strtolower($userAgent), 'windows phone') !== false) {
            $isMobile = true;
        }
        return $isMobile;
    }
}

if (! function_exists('get_user_agent')) {
    /**
     * 获取UA信息
     * @param $header
     * @return string
     */
    function get_user_agent($header)
    {
        return isset($header['user-agent']) ? $header['user-agent'][0] : '';
    }
}

if (! function_exists('get_real_ip')) {
    /**
     * 获取IP
     * @param $header
     * @return string
     */
    function get_real_ip($header)
    {
        $ip = isset($header['x-real-ip']) ? $header['x-real-ip'][0] : '';
        $forwarded = isset($header['x-forwarded-for']) ? $header['x-forwarded-for'][0] : '';
        if ($forwarded) {
            $ip = $forwarded;
        }
//    if($forwarded){
//        if(strpos($forwarded,',') > 25){
//            $ip = trim(trim(substr($forwarded,strpos($forwarded,',')),','));
//        }else{
//            $ip = substr($forwarded,0,strpos($forwarded,','));
//        }
//    }
        return $ip;
    }
}


if (! function_exists('cache')) {
    /**
     * 缓存
     * @return mixed
     */
    function cache()
    {
        return ApplicationContext::getContainer()->get(CacheInterface::class);
    }
}

if (! function_exists('state_to_text')) {
    /**
     * 状态转化
     * @param $data
     * @param $arr
     * @return mixed
     */
    function state_to_text(&$data, $arr = [])
    {
        foreach ($data as &$row) {
            foreach ($arr as $k => $val) {
                if (isset($row[$k]) && isset($val[$row[$k]])) {
                    $text = $k . '_text';
                    $row[$text] = $val[$row[$k]];
                }
            }
        }
        return $data;
    }
}

if (! function_exists('bcrypt')) {
    /**
     * Hash the given value against the bcrypt algorithm.
     *
     * @param  string  $value
     * @return string
     */
    function bcrypt($value)
    {
        return password_hash($value, PASSWORD_BCRYPT);
    }
}

if (! function_exists('list_to_tree')) {
    /**
     * 把返回的数据集转换成Tree
     * @param array $list 要转换的数据集
     * @param string $pk 主键字段名称
     * @param string $pid parent字段名称
     * @param string $child child名字
     * @param int $root 最顶级id数字
     * @return array
     */
    function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = 'children', $root = 0)
    {
        // 创建Tree
        $tree = [];
        // 创建基于主键的数组引用
        $refer = [];
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
        return $tree;
    }
}

if (! function_exists('text_to_array')) {
    /**
     * 文本换行转化成数组
     * @param  string  $text
     * @return string
     */
    function text_to_array($text)
    {
        $data = explode("\n", trim($text));
        $rule = [];
        foreach ($data as $item){
            $arr = explode(':', $item);
            $rule[$arr[0]] = $arr[1];
        }
        return $rule;
    }
}


if (! function_exists('setting')) {
    /**
     * 获取配置缓存
     * @param  string  $text
     * @return string
     */
    function setting($key)
    {
        $setting = cache()->get('setting:default');
        if(!$setting){
            $data = Setting::list();
            cache()->set('setting:default', $data);
        }
        return $setting[$key] ?: '';
    }
}

if (! function_exists('str_to_second')) {

    /**
     * 将时间字符串转化成秒数
     * @param $time
     * @return float|int|string
     */
    function str_to_second($time){
        $duration  = explode(":", $time);
        if(count($duration) == 3){
            $seconds = (int)$duration[0] * 3600 + (int)$duration[1] * 60 + (int)$duration[2];
        }
        if(count($duration) == 2){
            $seconds = (int)$duration[0] * 60 + (int)$duration[1];
        }
        return $seconds;
    }
}

if (! function_exists('invite_code')) {
    /*
     * 邀请码
     */
    function invite_code(){
        $code = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $rand = $code[rand(0,25)] . strtoupper(dechex((int)date('m'))) . date('d'). substr((string)time(),-5) . substr(microtime(),2,5) . sprintf('%02d',rand(0,99));
        for($a = md5( $rand, true ), $s = '0123456789ABCDEFGHIJKLMNOPQRSTUV',
            $d = '', $f = 0; $f < 8; $g = ord( $a[ $f ] ), $d .= $s[ ( $g ^ ord( $a[ $f + 8 ] ) ) - $g & 0x1F ], $f++
        );
        return $d;
    }
}

if (! function_exists('uuid')) {
    /*
     * uuid
     */
    function uuid($prefix = ''){
        $chars = md5(uniqid((string)mt_rand(), true));
        $uuid = substr($chars, 0, 8 ) . '-'
            . substr($chars, 8, 4 ) . '-'
            . substr($chars, 12, 4 ) . '-'
            . substr($chars, 16, 4 ) . '-'
            . substr($chars, 20, 12 );
        return $prefix . $uuid;
    }
}