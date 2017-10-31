<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
  <title>
  @section('title')
  lns
  @show
  </title>
  <link rel="shortcut icon" href="{{ URL::asset('favicon.ico') }}" />
  <link rel="stylesheet" type="text/css" href="{{ mix('/css/app.css') }}" />
</head>

<body class="layout-fill">
  <div id="gmfApp" class="layout-fill">
    @yield('content')
  </div>
</body>

</html>
