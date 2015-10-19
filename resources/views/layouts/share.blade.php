<!-- Modal Dialog -->
<div class="modal fade" id="share" role="dialog" aria-labelledby="shareLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">{{ trans('room.recording.share.modal.title') }}</h4>
      </div>
      <div class="modal-body">
	@foreach ($participants as $part)
				<li class="list-group-item">
					<span>{{ $part->mail }}</span>
				</li>
	@endforeach
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('room.recording.share.modal.cancel') }}</button>
        <button type="button" class="btn btn-default" id="share">{{ trans('room.recording.share.modal.share') }}</button>
      </div>
    </div>
  </div>
</div>
