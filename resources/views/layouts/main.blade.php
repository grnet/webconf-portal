<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="/favicon.ico">

    <title>@yield('title', 'webconf project')</title>

    <!-- Bootstrap core CSS -->
    <link href="{!! asset('bootstrap-3.3.5/css/bootstrap.min.css') !!}" rel="stylesheet">
	
    <!-- Custom styles for this template -->
    <link href="{!! asset('bootstrap-3.3.5/css/starter-template.css') !!}" rel="stylesheet">
	
	
    @section('css')
	<link href="{!! asset('css/webconf.css') !!}" rel="stylesheet">
    @show

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <nav class="navbar navbar-inverse navbar-fixed-top">
	 <a class="navbar-brand" href="#">@yield('proj_name', 'Webconf')</a>
      
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
         
	</div>
        <div id="navbar" class="collapse navbar-collapse">
		<div class="center">
          <ul class="nav navbar-nav">
	    <li class="{{ Request::is('/') ? 'active' : '' }}"><a href="{{ action('mainController@index') }}">{{ trans('webconf.menu.home') }}</a></li>
	    @if (Auth::guest())
		<li class="{{ Request::is('room/withPin') ? 'active' : '' }} "><a href="{{ action('roomController@withPin') }}">{{ trans('webconf.menu.withpin') }}</a></li>
	    @else
	         <li class="{{ (Request::is('room/own') || (Request::segment(2) == 'show'  && (strpos(URL::previous(),'/room/own') || strpos(URL::previous(),'/room/edit')) ) || Request::segment(2) == 'edit' ) ? 'active' : '' }}"><a href="{{ action('roomController@own') }}">{{ trans('webconf.menu.own') }}</a></li>
                 <li class="{{ (Request::is('room/invited') || (Request::segment(2) == 'show' && strpos(URL::previous(),'/room/invited') )) ? 'active' : '' }}"><a href="{{ action('roomController@invited') }}">{{ trans('webconf.menu.invited') }}</a></li>
	         <li class="{{ (Request::is('room/public') || (Request::segment(2) == 'show' && strpos(URL::previous(),'/room/public') )) ? 'active' : '' }}"><a href="{{ action('roomController@publicr') }}">{{ trans('webconf.menu.public') }}</a></li>
	    @endif
	    <li class="{{ Request::is('help') ? 'active' : '' }}"><a href="{{ action('mainController@help') }}">{{ trans('webconf.menu.help') }}</a></li>
		</ul>
		</div>
		<div class="right">
		<ul class="nav navbar-nav">
	    @if (Auth::guest())
		<li><a href="/saml2/login">{{ trans('webconf.login') }}</a></li>
	    @else
		<li><a href="/saml2/logout">{{ trans('webconf.logout') }} [ {{ Auth::user()->mail }} ]</a></li>
	    @endif
	    @if (Session::get('locale') == 'en')
	    	<li><a href="/el/{!! Request::path() !!}">el</a></li>
	    @else
	    	<li><a href="/en/{!! Request::path() !!}">en</a></li>
	    @endif
          </ul>
		  </div>
        </div><!--/.nav-collapse -->	
      
    </nav>

    <div class="container">

      <div class="starter-template">
	@section('content')
            <h1>Bootstrap starter template</h1>
            <p class="lead">Use this document as a way to quickly start any new project.<br> All you get is this text and a mostly barebones HTML document.</p>
	@show
      </div>

    </div><!-- /.container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

    <script src="{!! asset('bootstrap-3.3.5/js/bootstrap.min.js') !!}"></script>	
	
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="{!! asset('bootstrap-3.3.5/js/ie10-viewport-bug-workaround.js') !!}"></script>
	
    @section('js')
    @show

  </body>
</html>

