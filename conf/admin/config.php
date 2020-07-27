<?php
// Admin 模块 Config 配置
return[
//    reCatpcha 设置
    'recaptcha_public_key'  =>  '6Leu2PQUAAAAAPC8EsfFNFFrgnAQ_VRhZCrKUnEq',
    'recaptcha_private_key' => '6Leu2PQUAAAAAGUo5vinZ8T7ivAoxS_Ur1jzs-6E',

//全局替换规则
    'view_replace_str'  =>  [
        '__ROOT__' => '/',
        '__STATIC__' => '/static/admin',

    ],
    'template'  =>  [
        'layout_on'     =>  true,
        'layout_name'   =>  'layout',
    ],
    'default_filter' => 'htmlspecialchars,addslashes,strip_tags',

    'session'                => [
        'prefix'         => 'Me',
        'type'           => '',
        'auto_start'     => true,
        'expire'        =>  '2400',
    ],
];