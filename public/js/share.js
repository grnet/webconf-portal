$('#share').on('show.bs.modal', function (e) {
     // Pass form reference to modal for submission on yes/ok
      var form = $(e.relatedTarget).closest('form');
      $(this).find('.modal-footer #share').data('form', form);
});

<!-- Form confirm (yes/ok) handler, submits form -->
$('#share').find('.modal-footer #share').on('click', function(){
      $('input[name="recShareEmail"]').val($('input[name="recShareEmailModal"]').val());
      $(this).data('form').submit();
});
