<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml'>

<head>
  <meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
  <title>D2D Leads - Password Reset</title>
  <meta name='viewport' content='width=device-width, initial-scale=1.0' />
</head>

<body style='margin: 0; padding: 0'>
  <table border='0' cellpadding='0' cellspacing='0' width='100%'>
    <tr>
      <td style='padding: 10px 0 30px 0'>
        <table align='center' border='0' cellpadding='0' cellspacing='0' width='600' style='border: 1px solid #cccccc; border-collapse: collapse'>
          <tr>
            <td width='100%' bgcolor='#ffffff' style='
                  padding: 5px 0 5px 0;
                  color: #153643;
                  font-size: 12px;
                  font-weight: bold;
                  font-family: Arial, sans-serif;
                '>
              <table border='0' cellpadding='0' cellspacing='0' width='100%'>
                <tr>
                  <td align='right' style='padding: 18px 5px 0 0; font-size: 16px'>
                    <h1>NRG</h1>
                  </td>
                  <td>
                    <img alt='NRG logo' src='<?= "https://" .
                      $_SERVER[
                        'HTTP_HOST'
                      ] ?>/images/nrg-logo-mark.svg' alt='NRG' width='30' height='28' style='display: block' />
                  </td>
                  <td align='center' style='font-size: 17px; padding: 0 0 0 10px'>
                    <h1>D2D Leads</h1>
                  </td>
                  <td align='right' style='padding: 0 10px 0 0'>
                    <img src='<?= "https://" .
                      $_SERVER[
                        'HTTP_HOST'
                      ] ?>/images/reliant-logo.png' alt='Reliant Logo' width='130' height='50' style='display: block' />
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td width='100%' bgcolor='#ffd200' style='padding: 5px 0 5px 0'></td>
          </tr>
          <tr>
            <td bgcolor='#ffffff' align='center' style='padding: 40px 30px 40px 30px'>
              <table border='0' cellpadding='0' cellspacing='0' width='100%'>
                <tr>
                  <td style='
                        padding: 20px 0 30px 0;
                        color: #153643;
                        font-family: Arial, sans-serif;
                        font-size: 16px;
                        line-height: 20px;
                      '>
                    A password reset has been requested for user <?= $values[
                      'email'
                    ] ?>.
                    <br /><br />
                    <b>To reset your password click
                      <a href='<?= $values[
                        'url'
                      ] ?>' alt='Reset password' title='Reset password'>here</a>.</b>
                    <br /><br />
                    If you did not submit a password request please ignore
                    this email.
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td bgcolor='#03aeef' style='padding: 30px 30px 30px 30px'>
              <table border='0' cellpadding='0' cellspacing='0' width='100%'>
                <tr>
                  <td style='
                        color: #ffffff;
                        font-family: Arial, sans-serif;
                        font-size: 14px;
                      ' width='75%'>
                    &reg; Reliant | NRG 2020<br />
                  </td>
                  <td align='right' width='25%'>
                    <table border='0' cellpadding='0' cellspacing='0'>
                      <tr>
                        <td style='
                              font-family: Arial, sans-serif;
                              font-size: 14px;
                              font-weight: bold;
                            '>
                          <a href='<?= "https://" .
                            $_SERVER['HTTP_HOST'] ?> style='color: #ffffff'>
                          d2d.nrg.com
                          </a>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>

</html>