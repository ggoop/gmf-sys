@extends('gmf::auth')
@section('title')
    重置密码 - @parent
@stop
@section('content')
<div class="panel login-box">
    <div class="panel-heading">
        <h2 class="panel-title">重置密码</h2>
    </div>
    <div class="panel-body">
        <form role="form" method="POST" action="{{ route('password.email',$params) }}">
            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                <input class="form-control" type="text" name="email" value="{{ old('email') }}" placeholder="电子邮件地址" />
                @if ($errors->has('email'))
                    <span class="help-block">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                @endif
            </div>
            <div class="form-group layout layout-align-space-between-center">
              <a href="{{ route('login') }}">账号密码登录</a>
            </div>
            <div class="form-group">
              <button type="submit" class="btn btn-warn btn-block">发送邮件重置密码</button>
            </div>
        </form>
    </div>
    <div class="panel-footer login-sns text-center">
     {!! QrCode::size(120)->margin(1)->generate(url()->full()); !!}
    </div>
</div>
@endsection