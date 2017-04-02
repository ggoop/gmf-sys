# Ggoop/Gmf
gmf framework
## Documentation

### Quick Overview:

1 - 使用 Composer 依赖包管理器安装 Ggoop/Gmf:

```shell
composer require ggoop/gmf-laravel
```

2 - 接下来，将 Gmf 的服务提供者注册到配置文件 config/app.php 的 providers 数组中：
```shell
Ggoop\Gmf\GmfServiceProvider::class,
```

3 - Gmf 使用服务提供者注册内部的数据库迁移脚本目录，所以上一步完成后，你需要更新你的数据库结构。Gmf的迁移脚本会自动创建应用程序需要的数据表：

```shell
php artisan migrate
```

4 - 在 RouteServiceProvider 的 boot 方法中调用 Gmf::routes 函数。这个函数会注册一些必要路由：

```shell
<?php

namespace App\Providers;

use Ggoop\Gmf\Gmf;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider {
  public function boot() {
    Gmf::routes();
    parent::boot();
  }
}
```

5 - 使用 Artisan 命令 vendor:publish 来发布 Gmf 的 Vue 组件：
```shell
php artisan vendor:publish --tag=gmf-components
```


5 - 日志记录,在app\Http\Kernel.php 文件中，注册路由中间件

```shell
protected $routeMiddleware = [
    ...
    'visitor' => \Ggoop\Gmf\Http\Middleware\VisitorMiddleware::class,
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
