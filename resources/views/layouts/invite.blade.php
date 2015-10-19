<!-- Modal Dialog -->
<div class="modal fade" id="invite" role="dialog" aria-labelledby="inviteLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">{{ trans('room.invite.modal.title') }}</h4>
      </div>
      <div class="modal-body">
	<div>
  	 <div style="margin-top:10px;">{!! Form::label('name' , trans('room.show.invite')) !!}</div> <br/>
	  {!! Form::textarea('invite_text_modal', null, array('class'=>'form-control')) !!}
	  {!! Form::label('date_modal' , trans('room.show.date')) !!} <br/>
	  <div class="controls" style="position: relative">
		  {!! Form::input('date_modal', 'date_modal', null, ['class' => 'form-control']) !!}
	  </div>
	</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('room.invite.modal.cancel') }}</button>
        <button type="button" class="btn btn-default" id="invite">{{ trans('room.invite.modal.share') }}</button>
      </div>
    </div>
  </div>
</div>
