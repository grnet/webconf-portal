$('#invite').on('show.bs.modal', function (e) {
     // Pass form reference to modal for submission on yes/ok
      var form = $(e.relatedTarget).closest('form');
      $(this).find('.modal-footer #invite').data('form', form);
});

<!-- Form confirm (yes/ok) handler, submits form -->
$('#invite').find('.modal-footer #invite').on('click', function(){
      $('input[name="invite_text"]').val($('textarea[name="invite_text_modal"]').val());
      $('input[name="date"]').val($('input[name="date_modal"]').val());
      $(this).data('form').submit();
});
