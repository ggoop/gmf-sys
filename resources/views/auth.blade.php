<!doctype html>
<html lang="zh-cn">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>@section('title') {{env('APP_NAME')}} @show
  </title>
  <link rel="shortcut icon" href="{{ URL::asset('favicon.ico') }}" />
  <link rel="stylesheet" href="/assets/vendor/gmf-ac/css/auth.css">
</head>
<body>
  <div class="layout-row layout-align-center-center layout-fill">
    @yield('content')
  </div>
</body>

</html>
