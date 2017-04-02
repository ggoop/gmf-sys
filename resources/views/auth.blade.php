<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
        <title>授权中心</title>
       <link rel="stylesheet" type="text/css" href="/css/app.css"/>
    </head>
    <body>
      <header></header>
      <div id="gmfApp" class="gauth">
        <div class="oauth-paper">
          <div class="paper-content" ng-class="{'shrink':oauthLogin.shrink}">
            <div class="social-title">使用社交网络帐户登录</div>
            <div class="social-body">
              <div class="social-item social-qq">
                <i class="fa fa-qq"></i>
              </div>
              <div class="social-item social-weixin">
                <i class="fa fa-weixin"></i>
              </div>
              <div class="social-item social-github">
                <i class="fa fa-github"></i>
              </div>
              <div class="social-item social-google">
                <i class="fa fa-google-plus"></i>
              </div>
              <div class="social-item social-facebook">
                <i class="fa fa-facebook"></i>
              </div>
            </div>
            <div class="email-title" ng-click="oauthLogin.onShrink($event)">或者使用电子邮箱</div>
            <div class="email-body">
              <md-tabs>
                <md-tab label="登录" active="true">
                    <form name="loginForm" novalidate>
                    <div class="row">
                      <div class="input-field col s12">
                        <input name="password" type="password" placeholder="密码" class="validate">
                        <div ng-messages="loginForm.password.$error">
                          <div ng-message-exp="['required', 'minlength', 'maxlength']">
                            密码必须在6-50位之间
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="input-field col s12">
                        <input name="email" type="email" placeholder="密码" class="validate">
                        <div ng-messages="loginForm.email.$error">
                          <div ng-message-exp="['required', 'minlength', 'maxlength', 'pattern']">
                            电子邮件必须在4-50位之间，且是完整的电子邮件地址!
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <a class="forgot-link" href="/oauth/password">忘记密码?</a>
                    </div>
                    <div class="row">
                      <button type="submit" class="md-raised md-primary" ng-disabled="loginForm.$invalid">登录</button>
                    </div>
                  </form>
                </md-tab>
                <md-tab label="注册">
                    <form name="registerForm" novalidate>
                    <div class="row">
                      <div class="input-field col s12">
                        <input name="nickname" type="text" placeholder="姓名" class="validate">
                        <div ng-messages="loginForm.email.$error">
                          <div ng-message-exp="['required', 'minlength', 'maxlength', 'pattern']">
                            姓名需要在2-20位之间
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="input-field col s12">
                        <input name="password" type="password" placeholder="密码" class="validate">
                        <div ng-messages="loginForm.password.$error">
                          <div ng-message-exp="['required', 'minlength', 'maxlength']">
                            密码必须在6-50位之间
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="input-field col s12">
                        <input name="email" type="email" placeholder="密码" class="validate">
                        <div ng-messages="loginForm.email.$error">
                          <div ng-message-exp="['required', 'minlength', 'maxlength', 'pattern']">
                            电子邮件必须在4-50位之间，且是完整的电子邮件地址!
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <button type="submit" class="md-raised md-primary" ng-disabled="loginForm.$invalid">注册</button>
                    </div>
                  </form>
                </md-tab>
              </md-tabs>
            </div>
          </div>
        </div>
      </div>
      <footer></footer>
      <script src="/js/manifest.js"></script>
      <script src="/js/vendor.js"></script>
      <script src="/js/app.js"></script>
    </body>
</html>