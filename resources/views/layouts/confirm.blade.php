<!-- Modal Dialog -->
<div class="modal fade" id="confirmDelete" role="dialog" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close glyphicon glyphicon-remove" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title">Delete Parmanently</h4>
      </div>
      <div class="modal-body">
        <p>Are you sure about this ?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('webconf.confirm.cancel') }}</button>
        <button type="button" class="btn btn-default" id="confirm">{{ trans('webconf.confirm.delete') }}</button>
      </div>
    </div>
  </div>
</div>
