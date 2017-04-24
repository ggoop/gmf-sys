<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
  <tr>
    <td>
      <table bgcolor="#f0f0f0" width="600" align="center" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
          <td>
            <table bgcolor="#00a1cc" width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td style="height:50px;padding-left:30px;">
                  <a style="color: #fff;font-size:14px;text-decoration: none;" href="http://www.yyu9.com">U9</a>
                  <span style="color:#1bc5ef;padding:0px 10px">|</span>
                  <a style="color: #fff;font-size:14px;text-decoration: none;" href="#">智能制造</a>
                  <span style="color:#1bc5ef;padding:0px 10px">|</span>
                  <a style="color: #fff;font-size:14px;text-decoration: none;" href="#">经营者体验</a>
                </td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td style="padding:30px; color: #666666">
            @yield('content')
          </td>
        </tr>
        <tr>
          <td style="padding:15px 30px 10px 30px">
            <div style="border-top: 1px dashed #999;display: block;">&nbsp;</div>
          </td>
        </tr>
        <tr>
          <td style="padding:0px 30px 0px 30px;">
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td>
                    <!--二维码-->
                    <img src="{!!$message->embedData(\App\Libs\APIPub::SOQRCode($mainData->id), 'QrCode.png', 'image/png')!!}">
                  </td>
                  <td style="vertical-align: top;">
                    <table bgcolor="#00a65a" width="150" align="center" border="0" cellpadding="0" cellspacing="0">
                      <tr>
                        <td height="35" style="text-align: center;">
                          <a style="color:#fff;font-size:14px;text-decoration: none;" href="#">查看详情</a></td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
          </td>
        </tr>
        <tr>
          <td style="color: #999999;font-size: 12px; padding:20px;">
            <p>本邮件由系统自动发出，请勿直接回复！ 如果您有任何疑问或建议，请联系我们。</p>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>