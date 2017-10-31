@section('title')
注册_@parent
@stop
@extends('gmf::lns')
@section('content')
<div class="md-card">
  <div class="md-card-content">
    <form class="form-horizontal"  role="form" action="/sys/lns/regist" method="post">
      <input type="hidden" name="_token" value="{{ csrf_token() }}">
      <div class="md-input-container">
      <input class="md-input" type="text" name="ent_id" value="{{$ent_id or old('ent_id')}}" placeholder="企业ID" />
      </div>
    <div class="md-input-container">
      <textarea class="md-input" rows="10" name="content">{{old('content')}}</textarea>
      </div>
      <button type="submit" class="md-button md-raised md-primary">注册</button>
    </form>
  </div>
</div>

@stop
