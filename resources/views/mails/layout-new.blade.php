<!DOCTYPE html>
<html>
<head>
    <title></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>

    <style type="text/css">
        /* FONTS */

        html, body {
            margin: 0 !important;
            padding: 0 !important;
            height: 100% !important;
            width: 100% !important;
        }

        /* CLIENT-SPECIFIC STYLES */
        body, table, td, a {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table, td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        img {
            -ms-interpolation-mode: bicubic;
        }

        /* RESET STYLES */
        img {
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }

        body {
            height: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }

        /* iOS BLUE LINKS */
        a[x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }

        a {
            text-decoration: none;
        }

        a:hover {
            color: {{$company->primary_color_lighter ?? '#c55a9059'}} !important;
        }

        #a_link span:hover {
            color: {{$company->primary_color_lighter ?? '#c55a9059'}} !important;
            text-decoration: none;
        }

        #a_button:hover {
            background-color: {{$company->primary_color_lighter ?? '#c55a9059'}};
        }

        table tr td {
            padding-right: 30px;
            padding-left: 30px;
        }

        p {
            font-size: 16px;
        }

        p, h1, h2, h3, h4, h5, h6, span {
            padding: 0;
            margin: 0;
        }

        .email-lighter-background {
            background-color: {{$company->primary_color_lighter ?? '#c55a9059'}};
        }

        /* MOBILE STYLES */
        @media screen and (max-width: 726px) {
            h1 {
                font-size: 28px !important;
                line-height: 28px !important;
            }

            h2 {
                font-size: 22px;
            }

            h3 {
                font-size: 18px;
            }

            h4 {
                font-size: 16px;
            }

            p {
                font-size: 15px;
            }

            .wrapper {
                width: 100% !important;
            }

            .email-header {
                text-align: center;
            }
        }

        @media screen and (max-width: 440px) {
            table tr td {
                padding: 0;
            }

            .email-cell {
                padding: 0 10px !important;
            }

            h2, h3 {
                font-size: 18px;
            }

            .min-height {
                height: 20px !important;
            }

            .email-background {
                background-color: #f4f4f4 !important;
            }

            .email-header {
                color: black !important;
            }
        }

        body {
            margin: 0 !important;
            padding: 0 !important;
            font-family: sans-serif, 'Times New Roman', Times, serif;
        }

        /* ANDROID CENTER FIX */
        div[style*="margin: 16px 0;"] {
            margin: 0 !important;
        }
    </style>
</head>
<body style="background-color: #f4f4f4; margin: 0 !important; padding: 0 !important;  mso-line-height-rule: exactly;" width="100%">
<center style="width: 100%; ">
    <div style="margin: 0 auto;" class="email-container">
        <table border="0" align="center" bordercolor="" cellpadding="0" cellspacing="0" width="100%" style="margin: auto">
            <tr>
                <td align="center" class="email-background email-lighter-background">
                    <div style="max-width: 650px; margin: 0 auto;" class="email-container">
                        <!--[if mso]>
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="650" align="center">
                            <tr>
                                <td><![endif]-->
                        <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" class="wrapper">
                            <tr>
                                <td bgcolor="transparent" height="60" class="min-height"></td>
                            </tr>
                            <tr>
                                <td bgcolor="transparent" style="padding: 0" class="email-header">
                                    &emsp;&emsp;<img src="{{ $company->logo ? asset($company->logo) : asset('images/logo3.png') }}" width="200"
                                                     alt="{{$company->name}}"/>&emsp;&emsp;
                                </td>
                            </tr>
                            <tr>
                                <td bgcolor="transparent" height="23"></td>
                            </tr>
                            <tr>
                                <td style="background-color: transparent; font-size: 18px;color: white;padding: 0">
                                    <h3 style="padding: 0; margin: 0;color: #ffffff" class="email-header">&emsp;&emsp;@lang('template.resident_portal', [], $lang)&emsp;&emsp;</h3>
                                </td>
                            </tr>
                            <tr>
                                <td bgcolor="transparent" height="23"></td>
                            </tr>
                        </table>
                        <!--[if mso]>
                        </td>
                        </tr>
                        </table>
                        <![endif]-->
                    </div>
                </td>
            </tr>
            <tr>
                <td align="center" class="email-lighter-background">
                    <div style="max-width: 650px; margin: 0 auto;" class="email-container">
                        <!--[if mso]>
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="650"
                               align="center">
                            <tr>
                                <td>
                        <![endif]-->

                        <table border="0" cellpadding="0" cellspacing="0" width="100%" class="wrapper">
                            <tr>
                                <td height="40" width="100%" bgcolor="#ffffff"
                                    style="border-radius: 4px 4px 0 0;-webkit-border-radius: 4px 4px 0 0; -moz-border-radius:  4px 4px 0 0;  -ms-border-radius:  4px 4px 0 0;"
                                    class="min-height"></td>
                            </tr>
                        </table>
                        <!--[if mso]>
                        </td>
                        </tr>
                        </table>
                        <![endif]-->
                    </div>
                </td>
            </tr>
            <tr>
                <td bgcolor="#f4f4f4" align="center">
                    <div style="max-width: 650px; margin: 0 auto;" class="email-container">
                        <!--[if mso]>
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="650"
                               align="center">
                            <tr>
                                <td>
                        <![endif]-->
                        <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" class="wrapper">
                            <tr>
                                <td bgcolor="#ffffff" align="center"
                                    style="color: #000000; font-size: 18px; font-weight: 400; line-height: 25px;"
                                    class="email-cell">
                                        @yield('body')
                                </td>
                            </tr>
                        </table>
                        <!--[if mso]>
                        </td>
                        </tr>
                        </table>
                        <![endif]-->
                    </div>
                </td>
            </tr>
        </table>
        <!--[if mso]>
        </td>
        </tr>
        </table>
        <![endif]-->
    </div>
    </td>
    </tr>
    <tr>
        <td bgcolor="#f4f4f4" align="center" cellpadding="0" cellspacing="0">
            <div style="max-width: 650px; margin: 0 auto;" class="email-container">
                <!--[if mso]>
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="650"
                       align="center">
                    <tr>
                        <td>
                <![endif]-->
                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="wrapper">
                    <tr>
                        <td height="40" width="100%" bgcolor="#ffffff"
                            style="border-radius:  0 0 4px 4px;-webkit-border-radius:  0 0 4px 4px; -moz-border-radius:   0 0 4px 4px;  -ms-border-radius:   0 0 4px 4px;"></td>
                    </tr>
                </table>
                <!--[if mso]>
                </td>
                </tr>
                </table>
                <![endif]-->
            </div>
        </td>
    </tr>
    <tr>
        <td bgcolor="#f4f4f4" align="center">
            <div style="max-width: 650px; margin: 0 auto;" class="email-container">
                <!--[if mso]>
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="650"
                       align="center">
                    <tr>
                        <td>
                <![endif]-->
                <table align="center" border="0" width="100%" cellpadding="0" cellspacing="0" class="wrapper">
                    <tr>
                        <td height="20" bgcolor="transparent"></td>
                    </tr>
                    <tr>
                        <td bgcolor="transparent" align="center"
                            style="color: grey; font-size: 12px; font-weight: 400; line-height: 25px;">
                            @lang('template.generated_email_for_user', ['userName' =>  $userName], $lang)
                        </td>
                    </tr>
                    <tr>
                        <td bgcolor="transparent" height="15"></td>
                    </tr>
                    <tr>
                        <td bgcolor="transparent" align="center"
                            style="color: #000000; font-size: 14px; font-weight: 400; line-height: 1.3;padding: 0 10px"
                            class="email-cell">
                                    <span>
                                             @lang('template.generated_email_for_company', ['companyName' =>  $companyName], $lang)
                                   </span>
                        </td>
                    </tr>
                    <tr>
                        <td bgcolor="transparent" height="20" style="z-index: 1;"></td>
                    </tr>
                    <tr>
                        <td bgcolor="transparent" align="center" height="60"
                            style="color: #000000; font-size: 18px; font-weight: 400; line-height: 60px;z-index:999">
                            <img src="{{ $company->logo ? asset($company->logo) : asset('images/logo3.png') }}"
                                 width="100" height="30" alt="{{$company->name}}" style="display: block;"/>
                        </td>
                    </tr>
                    <tr>
                        <td bgcolor="transparent" height="10"></td>
                    </tr>
                    <tr>
                        <td bgcolor="transparent" align="center"
                            style="color: #000000; font-size: 14px; font-weight: 400; line-height: 1;">
                            Rorschacher Str. 302, 9016 St. Gallen
                        </td>
                    </tr>
                    <tr>
                        <td height="20"></td>
                    </tr>
                    <tr>
                        <td bgcolor="transparent" align="center" style="color: #000000; font-size: 14px; font-weight: 400; line-height: 25px;">
                            {!! __('template.links', [], $lang) !!}
                        </td>
                    </tr>
                    <tr>
                        <td height="30"></td>
                    </tr>
                </table>
                <!--[if mso]>
                </td>
                </tr>
                </table>
                <![endif]-->
            </div>
        </td>
    </tr>
    </table>
    </div>
</center>
</body>
</html>
