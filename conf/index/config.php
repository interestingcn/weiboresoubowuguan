<?php
// Index 模块配置
return[

//全局替换规则
    'view_replace_str'  =>  [
        '__ROOT__' => '/',
        '__ASSEST__' => '/static/index',

    ],
    'default_filter' => 'htmlspecialchars,addslashes,strip_tags',
];