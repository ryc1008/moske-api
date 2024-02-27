<?php
declare(strict_types=1);


return [
    'default' =>[
        'agent' => '60029',//代理编号
        'des_key' => 'E4A47FC56B2A195D',//MD5密钥
        'md5_key' => 'CE0ABD95EE616130',//DES密钥
        'api_url' => 'https://channel.tzvolvo.com/channelHandle', //api接口URL
        'record_url' => 'https://record.tzvolvo.com/getRecordHandle', //拉单独立接口URL
        'line_code' => 'lc60029', //linecode，格式lc+代理编号
    ]
];
