<?php
return [
    'path_not_exist' => '<comment>项目路径不存在, 请执行命令 php potato app:init 进行配置!!!</comment>',
    'maxretry' => '<comment>超过最大重试次数, 应用已退出</comment>',
    'app:init' => [
        'description' => '初始化/设置 项目配置',
        'help' => '该命令可以初始化或重新设置一些配置',
        'set_path' => '<question>请输入项目地址 : </question>',
        'path_invalid' => '<comment> 路径不存在 ! </comment>',
        'init_fail' => '<error>设置失败, 可能是路径无权限</error>'
    ]
];