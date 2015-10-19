@extends ('layouts.main')

@section ('css')
	<link href="{!! asset('css/bootstrap-datetimepicker.min.css') !!}" rel="stylesheet">
	<link href="{!! asset('css/bootstrap-switch.min.css') !!}" rel="stylesheet">
	@parent
@stop


@section ('content')


<div id="wrapper">
<div id="sidebar-wrapper">

<ul class="btn-top-xs sidebar-nav nav-stacked navbar-fixed-top">
	@if ($check_owner)
		<li class="col-xs-2 col-md-2"><a class="btn btn-sidebar side-menu" href="{{ action('roomController@edit', $room->id) }}"><i class="glyphicon glyphicon-pencil"></i>&nbsp; <span>{{ trans('room.show.edit') }}</span></a></li>
		<li class="col-xs-2 col-md-2">{!! Form::open(['action' => ['roomController@destroy', $room->id], 'method' => 'delete']) !!}
			<a class='btn btn-sidebar side-menu' type='button' data-toggle="modal" data-target="#confirmDelete" data-title="{{ trans('room.show.destroy') }}" data-message='{{ trans('room.show.destroy.confirm') }}'>
			    <i class='glyphicon glyphicon-trash'></i><span>&nbsp;&nbsp;{{ trans('room.show.destroy') }}</span>
			</a>
		{!! Form::close() !!}
		</li>
		<li class="col-xs-2 col-md-2">
		{!! Form::open(['action' => ['roomController@invite', $room->id], 'method' => 'post', 'id' => 'form_invite']) !!}
		  {!! Form::input('hidden', 'invite_text' ) !!}
		  {!! Form::input('hidden', 'date' ) !!} 
			<a class="btn btn-sidebar side-menu" type="button" id="show_invite" data-toggle="modal" data-target="#invite"><i class="glyphicon glyphicon-envelope"></i><span>&nbsp;&nbsp;{{ trans('room.show.show_invite') }}</span></a>
		{!! Form::close() !!}
		</li>
	
	@endif
		<li class="col-xs-2 col-md-2"><a class="btn btn-sidebar side-menu" href="{{ action('bbbController@join', [$room->id]) }}"><i class="glyphicon glyphicon-facetime-video" aria-hidden="true"></i>&nbsp;&nbsp;<span>{{ trans('webconf.room.join') }}</span></a></li>
		<li class="col-xs-2 col-md-2"><a class="btn btn-mini btn-sidebar side-menu" href="{{ URL::previous() }}"><i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;<span>{{ trans('room.show.back') }}</span></a></li>
		</ul>
	
	

</div>
</div>

<div class="wc_content_show" style="margin-top:80px">
@include ('errors.list')
<div id="page-content-wrapper" class="col-xs-12">
@include ('layouts.confirm')
@include ('layouts.share')
@include ('layouts.invite')
	
	@if ($check_owner)
	{{-- email invitations --}}
	@if (Session::has('message'))
		<div class="alert alert-success">
		<h5><i class="glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ Session::get('message') }}</strong></h5>
		</div>
	@endif
	@endif

	<div class="row">
		<div class="titles"><h4>{{ $room->name }}</h4></div>
		<div class="form-group">
			
			<div class="room-info-1">
				<span>{{ trans('room.list.created_by') }}&nbsp;</span><span style="font-weight: bold">{{ $room->belongs->mail }}</span></br>
				<span>{{ trans('room.list.last_meeting') }}&nbsp;</span><span>{{ $room->meetings()->orderBy('created_at','desc')->pluck('created_at') }}</span>
			</div>
			<div class="room-info-2">
				<span style="float:right" class="nb_participants">{{$room->participants->count() }}</span></br><span style="float:right">{{ trans('room.list.participants_count') }} </span>
			</div>
			<div style="clear: both;"></div>
		</div>
		<div class="form-group">
			
				<span style="float:left; margin-left:10px">
					
					@if (!$room->public)
					<i class="glyphicon glyphicon-lock"></i>
					@endif
					@if ($room->recording)
					<i class="glyphicon glyphicon-record"></i> {{ trans('room.show.record_yes') }}
					@endif
				</span>
				<span style="float:right">
					@if (\App\Http\Controllers\bbbController::running($room))
					<p style="color:white; background-color:red; padding:0.3em; display:inline;">{{ trans('room.list.running') }}</p>
					@endif
				</span>
			
		
		</div><div style="clear: both;"></div>	
		
		<div class="form-group show-pin"><span>{{ trans('room.show.pin') }}</span><span class="pin-number"> {{ $room->access_pin }}</span></div>
	</div>
	
	
	
	
	
	

{{-- admin actions --}}
@if ($participants)
	<div class="row">
		<div class="secondary-titles"><h5>{{ trans('room.show.participants') }}</h5></div>
		<div class="form-group">
			<ul class="list-group">
	@foreach ($participants as $part)
				<li class="list-group-item participants-show">
				@if ($part->moderator)
					<span><i class="glyphicon glyphicon-tower"></i>&nbsp;</span>
				@else 
					<span><i class="glyphicon glyphicon-pawn"></i>&nbsp;</span>
				@endif
				
					<span>{{ $part->mail }}</span>
				
				</li>
	@endforeach
			</ul>
		</div>
	</div>
@endif



@if ($recordings)
	
	<div class="row">
		<div class="secondary-titles"><h5>{{ trans('room.show.recordings') }}</h5></div>
		<div class="form-group">
			<div class="rec-items">
				<ul class="list-group">
			@foreach ($recordings as $rec)
					<li style="list-style-type:none">
						<div class="row">
							<div class="col-md-3 col-xs-3"><h5 style="color:#222;">{{ $rec['time'] }} </h5><h5 style="color:#5C5959; margin-top:2px;">{{ trans('room.show.duration') }}: {{ $rec['duration'] }}</h5></div>
							<div class="col-md-8 col-xs-9" style="float:right">
								<div class="col-md-1" style="float:right"><a target="_blank" href="{{ $rec['url'] }}"><button class="btn btn-recordings"><i class="glyphicon glyphicon-film"></i></button></a>
								</div>
			{{-- only owners can download,delete and share --}}
			@if ($check_owner)
								<div class="col-md-1 col-xs-1" style="float:right">{!! Form::open(['action' => ['recordingsController@delete', $rec['portal_id']], 'method' => 'delete']) !!}
								{!! Form::input('hidden', 'server_id', $rec['server_id']) !!}
									<button class='btn  btn-recordings' type='button' data-toggle="modal" data-target="#confirmDelete" data-title="{{ trans('room.show.destroy.recording') }}" data-message='{{ trans('room.show.destroy.recording.confirm') }}'>
										<i class='glyphicon glyphicon-trash'></i>
									</button>
								{!! Form::close() !!}
								</div>
						
								<div class="col-md-1 col-xs-1" style="float:right">{!! Form::open(['action' => ['recordingsController@share'], 'method' => 'post']) !!}
								{!! Form::input('hidden', 'server_id', $rec['server_id']) !!}
								{!! Form::input('hidden', 'rec_id', $rec['id']) !!}
								{!! Form::input('hidden', 'room_id', $room->id) !!}
						
									<button class='btn btn-recordings' type='button' id="recShare" data-toggle="modal" data-target="#share">
										<i class='glyphicon glyphicon-share-alt'></i> 
									</button>
								{!! Form::close() !!}
								</div>
						
								<div class="col-md-1 col-xs-1" style="float:right"><a href="{{ $rec['download_url'] }}"><button class="btn btn-recordings"><i class="glyphicon glyphicon-download-alt"></i></button></a></div>
								<div style="clear:both"></div>
								

							</div>
						</div>
						
						<div class="row col-md-12 col-xs-12" style="border-bottom:1px solid #cecece; margin-bottom:20px;">
									<div style="float:right">{!! Form::open(['action' => ['recordingsController@keep', $rec['portal_id']], 'method' => 'put', 'class' => 'rec_keep_form']) !!}
										{!! Form::checkbox('rec_keep','value',$rec['keep']) !!}
										{!! Form::close() !!}</div>
									<div style="clear:both"></div>
						
									<div style="float:right">{!! Form::open(['action' => ['recordingsController@publish', $rec['portal_id']], 'method' => 'put', 'class' => 'rec_publish_form']) !!}
										{!! Form::checkbox('rec_publish','value',$rec['published']) !!}
										{!! Form::close() !!}</div>
								</div>

			@endif{{-- end admin actions --}}
						
						
					</li>
					<div style="clear:both"></div>
		@endforeach
				</ul>
			</div>	
		</div>
	</div>
@endif
</div>

</div>

@stop


@section ('js')
	<script src="{!! asset('js/moment.min.js') !!}"></script>
	<script src="{!! asset('js/bootstrap-datetimepicker.min.js') !!}"></script>
	<script src="{!! asset('js/bootstrap-switch.min.js') !!}"></script>
	<script src="{!! asset('js/confirm.js') !!}"></script>
	<script src="{!! asset('js/share.js') !!}"></script>
	<script src="{!! asset('js/invite.js') !!}"></script>
	<script>
	//datetimepicker
	$('#date_modal').datetimepicker({
		//inline: true,
		//sideBySide: true,
		minDate: new Date(),
		format: "DD/MM/YYYY HH:mm",
	});
	//keep recording switch
	$(".rec_keep_form [type='checkbox']").bootstrapSwitch({
		'onText': '{{ trans('room.show.recording.to_keep') }}',
		'offText': '{{ trans('room.show.recording.to_delete') }}',
		//on state change we submit the form via ajax
		'onSwitchChange': function(event,state) {
			var form = $(this).closest('form');
			console.log(form);
			$.ajax({
				url: form.prop('action'),
				method: 'POST',
				data: {
					'_token': form.find( 'input[name=_token]').val(),
					'_method': form.find( 'input[name=_method]').val(),
					'keep': state
				      },
			        success: function(data){
					if(typeof data.status == 'undefined' || data.status != 'success'){
						//$(".rec_keep_form [type='checkbox']").bootstrapSwitch('toggleState', true, true);
						this.bootstrapSwitch('toggleState', true, true);
					}
				},
				error: function(error){
					this.bootstrapSwitch('toggleState', true, true);
				}
			});
		}
	});

	//publish recording switch
	$(".rec_publish_form [type='checkbox']").bootstrapSwitch({
		'onText': '{{ trans('room.show.recording.published') }}',
		'offText': '{{ trans('room.show.recording.unpublished') }}',
		//on state change we submit the form via ajax
		'onSwitchChange': function(event,state) {
			var form = $(this).closest('form');
			$.ajax({
				url: form.prop('action'),
				method: 'POST',
				data: {
					'_token': form.find( 'input[name=_token]').val(),
					'_method': form.find( 'input[name=_method]').val(),
					'publish': state
				      },
			        success: function(data){
					if(typeof data.status == 'undefined' || data.status != 'success'){
						this.bootstrapSwitch('toggleState', true, true);
					}
				},
				error: function(error){
					this.bootstrapSwitch('toggleState', true, true);
				}
			});
		}
	});

	</script>
@stop
