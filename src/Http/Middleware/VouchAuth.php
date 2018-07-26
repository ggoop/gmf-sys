<?php
namespace Gmf\Sys\Http\Middleware;

use Closure;
use Exception;
use GAuth;
use Auth;

class VouchAuth
{
  public function handle($request, Closure $next)
  {
    if (!$request->expectsJson() && $request->isMethod('GET') && $vcode = $request->query('vcode')) {
      if ($c = app('Gmf\Sys\Bp\VCode')->checker($vcode, 'auth.login')) {
        app('Gmf\Sys\Bp\VCode')->delete($vcode);
        if ($c->user_id) {
          $user = config('gmf.user.model')::find($c->user_id);
          if ($user && Auth::loginUsingId($user->id)) {
            $url = url()->current();
            $p = http_build_query($request->except('vcode'));
            if ($p) $url = $url . '?' . $p;
            return redirect($url);
          }
        }
      }
      throw new Exception('vcode error!');
    }
    return $next($request);
  }
}
