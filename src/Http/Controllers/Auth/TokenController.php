<?php
namespace Gmf\Sys\Http\Controllers\Auth;

use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Models;
use Gmf\Sys\Passport\TokenRepository;
use Lcobucci\JWT\Parser as JwtParser;
use League\OAuth2\Server\AuthorizationServer;
use Psr\Http\Message\ServerRequestInterface;
use Validator;
use Zend\Diactoros\Response as Psr7Response;

class TokenController extends Controller {
  /**
   * The authorization server.
   *
   * @var \League\OAuth2\Server\AuthorizationServer
   */
  protected $server;

  /**
   * The token repository instance.
   *
   * @var \Gmf\Passport\TokenRepository
   */
  protected $tokens;

  /**
   * The JWT parser instance.
   *
   * @var \Lcobucci\JWT\Parser
   */
  protected $jwt;

  /**
   * Create a new controller instance.
   *
   * @param  \League\OAuth2\Server\AuthorizationServer  $server
   * @param  \Gmf\Passport\TokenRepository  $tokens
   * @param  \Lcobucci\JWT\Parser  $jwt
   * @return void
   */
  public function __construct(AuthorizationServer $server,
    TokenRepository $tokens,
    JwtParser $jwt) {
    $this->jwt = $jwt;
    $this->server = $server;
    $this->tokens = $tokens;
  }
  protected function getRequestParameter($parameter, ServerRequestInterface $request, $default = null) {
    $requestParameters = (array) $request->getParsedBody();

    return isset($requestParameters[$parameter]) ? $requestParameters[$parameter] : $default;
  }
  public function issueToken(ServerRequestInterface $request) {
    $token = false;
   
    $type = $this->getRequestParameter('type', $request, 'password');
    $input = (array) $request->getParsedBody();
    switch ($type) {
    case 'password':
      $token = $this->issueTokenByPassword($request);
      break;
    case 'ent':
      $token = $this->issueTokenByOpenid($request);
      break;
    case 'client_credentials':
      $input['grant_type'] = $type;
      $token = $this->server->respondToAccessTokenRequest($request->withParsedBody($input), new Psr7Response);
      return $token;
      break;
    }
    return $this->toJson($token);
  }
  public function issueTokenByPassword(ServerRequestInterface $request) {
    $input = array_only((array) $request->getParsedBody(), ['id', 'account', 'password']);
    $validator = Validator::make($input, [
      'account' => [
        'required',
      ],
      'password' => [
        'required',
      ],
    ]);
    if ($validator->fails()) {
      return $this->toError($validator->errors());
    }
    $user = app('Gmf\Sys\Bp\UserAuth')->login($this, $input);
    return app('Gmf\Sys\Bp\Auth\Token')->issueToken($user);
  }
  /**
   * 通过 用户 openid+ 应用 openid+ 企业 openid+ 企业应用 token 获取token
   * {token:{access_token:'',expires_in:'',token_type:'Bearer'},signature:'sss'}
   */
  public function issueTokenByOpenid(ServerRequestInterface $request) {
    $input = (array) $request->getParsedBody();
    Validator::make($input, [
      'ent_openid' => 'required',
      'user_openid' => 'required',
      'token' => 'required',
    ])->validate();

    $params = [];
    $ent = Models\Ent\Ent::where('openid', $input['ent_openid'])->first();
    if (!empty($ent)) {
      $params['entId'] = $ent->id;
    }
    $user = Models\User::where('openid', $input['user_openid'])->first();
    if (!empty($user)) {
      $params['userId'] = $user->id;
    }
    $params['token'] = $input['token'];
    return app('Gmf\Sys\Bp\Auth\Token')->issueTokenByOpenid($params);
  }
}
