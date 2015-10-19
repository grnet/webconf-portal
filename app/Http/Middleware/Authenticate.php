<?php namespace App\Http\Middleware;

//tsipizic simplesamlphp
require_once('/usr/share/simplesamlphp/lib/_autoload.php');

use Closure;
use Illuminate\Contracts\Auth\Guard;

use App\User;
use Auth;

class Authenticate {	

	/**
	 * The Guard implementation.
	 *
	 * @var Guard
	 */
	protected $auth;

	/**
	 * Create a new filter instance.
	 *
	 * @param  Guard  $auth
	 * @return void
	 */
	public function __construct(Guard $auth)
	{
		$this->auth = $auth;
	}

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		if ($this->auth->guest())
		{
			if ($request->ajax())
			{
				return response('Unauthorized.', 401);
			}
			else
			{
				//return redirect()->guest('auth/login')
				//tsipizic for SAML
				//login user and get attributes
				$as = new \SimpleSAML_Auth_Simple('default-sp');
				$as->requireAuth();
				$attributes = $as->getAttributes();
				//create user if he does not exist and log him in
				$mail = $attributes['mail'][0];
				$db_user = User::where('mail', $mail)->first();
				if($db_user){
					Auth::login($db_user);
				}
				else{
					$user = new User();
					$user->mail = $mail;
					$user->save();
					Auth::login($user);
				}

			}
		}

		return $next($request);
	}

}
