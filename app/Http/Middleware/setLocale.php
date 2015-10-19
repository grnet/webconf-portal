<?php namespace App\Http\Middleware;

use Closure;
use Session;
use Redirect;

class setLocale {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		//tsipizic for unit testing we set language to english by default
		if($request->getHost() == 'localhost'){
	                app()->setLocale('en');
        	        return $next($request);
		}


		if(in_array($request->segment(1), config('app.locale'))){
			Session::put('locale', $request->segment(1));
			return Redirect::to(substr($request->path(), 3));
		}

		// Check if the session has the language
		if(!Session::has('locale')) {
			Session::put('locale', config('app.fallback_locale'));
		}

		app()->setLocale(Session::get('locale'));
		return $next($request);
	}

}
