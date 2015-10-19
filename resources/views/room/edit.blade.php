@extends ('layouts.main')

@section ('content')

<div class="wc_content" style="margin-top:40px">	
  @include ('errors.list')

  @if(isset($room))
    {!! Form::model($room, ['action' => ['roomController@update', $room->id], 'method' => 'put']) !!}
  @else
    {!! Form::open(array('action' => 'roomController@store')) !!}    
  @endif

  @foreach ($errors->get('email[0]') as $message)
	<span>{{ $message }}</span>
  @endforeach
  
 <!-- <div class="row">
  
	<div class="col-xs-12 form-group">
		{!! Form::label('name' , trans('room.edit.name')) !!}&nbsp
		{!! Form::text('name') !!}
		<br/>
	</div>-->
	 <div class="row">
  
	<div class="col-xs-12 titles">
		{!! Form::label('name' , trans('room.edit.name')) !!}&nbsp
		<span class="input-room">{!! Form::text('name') !!}</span>
		<br/>
	</div>
	
	</div>
   <div class="row">
	<div class="col-xs-12 form-group">
		{!! Form::checkbox('recording' , 1) !!}&nbsp
		<span id="properties_label">{!! Form::label('recording' , trans('room.edit.recording')) !!}</span>
		<br/>
	</div>
	</div>
	<div class="row">
	<div class="col-xs-12 form-group">
		{!! Form::checkbox('public' , 1) !!}&nbsp
		<span id="properties_label">{!! Form::label('public' , trans('room.edit.public')) !!}</span>
		<br/>
	</div>
	</div>
	<div class="row">
		<div class="col-xs-12 form-group">
            <table id="participants_table" class="table table-responsive table-bordered">
					{!! Form::label('name' , trans('room.edit.participants')) !!} <br/>
					{!! Form::textarea('room_participants', null, array('id'=> 'room_participants', 'class'=>'form-control')) !!}
					<div class="secondary-btn">{!! Form::button(trans('room.edit.add_participants'), array('id'=>'add_participants_btn', 'class'=>'btn btn-default btn-xs')) !!}</div></br>
			
                        <thead><tr><th class="col-xs-6">{{ trans('room.edit.participant_email') }}</th><th class="col-xs-6">{{ trans('room.edit.moderator') }}</th><th class="col-xs-6"></th></tr></thead>
						<tbody>
				
						@foreach ($emails as $i => $email)
							<?php  $myclass = $errors->get('email.'.$i)?"class=has-error":'' ?>
							<tr {{ $myclass }}><td><input class="form-control" name="email[]" type="text" value="{{ $email[0] }}" placeholder="Disabled input here..." readonly>{!! $myclass!=''?'<span style="color: #A94442;">Invalid email</span>':''!!}</td><td><input type="checkbox" name="moderator[]" value="{{ $email[0] }}" aria-label="..."{{ $email[1]==1?' checked':''}}></td><td><button type="button" class="btn btn-primary btn-xs" onclick="removeParticipant(this);"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button></td></tr>
						@endforeach

                        </tbody>
            </table>
		
                
        </div>
	</div>
	

	<nav class="navbar navbar-inverse navbar-fixed-top" style="margin-top:50px; height:65px; background-color:white; border-color: #6B6464;
    box-shadow: 0px 3px 3px #5C5959; z-index:1001;">
        <div class="container">{!! Form::submit(trans('webconf.room.submit'), [ 'id'=> 'my_submit', 'class' => 'btn btn-default']); !!}</div>
  {!! Form::close() !!}
   </nav>
  
</div>

@stop

@section ('js')
<script type="text/javascript">
  function removeParticipant(elem){
	//we remove the tr parent
	$(elem).parent().parent().remove();

  }

  function alreadyThere(myline){
	var numfound = $('#participants_table tbody tr input[value="'+myline+'"]').length;
	//alert('found:'+numfound);
	return numfound;
  }

  function validateEmail(email) {
    var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
    return re.test(email);
  }

  function addEmailsInTable(){
	//alert('lolo');

        var lines = $('textarea').val().replace(/,/g,'\n').split('\n');
        for(var i = 0;i < lines.length;i++){
                var myline = lines[i].trim().toLowerCase();
                if(myline == '' || alreadyThere(myline) || !validateEmail(myline))
                //if(myline == '' || alreadyThere(myline))
                        continue;

                var row='<tr><td><input class="form-control" name="email[]" type="text" value="'+myline+'" placeholder="Disabled input here..."></td><td><input type="checkbox" name="moderator[]" value="'+myline+'" aria-label="..."></td><td><button type="button" class="btn btn-primary btn-xs" onclick="removeParticipant(this);"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button></td></tr>';

            $('#participants_table tbody').append(row);
            //alert(lines[i]);
        }       

        //empty textarea ...
        $('textarea').val('');
  }

  $("#add_participants_btn").click(addEmailsInTable);
  $("#my_submit").click(addEmailsInTable);

</script>
@stop
