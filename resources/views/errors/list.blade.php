@if (count($errors) > 0)
    <div class="alert alert-danger">
        <ul>
	    <?php $email_error_printed_once = false; ?>
            @foreach ($errors->all() as $error)
		@if(strpos($error, 'must be a valid email address')!==FALSE ) 
    		    @if(!$email_error_printed_once)
			<li>Invalid email(s) provided</li>
			<?php $email_error_printed_once = true; ?>
		    @endif
		@else
		    <li>{{ $error }}</li>
		@endif
            @endforeach
        </ul>
    </div>
@endif
