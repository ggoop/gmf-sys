@extends('gmf::auth')
@section('title')
    新用户注册 - @parent
@stop
@section('content')
<div class="panel login-box">
    <div class="panel-heading">
        <h2 class="panel-title">新用户注册</h2>
    </div>
    <div class="panel-body">
        <form role="form" method="POST" action="{{ route('register') }}">
            <div class="form-group{{ $errors->has('account') ? ' has-error' : '' }}">
                <input class="form-control" type="text" name="account" value="{{ old('account') }}" placeholder="电子邮件或者手机号" />
                @if ($errors->has('account'))
                    <span class="help-block">
                        <strong>{{ $errors->first('account') }}</strong>
                    </span>
                @endif
            </div>
            <div class="form-group{{ $errors->has('secret') ? ' has-error' : '' }}">
                <input class="form-control" type="password" name="secret" placeholder="密码" />
                @if ($errors->has('secret'))
                    <span class="help-block">
                        <strong>{{ $errors->first('secret') }}</strong>
                    </span>
                @endif
            </div>
            <div class="form-group{{ $errors->has('secret_confirmation') ? ' has-error' : '' }}">
                <input class="form-control" type="password" name="secret_confirmation" placeholder="确认密码" />
                @if ($errors->has('secret_confirmation'))
                    <span class="help-block">
                        <strong>{{ $errors->first('secret_confirmation') }}</strong>
                    </span>
                @endif
            </div>
            <div class="form-group layout layout-align-space-between-center">
                <a href="{{ route('login') }}">账号密码登录</a>
            </div>
            <div class="form-group">
              <button type="submit" class="btn btn-info btn-block">立即注册</button>
            </div>
        </form>
    </div>
    <div class="panel-footer login-sns text-center">
     {!! QrCode::size(120)->margin(1)->generate(url()->full()); !!}
    </div>
</div>
@endsection