<?php namespace Frozennode\Administrator\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

class ValidateAdmin {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		$permission = config('administrator.permission');
		$result = call_user_func([app($permission[0]), $permission[1]]);
		if (!$response = $result)
		{
			$loginUrl = url(config('administrator.login_path', 'user/login'));
			$redirectKey = config('administrator.login_redirect_key', 'redirect');
			$redirectUri = $request->url();

			return redirect()->guest($loginUrl)->with($redirectKey, $redirectUri);
		}

		//otherwise if this is a response, return that
		else if (is_a($response, JsonResponse::class) || is_a($response, Response::class))
		{
			return $response;
		}

		//if it's a redirect, send it back with the redirect uri
		else if (is_a($response, RedirectResponse::class))
		{
			$redirectKey = config('administrator.login_redirect_key', 'redirect');
			$redirectUri = $request->url();

			return $response->with($redirectKey, $redirectUri);
		}

		return $next($request);
	}

}