<?php
declare(strict_types=1);

return [
    /*
     * 小程序
     */
    'mini_program' => [
        'default' => [
            'app_id'  => env('WECHAT_MINI_PROGRAM_APPID', ''),
            'secret'  => env('WECHAT_MINI_PROGRAM_SECRET', ''),
            'token'   => env('WECHAT_MINI_PROGRAM_TOKEN', ''),
            'aes_key' => env('WECHAT_MINI_PROGRAM_AES_KEY', ''),
        ],
    ],

    /*
     * 微信支付
     */
    'payment' => [
        'default' => [
            'sandbox'            => env('WECHAT_PAYMENT_SANDBOX', false),
            'app_id'             => env('WECHAT_PAYMENT_APPID', ''),
            'mch_id'             => env('WECHAT_PAYMENT_MCH_ID', 'your-mch-id'),
            'key'                => env('WECHAT_PAYMENT_KEY', 'key-for-signature'),
            'cert_path'          => env('WECHAT_PAYMENT_CERT_PATH', 'path/to/cert/apiclient_cert.pem'),    // XXX: 绝对路径！！！！
            'key_path'           => env('WECHAT_PAYMENT_KEY_PATH', 'path/to/cert/apiclient_key.pem'),      // XXX: 绝对路径！！！！
            'notify_url'         =>  env('WECHAT_PAYMENT_NOTIFY_URL', ''),                             // 默认支付结果通知地址
        ],
        // ...
    ],
];