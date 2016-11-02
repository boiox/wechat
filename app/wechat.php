<?php

return [
    '__pattern__'   => [
        'name' => '\w+',
    ],

    'wechat/[:echostr]/:signature/:timestamp/:nonce'       => ['wechat/Index/index',['method'=>'get']],

];