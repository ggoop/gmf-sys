# Gmf/Sys
gmf framework
## Documentation

### Quick Overview:

1 - 使用 Composer 依赖包管理器安装 Gmf/Sys:

```shell
composer require ggoop/gmf-sys
```

2 - 接下来，将 Gmf 的服务提供者注册到配置文件 config/app.php 的 providers 数组中：
```shell
Gmf\Sys\ServiceProvider::class,
Gmf\Sys\Passport\ServiceProvider::class,
```

3 - Gmf 使用服务提供者注册内部的数据库迁移脚本目录，所以上一步完成后，你需要更新你的数据库结构。Gmf的迁移脚本会自动创建应用程序需要的数据表：

```shell
php artisan migrate
```

4.1 - 在 app/Providers/RouteServiceProvider.php 的 boot 方法中调用 Sys::routes 函数。这个函数会注册一些必要路由：

```shell
<?php

namespace App\Providers;

use Gmf\Sys\Sys;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider {
  public function boot() {
    Sys::routes();
    parent::boot();
  }
}
```
4.2 - 在 app/Providers/AuthServiceProvider.php 的 boot 方法中调用 Passport::routes 函数。这个函数会注册一些必要路由：

```shell
<?php

namespace App\Providers;

use Gmf\Sys\Passport\Passport;

class RouteServiceProvider extends ServiceProvider {
  public function boot() {
    Passport::routes();
    Passport::enableImplicitGrant();
  }
}
```

4.3 - 需要将配置文件 config/auth.php 中 api 部分的授权保护项（ driver ）改为 passport：

```shell
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],

    'api' => [
        'driver' => 'passport',
        'provider' => 'users',
    ],
],
```

5 - 使用 Artisan 命令 vendor:publish 来发布 Gmf\Sys 的 Vue 组件：
```shell
php artisan vendor:publish --tag=gmf
```


6 - 日志记录,在app\Http\Kernel.php 文件中，注册路由中间件

```shell
protected $routeMiddleware = [
    ...
    'visitor' => \Gmf\Sys\Http\Middleware\VisitorMiddleware::class,
  ];
protected $middlewareGroups = [
    'web' => [
      ...
      'visitor',
    ],

    'api' => [
      ...
      'visitor',
    ],
  ];
```

7.1 - 你需要运行 passport:install 命令来创建生成安全访问令牌时用到的加密密钥，同时，这条命令也会创建「私人访问」客户端和「密码授权」客户端

```shell
php artisan passport:install
```
7.2 - 发放访问令牌-管理客户端

```shell
php artisan passport:client
```
