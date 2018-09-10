<?php

namespace Gmf\Sys\Http\Controllers;

use GAuth;
use Illuminate\Http\Request;

class UserController extends Controller
{
  public function index(Request $request)
  {
    $query = config('gmf.user.model')::where('id', '!=', '')->whereIn('id', GAuth::ids());
    $data = $query->get();
    return $this->toJson($data);
  }
  public function show(Request $request, string $id)
  {
    $query = config('gmf.user.model')::whereNotNull('account')->whereNotNull('openid');
    $data = $query->where(function ($query) use ($id) {
      $query->where('id', $id)->orWhere('openid', $id);
    })->first();
    return $this->toJson($data);
  }
}
