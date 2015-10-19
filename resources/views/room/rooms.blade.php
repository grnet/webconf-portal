@extends ('layouts.main')

@section ('content')
<div class="wc_content">

  <nav class="navbar navbar-inverse navbar-fixed-top" style="margin-top:50px; height:65px; background-color:white; border-color: #6B6464;
    box-shadow: 0px 3px 3px #5C5959; z-index:1001;">
	<div class="container">
		<a href={{ action('roomController@create') }}>
			<button type="button" class="btn btn-default" id="new-room">
				<span class="glyphicon glyphicon-plus">&nbsp;</span><span>{{ trans('room.rooms.add') }}</span>
		</button>
		</a>
  </div>
  </nav>
  <div class="rooms">
  <ul class="list-group">
  @foreach ($rooms as $room)
    <li class="list-group-item" style="border: none;">
	<a class="btn btn-info btn-block btn-lg wc_room_join_btn col-xs-12" href="{{ action('bbbController@join', [$room->id]) }}" id="join_{{ $room->id }}"><span class="glyphicon glyphicon-facetime-video" style="vertical-align:middle" aria-hidden="true"></span><span class="join-text">{{ trans('webconf.room.join') }}</span></a>
	<div class="rooms-properties">
	<a class="btn btn-info btn-block btn-lg wc_room_show_btn col-xs-12" href="{{ action('roomController@show', [$room->id]) }}" style="margin-top: 0px;"><span class="glyphicon glyphicon-menu-right pull-right" aria-hidden="true"></span>{{ $room->name }}</a>
	
	<div class="room-info" id="{{ $room->id }}">
		
		<div class="room-info-1">
		<span>{{ trans('room.list.created_by') }}&nbsp;</span><span style="font-weight: bold">{{ $room->belongs->mail }}</span></br>
		<span>{{ trans('room.list.last_meeting') }}&nbsp;</span><span>{{ $room->meetings()->orderBy('created_at','desc')->pluck('created_at') }}</span>
		</div>
		<div class="room-info-2">
		<span style="float:right" class="nb_participants">{{$room->participants->count() }}</span></br><span style="float:right">{{ trans('room.list.participants_count') }} </span>
		</div>
		<div style="clear: both;"></div>
		
		<div class="room-info-signs">
		<span style="float:left;" class="room_running_container">
		@if (\App\Http\Controllers\bbbController::running($room))
		<p class="room_running" style="color:white; background-color:red; padding:0.3em; display:inline;margin-left:10px">{{ trans('room.list.running') }}</p>
		@endif
		</span>
		<span style="float:right">
		
		@if ($room->recording)
			<i class="glyphicon glyphicon-record"></i>
		@endif
		@if (!$room->public)
			<i class="glyphicon glyphicon-lock"></i>
		@endif
		</span>
		</div>
		
	</div>
	<div style="clear: both;"></div>
	</div>
    </li>
	
  @endforeach
 
  </ul>
  </div>
</div>

@stop

@section ('js')
<script>
updateRunningRooms();

function updateRunningRooms(){
	$('.room-info').each(function(i, el){
		$.ajax({
			url: '/room/running/'+el.id,
			type: 'get',
			dataType: 'json',
			success: function(data){
				if(data.running){
					$(el).find('.room_running_container').html('<p class="room_running" style="color:white; background-color:red; padding:0.3em; display:inline;margin-left:10px">{{ trans('room.list.running') }}</p>');
				}
				else{
					$(el).find('.room_running').remove();
				}
			}
		});
	});
	setTimeout( updateRunningRooms, 30000);
}

</script>
@stop
