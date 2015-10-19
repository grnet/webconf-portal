@extends ('layouts.main')

@include ('errors.list')
@section ('content')
{!! Form::open(array('action' => 'bbbController@join_pin')) !!}
	<body id="pin">
	
	<div class="form-group-pin">		
		
		<div class="row">
			<div class="col-xs-12" id="header_img">
				<div id="img_pin"><img src="/img/light.png"></div>
			</div>
            
			<div class="col-xs-12 form-group-name" id="pin_label">{!! Form::label('access_pin' , trans('room.join_pin.access_pin')) !!}</div>	
			
			
			<div class="col-xs-12" id="insert_pin">{!! Form::password('access_pin') !!}</div>
			<div class="col-xs-12 valid">{!! app('captcha')->display(); !!}</div>
			
		</div>
	
		<div class="row">
			
				<div class="col-xs-12" id="join_submit">{!! Form::submit(trans('room.join_pin.submit'), [ 'class' => 'btn btn-default', 'id' => 'join_withPin_submit']); !!}</div>
			
		</div>
	
	</div>
	</body>
{!! Form::close() !!}
@stop
