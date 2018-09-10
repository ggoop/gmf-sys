<?php

namespace Gmf\Sys\Http\Controllers\OAuth;

use DB;
use Auth;
use Gmf\Sys\Http\Resources;
use Gmf\Sys\Models;
use Illuminate\Http\Request;
use Validator;
use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Passport\ClientRepository;
use Carbon\Carbon;
class AuthorizeController extends Controller
{
  protected $clients;
  public function __construct(ClientRepository $clients)
  {
    $this->clients = $clients;
  }
  public function getAuthorize(Request $request)
  {
    $input = $request->all();
    $validator = Validator::make($input, [
      'client_id' => 'required',
      'redirect_uri' => 'required',
      'response_type' => 'required',
    ]);
    if ($validator->fails()) {
      return 'hello!';
    }
    $clientId = $input['client_id'];
    $redirect_uri = $input['redirect_uri'];
    $c = $this->clients->findActive($clientId);
    if (empty($c)) {
      return '应用未授权！';
    }
    if (!Auth::id()) {
      return redirect('/auth/login?continue=' . urlencode($request->fullUrl()));
    }
    $datas = [
      'user_id' => Auth::id(),
      'client_id' => $clientId,
      'expire_time' => Carbon::now()->addMinute()
    ];
    $code = app('Gmf\Sys\Bp\VCode')->generate('oauth.authorize', $datas)->id;
    if (str_contains($redirect_uri, '?')) {
      $redirect_uri .= '&code=' . $code;
    } else {
      $redirect_uri .= '?code=' . $code;
    }
    return redirect($redirect_uri);
  }
}