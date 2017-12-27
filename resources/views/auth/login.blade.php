@extends('gmf::auth')
@section('title')
    账号密码登录 - @parent
@stop
@section('content')
<div class="panel login-box">
    <div class="panel-heading">
        <h2 class="panel-title">账号密码登录</h2>
    </div>
    <div class="panel-body">
        <form role="form" method="POST" action="{{ route('login',$params) }}">
            <div class="form-group{{ $errors->has('account') ? ' has-error' : '' }}">
                <input class="form-control" type="text" name="account" value="{{ old('account') }}" placeholder="电子邮件地址" />
                @if ($errors->has('account'))
                    <span class="help-block">
                        <strong>{{ $errors->first('account') }}</strong>
                    </span>
                @endif
            </div>
            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                <input class="form-control" type="password" name="password" placeholder="密码" />
                @if ($errors->has('password'))
                    <span class="help-block">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif
            </div>
            <div class="form-group layout layout-align-space-between-center">
              <a href="{{ route('password.email') }}">找回我的账号密码</a>
              <a href="{{ route('register') }}">创建账号</a>
            </div>
            <div class="form-group">
              <button type="submit" class="btn btn-primary btn-block">登录</button>
            </div>
        </form>
    </div>
    <div class="panel-footer login-sns text-center">
    {!! QrCode::size(120)->margin(1)->generate(url()->full()) !!}
    </div>
</div>
@endsection