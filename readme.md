# Gmf/Sys
gmf framework
## Documentation

### Quick Overview:

0 - 使用 git clone https://github.com/ggoop/gmf-laravel.git ,下载框架，或者使用下列方式安装。

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
'providers' => [
    'users' => [
      'driver' => 'eloquent',
      'model' => Gmf\Sys\Models\User::class,
    ],
  ],
'passwords' => [
    'users' => [
      'provider' => 'users',
      'table' => 'gmf_sys_password_resets',
      'expire' => 60,
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
    'client_credentials' => \Gmf\Sys\Passport\Http\Middleware\CheckClientCredentials::class,
    'ent_check' => \Gmf\Sys\Http\Middleware\EntCheck::class,
  ];
protected $middlewareGroups = [
    'web' => [
      ...
      \Gmf\Sys\Passport\Http\Middleware\CreateFreshApiToken::class
      'visitor',
    ],

    'api' => [
      ...
      'visitor',
    ],
  ];
```

7.1 - 你需要运行 passport:keys 命令来创建生成安全访问令牌时用到的加密密钥

```shell
php artisan passport:keys
```

8.1 - 设置入口路由routes/web.php 

```shell
Route::get('/', ['uses' => 'HomeController@home']);
...
Route::get('/{page?}', ['uses' => 'HomeController@index'])->where('page', '^(?!js\/|gapi\/|css\/|dist\/).*$');
```

增加控制器HomeController，内容如下
```shell
<?php

namespace App\Http\Controllers;
use Auth;
use Gmf\Sys\Builder;
use Gmf\Sys\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller {
  public function __construct() {
    $this->middleware('auth');
  }
  public function home() {
    return redirect('mana.app/mana.app.home');
  }
  public function index(Request $request) {   
    $config = new Builder;
    ...
    return view('mana::app', ['config' => $config]);
  }
```