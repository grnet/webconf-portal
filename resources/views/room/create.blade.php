@extends ('layouts.main')

@section ('content')
@include ('errors.list')
{!! Form::open(array('action' => 'roomController@store')) !!}
	
	<div class="row">
		<div class="titles">
			<h4>{{ trans('room.rooms.add') }}</h4>
		</div>
	</div>
	<div class="form-group">
		{!! Form::label('name' , trans('room.create.name')) !!}
		{!! Form::text('name') !!}
	</div>

	<div class="form-group">
		{!! Form::label('recording' , trans('room.create.recording')) !!}
		{!! Form::checkbox('recording' , 1) !!}
	</div>
	<div class="form-group">
		{!! Form::label('public' , trans('room.create.public')) !!}
		{!! Form::checkbox('public' , 1) !!}
	</div>
	<div class="form-group">
		{!! Form::label('participants' , trans('room.create.participants')) !!}<br/>
		{!! Form::textarea('participants') !!}
	</div>	


	{!! Form::submit(trans('room.create.submit'), [ 'class' => 'btn btn-default']); !!}
{!! Form::close() !!}
@stop
