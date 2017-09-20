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

5 - 日志记录,在app\Http\Kernel.php 文件中，注册路由中间件

```shell
protected $routeMiddleware = [
    ...
    'visitor' => \Gmf\Sys\Http\Middleware\VisitorMiddleware::class,
    'ent_check' => \Gmf\Sys\Http\Middleware\EntCheck::class,
  ];
protected $middlewareGroups = [
    'web' => [
      ...
      \Gmf\Sys\Passport\Http\Middleware\CreateFreshApiToken::class
      'visitor',
      'ent_check',
    ],

    'api' => [
      ...
      'visitor',
      'ent_check',
    ],
  ];
```

6 - 项目安装,在项目目录下，执行下列命令

```shell
 php artisan gmf:install --seed --force
```
