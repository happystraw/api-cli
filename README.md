# HappyStraw Console

> 一个轻量级cli框架

## 环境要求

* php>=5.6
* composer

## 快速起步

###### 1. 使用`composer`安装
```shell
$ composer create-project happystraw/console project-name
```

###### 2. 创建新的命令

```shell
$ php potato make:command User/Create
```

此操作会:

1. 生成文件`src/Consoles/User/Create.php`
2. 自动注册命令 `user:create` 到`src/Consoles/Kernel.php` 中`$commands`
3. 使用命令`php potato user:create`即可调用新增的命令


###### 3. 命令文档详见: [symfony/console](https://github.com/symfony/console)

## 标准应用结构

```php
├── bootstrap // 启动文件目录
│   └── app.php // 应用启动文件
├── config // 配置文件目录
│   ├── base.php // 常量定义文件
│   └── common.php // 配置定义文件
├── resources // 资源目录
│   ├── lang // 语言配置文件目录
│   └── tpl // 模板文件目录
├── src // 项目代码目录
│   ├── Consoles // 自定义命令文件目录
│   │   ├── Make
│   │   │   └── MakeCommand.php
│   │   └── Kernel.php // console应用
│   ├── Facades // Facade调用文件目录
│   ├── Librarys // 核心库目录
│   ├── Traits // Trait文件目录
│   ├── Application.php // 应用文件
│   └── Helpers.php // 助手函数文件
├── vendor // 依赖目录
├── composer.json
├── LICENSE
├── potato // 入口文件
└── README.md
```


## License
Happystraw Console is open source and released under the MIT Licence.

Copyright (c) 2017 FangYutao
