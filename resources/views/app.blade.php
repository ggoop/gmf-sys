<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
  <meta content="{{ env('APP_NAME') }}" property="og:site_name">
  <title>{{env('APP_NAME')}}</title>
  <link rel="shortcut icon" href="{{ URL::asset('favicon.ico') }}" />
  <link rel="stylesheet" type="text/css" href="{{ mix('/css/app.css') }}" />
  @if (!empty($data))
      <script>
        gmfData={!! json_encode($data) !!};
      </script>
  @endif
</head>
<body>
  <div id="gmfApp" class="layout-fill">
    <router-view></router-view>
    <md-toast ref="rootToast"></md-toast>
  </div>
  <script src="{{ mix('/js/manifest.js')}}"></script>
  <script src="{{ mix('/js/vendor.js')}}"></script>
  <script src="{{ mix('/js/app.js')}}"></script>
</body>

</html>
