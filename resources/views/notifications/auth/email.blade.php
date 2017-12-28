@component('mail::message')
{{-- Greeting --}}
@if (! empty($greeting))
# {{ $greeting }}
@else
@if ($level == 'error')
# Whoops!
@else
# 你好!
@endif
@endif

{{-- Intro Lines --}}
@foreach ($introLines as $line)
{{ $line }}

@endforeach

{{-- Action Button --}}
@isset($actionText)
<?php
switch ($level) {
case 'success':
	$color = 'green';
	break;
case 'error':
	$color = 'red';
	break;
default:
	$color = 'blue';
}
?>
@component('mail::button', ['url' => $actionUrl, 'color' => $color])
{{ $actionText }}
@endcomponent
@endisset

{{-- Outro Lines --}}
@foreach ($outroLines as $line)
{{ $line }}

@endforeach

{{-- Salutation --}}
@if (! empty($salutation))
{{ $salutation }}
@else
Regards,<br>{{ config('app.name') }}
@endif

{{-- Subcopy --}}
@if (! empty($actionText)&&! empty($actionUrl))
@isset($actionText)
@component('mail::subcopy')
如果你无法使用 "{{ $actionText }}" 按钮, 建议你复制下边的链接，到浏览器中进行浏览: [{{ $actionUrl }}]({{ $actionUrl }})
@endcomponent
@endisset
@endif

@endcomponent
