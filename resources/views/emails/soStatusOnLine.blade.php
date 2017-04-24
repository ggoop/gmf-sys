@extends('emails.soStatus')

@section('content')
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td style="line-height: 30px;font-size: 16px;color:#666">
      <p style="font-size: 20px;"><span>{{$mainData->user_nickname}}</span> 你好：</p>
      <p >
        你的订单  <span style="color:#b44e00;font-weight: bold;">{{$mainData->docno}}</span>  已于{{$mainData->process_time}}，在<span style="color:#00a65a;font-weight: bold;">{{$mainData->product_factory}}</span>上线加工。
      </p>
      <p>
        手机扫二维码或者点击链接，查看生产进度！
      </p>
    </td>
  </tr>
</table>
@stop