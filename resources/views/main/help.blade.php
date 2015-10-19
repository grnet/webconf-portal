@extends('layouts/main')

@section('content')
<div class="row">
	<div class="frame"><img src="/img/speed.png" id="img_help"></div>
</div class="row">
    <div style="display:block; width:300px; background-color:#ffffff; border:1px solid #cecece; margin:auto; padding-bottom:10px; border-bottom-left-radius:10px; border-bottom-right-radius:10px;">
	<a target="_blank" href="{{ $bbb_check_url }}"><button class="btn btn-default">{{ trans('webconf.help.check_your_connection') }}</button></a>
	</div>
</div>
@stop
