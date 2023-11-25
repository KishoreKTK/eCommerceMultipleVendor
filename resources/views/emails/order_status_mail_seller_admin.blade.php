<!DOCTYPE html
   PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
      <meta name="format-detection" content="telephone=no" />
      <meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE" />
      <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700&display=swap" rel="stylesheet">
      <title>{{ config('app.name') }}</title>
      <style type="text/css">
        body {
            width: 100% !important;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
            margin: 0;
            padding: 0;
        }
        .ReadMsgBody {
         width: 100%;
        }
        .ExternalClass {
         width: 100%;
        }
        .backgroundTable {
         margin: 0 auto;
         padding: 0;
         width: 100% !important;
        }
        .ExternalClass * {
         line-height: 115%;
        }
        p {
         margin: 0;
        }
        div.tpl-content {
         padding: 0 !important;
        }
        span.preheader {
         display: none;
         font-size: 1px;
         visibility: hidden;
         opacity: 0;
         color: transparent;
         height: 0;
         width: 0;
        }
        a {
         text-decoration: none;
         color: inherit;
        }
        table,
        td {
        border-collapse: collapse;
        }
        *[class="sm-show"],
        .sm-show {
        display: none;
        visibility: hidden
        }
        @media only screen and (max-width: 795px) {
        *[class="mobile-column"],
        .mobile-column {
        display: block;
        }
        *[class="mob-column"],
        .mob-column {
        float: none !important;
        width: 100% !important;
        }
        *[class="hide"],
        .hide {
        display: none !important;
        }
        *[class="condensed"],
        .condensed {
        padding-bottom: 40px !important;
        display: block;
        }
        *[class="center"],
        .center {
        text-align: center !important;
        width: 100% !important;
        height: auto !important;
        }
        *[class="100pad"] {
        width: 100% !important;
        padding: 20px;
        }
        *[class="100padleftright"] {
        width: 100% !important;
        padding: 0 20px 0 20px;
        }
        *[class="100padtopbottom"] {
        width: 100% !important;
        padding: 20px 0 20px 0;
        }
        *[class="hr"],
        .hr {
        width: 100% !important;
        }
        *[class="p10"],
        .p10 {
        width: 10% !important;
        height: auto !important;
        }
        *[class="p20"],
        .p20 {
        width: 20% !important;
        height: auto !important;
        }
        *[class="p30"],
        .p30 {
        width: 30% !important;
        height: auto !important;
        }
        *[class="p40"],
        .p40 {
        width: 40% !important;
        height: auto !important;
        }
        *[class="p50"],
        .p50 {
        width: 50% !important;
        height: auto !important;
        }
        *[class="p60"],
        .p60 {
        width: 60% !important;
        height: auto !important;
        }
        *[class="p70"],
        .p70 {
        width: 70% !important;
        height: auto !important;
        }
        *[class="p80"],
        .p80 {
        width: 80% !important;
        height: auto !important;
        }
        *[class="p90"],
        .p90 {
        width: 90% !important;
        height: auto !important;
        }
        *[class="p100"],
        .p100 {
        width: 100% !important;
        height: auto !important;
        }
        *[class="p15"],
        .p15 {
        width: 15% !important;
        height: auto !important;
        }
        *[class="p25"],
        .p25 {
        width: 25% !important;
        height: auto !important;
        }
        *[class="p33"],
        .p33 {
        width: 33% !important;
        height: auto !important;
        }
        *[class="p35"],
        .p35 {
        width: 35% !important;
        height: auto !important;
        }
        *[class="p45"],
        .p45 {
        width: 45% !important;
        height: auto !important;
        }
        *[class="p55"],
        .p55 {
        width: 55% !important;
        height: auto !important;
        }
        *[class="p65"],
        .p65 {
        width: 65% !important;
        height: auto !important;
        }
        *[class="p75"],
        .p75 {
        width: 75% !important;
        height: auto !important;
        }
        *[class="p85"],
        .p85 {
        width: 85% !important;
        height: auto !important;
        }
        *[class="p95"],
        .p95 {
        width: 95% !important;
        height: auto !important;
        }
        *[class="alignleft"] {
        text-align: left !important;
        }
        *[class="100button"] {
        width: 100% !important;
        }
        *[class="mob-auto"],
        .mob-auto {
        width: auto !important;
        height: auto !important;
        }
        *[class="sm-show"],
        .sm-show {
        display: initial !important;
        visibility: visible !important;
        }
        *[class="sm-no-border"],
        .sm-no-border {
        border-left: 0 !important;
        border-top: 0 !important;
        border-bottom: 0 !important;
        border-right: 0 !important;
        }
        }
        @media only screen and (max-width: 450px) {
        *[class="xs-no-pad"],
        .xs-no-pad {
        padding: 0 !important;
        }
        *[class="xs-p25"],
        .xs-p25 {
        width: 25% !important;
        height: auto !important;
        }
        *[class="xs-p50"],
        .xs-p50 {
        width: 50% !important;
        height: auto !important;
        }
        *[class="xs-p75"],
        .xs-p75 {
        width: 75% !important;
        height: auto !important;
        }
        *[class="xs-p100"],
        .xs-p100 {
        width: 100% !important;
        height: auto !important;
        }
        *[class="xs-hide"],
        .xs-hide {
        display: none !important;
        }
        *[class="xs-header"],
        .xs-header {
        font-size: 45px !important
        }
        }
      </style>
    </head>
    <body
      style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; background-color: #ececec; margin: 0; padding: 0; width: 100% !important;">
        <span class="preheader"
            style="color: transparent; display: none; font-size: 1px; height: 0; opacity: 0; visibility: hidden; width: 0;">{{ config('app.name') }} Email Verification.</span>
        <table
            style="background-color: #ececec; margin: 0; mso-table-lspace: 0; mso-table-rspace: 0; padding: 0; width: 100%;"
            cellspacing="0" cellpadding="0" border="0" bgcolor="#ececec" width="100%">
            <tr>
                <td align="center" valign="top">
                <table class="p100"
                    style="background-color: #ffffff; margin: 0; mso-table-lspace: 0; mso-table-rspace: 0; padding: 0; width: 800px;"
                    width="800" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff">
                    <tr>
                        <td style="width: 30px; font-size: 1px;" width="30" valign="top" align="left">&nbsp;</td>
                        <td align="center" valign="top">
                            <table class="p100"
                            style="margin: 0; mso-table-lspace: 0; mso-table-rspace: 0; padding: 0; width: 600px;"
                            width="600" cellspacing="0" cellpadding="0" border="0" align="center">
                            <tr>
                                <td style="height: 20px; line-height: 20px; font-size: 1px; mso-line-height-rule: exactly;"
                                    valign="top" align="left">&nbsp;</td>
                            </tr>
                            <tr>
                                <td align="left" valign="top">
                                    <table class="p100"
                                        style="margin: 0; mso-table-lspace: 0; mso-table-rspace: 0; padding: 0; width: 600px;"
                                        width="600" cellspacing="0" cellpadding="0" border="0" align="left">
                                        <tr>
                                        <td align="center" valign="middle">
                                            <table class="p100"
                                                style="margin: 0; mso-table-lspace: 0; mso-table-rspace: 0; padding: 0; width: 600px;"
                                                width="600" cellspacing="0" cellpadding="0" border="0"
                                                align="center">
                                                <tr>
                                                    <td style="height: 96.6px; line-height:96.6px; mso-line-height-rule: exactly;"
                                                    valign="middle" align="left">
                                                    <a href="{{url('/')}}" target="blank"
                                                        style="border: none; color: inherit; display: block; font-family: 'Roboto', sans-serif; font-size: inherit; outline: none; text-decoration: none;">
                                                    <img src="{{ asset('assets/images/starling_logo.png') }}" alt="image"
                                                        style="-ms-interpolation-mode: bicubic; border: 0; display: block; outline: 0; text-decoration: none;  width: 140px; height: 110.6px;"
                                                        width="70" height="96.6" border="0"/>
                                                    </a>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style="height: 15px; line-height: 15px; font-size: 1px; mso-line-height-rule: exactly;"
                                    valign="top" align="left">&nbsp;</td>
                            </tr>
                            </table>
                        </td>
                        <td style="width: 30px; font-size: 1px;" width="30" valign="top" align="left">&nbsp;</td>
                    </tr>
                </table>
                </td>
            </tr>
        </table>
        <table
            style="background-color: #ececec; margin: 0; mso-table-lspace: 0; mso-table-rspace: 0; padding: 0; width: 100%;"
            cellspacing="0" cellpadding="0" border="0" bgcolor="#ececec" width="100%">
            <tr>
                <td align="center" valign="top">
                <table class="p100"
                    style="background-color: #ffffff; margin: 0; mso-table-lspace: 0; mso-table-rspace: 0; padding: 0; width: 800px;"
                    width="800" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff">
                    <tr>
                        <td style="width: 30px; font-size: 1px;" width="30" valign="top" align="left">&nbsp;</td>
                        <td align="center" valign="top">
                            <table class="p100"
                            style="margin: 0; mso-table-lspace: 0; mso-table-rspace: 0; padding: 0; width: 600px;"
                            width="600" cellspacing="0" cellpadding="0" border="0" align="center">

                            <tr>
                                <td align="left" valign="top">
                                    <table class="p100"
                                        style="margin: 0; mso-table-lspace: 0; mso-table-rspace: 0; padding: 0; width: 600px;"
                                        width="600" cellspacing="0" cellpadding="0" border="0" align="left">
                                        <tr>
                                        <td align="center" valign="top">
                                            <table class="p100"
                                                style="margin: 0; mso-table-lspace: 0; mso-table-rspace: 0; padding: 0; width: 600px;"
                                                width="600" cellspacing="0" cellpadding="0" border="0"
                                                align="center">

                                                <tr>
                                                    <td style="color: #FF4500; font-family: 'Roboto', sans-serif; font-size: 40px; font-weight: 800; line-height: 20px; mso-line-height-rule: exactly;"
                                                    valign="top" align="right">
                                                    <font face="'Roboto', sans-serif">{{ $data['status_name'] }}
                                                    </font>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td style="height: 20px; line-height: 20px; font-size: 1px; mso-line-height-rule: exactly;"
                                                    valign="top" align="left">&nbsp;</td>
                                                </tr>

                                                <tr>
                                                    <td style="color: #000000; font-family: 'Roboto', sans-serif; font-size: 14px; font-weight: 400; line-height: 23px; mso-line-height-rule: exactly;"
                                                    valign="top" align="left">
                                                    <font face="'Roboto', sans-serif">Dear {{ $data['seller_name'] }},
                                                    </font>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="height: 20px; line-height: 20px; font-size: 1px; mso-line-height-rule: exactly;"
                                                    valign="top" align="left">&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    @php
                                                        // if($data['order_type'] == '1'){
                                                        //     $order_type = 'Pick Up';
                                                        // } else {
                                                        //     $order_type = 'Delivery';
                                                        // }
                                                        $order_type = $data['order_type'];
                                                    @endphp
                                                    <td style="color: #000000; font-family: 'Roboto', sans-serif; font-size: 14px; font-weight: 400; line-height: 23px; mso-line-height-rule: exactly;"
                                                    valign="top" align="left">
                                                        <font face="'Roboto', sans-serif">
                                                            @if ($data['status_id'] == '1')
                                                                An order <bold>#{{ $data['order_id'] }}</bold> is awaiting approval from you.
                                                            @elseif ($data['status_id'] == '2')
                                                                An order <bold>#{{ $data['order_id'] }}</bold> is rejected by you.
                                                            @elseif ($data['status_id'] == '3')
                                                                An order <bold>#{{ $data['order_id'] }}</bold> is accepted by the you.
                                                            @elseif ($data['status_id'] == '5')
                                                                An order <bold>#{{ $data['order_id'] }}</bold> is ready for {{ $order_type }}.
                                                            @elseif ($data['status_id'] == '6')
                                                                An order <bold>#{{ $data['order_id'] }}</bold> has Shipped.
                                                            @else
                                                                An order <bold>#{{ $data['order_id'] }}</bold> is accepted by you.
                                                            @endif.
                                                        </font>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="height: 10px; line-height: 10px; font-size: 1px; mso-line-height-rule: exactly;"
                                                    valign="top" align="left">&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td style="height: 20px; line-height: 20px; font-size: 1px; mso-line-height-rule: exactly;"
                                                    valign="top" align="left">
                                                    <table>
                                                        <tbody>
                                                            <tr>
                                                                <td style="width: 280px;margin-right: 20px;padding: 20px;">
                                                                    <div class="o_px-xs" style="padding-left: 8px;padding-right: 8px;">
                                                                        <table width="100%" role="presentation" cellspacing="0" cellpadding="0" border="0">
                                                                        <tbody>
                                                                            <tr>
                                                                                <td class="o_bg-dark o_br o_px o_py" align="left" style="background-color: #333333;border-radius: 4px;padding-left: 16px;padding-right: 16px;padding-top: 16px;padding-bottom: 16px;">
                                                                                    <p class="o_sans o_text o_text-white" style="font-family: Helvetica, Arial, sans-serif;margin-top: 0px;margin-bottom: 0px;font-size: 16px;line-height: 24px;color: #ffffff;">Order Summary</p>
                                                                                    <table width="100%" role="presentation" cellspacing="0" cellpadding="0" border="0">
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <td width="60%" class="o_pt-xs" align="left" style="padding-top: 8px;">
                                                                                                <p class="o_sans o_text-xs o_text-dark_light" style="font-family: Helvetica, Arial, sans-serif;margin-top: 0px;margin-bottom: 0px;font-size: 14px;line-height: 21px;color: #a0a3ab;">Order Id:</p>
                                                                                            </td>
                                                                                            <td width="40%" class="o_pt-xs" align="right" style="padding-top: 8px;">
                                                                                                <p class="o_sans o_text-xs o_text-dark_light" style="font-family: Helvetica, Arial, sans-serif;margin-top: 0px;margin-bottom: 0px;font-size: 14px;line-height: 21px;color: #a0a3ab;">{{ $data['order_id'] }}</p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td width="60%" class="o_pt-xs" align="left" style="padding-top: 8px;">
                                                                                                <p class="o_sans o_text-xs o_text-dark_light" style="font-family: Helvetica, Arial, sans-serif;margin-top: 0px;margin-bottom: 0px;font-size: 14px;line-height: 21px;color: #a0a3ab;">Order Type:</p>
                                                                                            </td>
                                                                                            <td width="40%" class="o_pt-xs" align="right" style="padding-top: 8px;">
                                                                                                <p class="o_sans o_text-xs o_text-dark_light" style="font-family: Helvetica, Arial, sans-serif;margin-top: 0px;margin-bottom: 0px;font-size: 14px;line-height: 21px;color: #a0a3ab;">
                                                                                                    {{-- @if($data['order_type'] == '1')
                                                                                                    Pick Up
                                                                                                    @else
                                                                                                    Delivery
                                                                                                    @endif --}}
                                                                                                    {{ $order_type }}
                                                                                                </p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td width="60%" class="o_pt-xs" align="left" style="padding-top: 8px;">
                                                                                                <p class="o_sans o_text-xs o_text-white" style="font-family: Helvetica, Arial, sans-serif;margin-top: 0px;margin-bottom: 0px;font-size: 14px;line-height: 21px;color: #ffffff;"><strong>Amount:</strong></p>
                                                                                            </td>
                                                                                            <td width="40%" class="o_pt-xs" align="right" style="padding-top: 8px;">
                                                                                                <p class="o_sans o_text-xs o_text-success" style="font-family: Helvetica, Arial, sans-serif;margin-top: 0px;margin-bottom: 0px;font-size: 14px;line-height: 21px;color: #0ec06e;"><strong>{{ $data['grand_total'] }} AED</strong></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                    </table>
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                        </table>
                                                                    </div>
                                                                </td>
                                                                <td style="width: 280px;margin-right: 20px;padding: 20px;">
                                                                    <div class="o_px-xs o_sans o_text o_text-secondary o_left o_xs-center" style="font-family: Helvetica, Arial, sans-serif;margin-top: 0px;margin-bottom: 0px;font-size: 16px;line-height: 24px;color: #424651;text-align: left;padding-left: 8px;padding-right: 8px;">
                                                                        <p class="o_text-xxs o_caps o_text-light o_mb-xs" style="text-transform: uppercase;letter-spacing: 1px;font-size: 12px;line-height: 19px;color: #82899a;margin-top: 0px;margin-bottom: 8px;">Order Address</p>
                                                                        <p class="o_mb" style="margin-top: 0px;margin-bottom: 16px;"><strong>{{ $data['user_name'] }}</strong><br>
                                                                            {{ $data['user_email'] }}<br>

                                                                        </p>
                                                                        <p style="margin-top: 0px;margin-bottom: 0px;">
                                                                            {{ $data['address'] }}
                                                                        </p>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td align="center" valign="top">
                                                        <table align="center" style="background-color:#ef814c;border:2px solid #ef814c;border-collapse:separate!important;border-radius:30px;color:#ececec;font-family:'Roboto',sans-serif;font-size:13px;margin:13px;padding:0" cellspacing="0" cellpadding="0" border="0" bgcolor="transparent">
                                                            <tbody>
                                                                <tr>
                                                                    <td style="border-radius:30px;background-color:#ef814c;color:#ffffff;font-family:'Roboto',sans-serif;font-weight:700;line-height:100%;padding:11px 20px;vertical-align:middle" align="center" valign="top">
                                                                        <a href="{{ url('/seller/order/order-detail/'.$data['myorderid']) }}" style="border:none;color:#ffffff;display:block;font-family:'Roboto',sans-serif;font-size:inherit;font-weight:700;outline:none;text-decoration:none" target="_blank">View Order</a>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td style="height: 60px; line-height: 60px; font-size: 1px; mso-line-height-rule: exactly;"
                                                    valign="top" align="left">&nbsp;</td>
                                                </tr>

                                                <tr>
                                                    <td style="color: #000000; font-family: 'Roboto', sans-serif; font-size: 14px; font-weight: 400; line-height: 23px; mso-line-height-rule: exactly;"
                                                    valign="top" align="left">
                                                    <font face="'Roboto', sans-serif">Thank you for doing business with us.
                                                    </font>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="height: 20px; line-height: 20px; font-size: 1px; mso-line-height-rule: exactly;"
                                                    valign="top" align="left">&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td style="color: #000000; font-family: 'Roboto', sans-serif; font-size: 14px; font-weight: 400; line-height: 23px; mso-line-height-rule: exactly;"
                                                    valign="top" align="left">
                                                    <font face="'Roboto', sans-serif">Thanks!
                                                    </font>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="color: #000000; font-family: 'Roboto', sans-serif; font-size: 14px; font-weight: 400; line-height: 23px; mso-line-height-rule: exactly;"
                                                    valign="top" align="left">
                                                    <font face="'Roboto', sans-serif">{{ config('app.name') }}
                                                    </font>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        </tr>
                                        <tr>
                                        <td style="height: 60px; line-height: 60px; font-size: 1px; mso-line-height-rule: exactly;"
                                            valign="top" align="left">&nbsp;</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            </table>
                        </td>
                        <td style="width: 30px; font-size: 1px;" width="30" valign="top" align="left">&nbsp;</td>
                    </tr>
                </table>
                </td>
            </tr>
        </table>
        <table
            style="background-color: #ececec; margin: 0; mso-table-lspace: 0; mso-table-rspace: 0; padding: 0; width: 100%;"
            cellspacing="0" cellpadding="0" border="0" bgcolor="#ececec" width="100%">
            <tr>
                <td align="center" valign="top">
                <table class="p100"
                    style="background-color: #ef814c; margin: 0; mso-table-lspace: 0; mso-table-rspace: 0; padding: 0; width: 800px;"
                    width="800" cellspacing="0" cellpadding="0" border="0" bgcolor="#ef814c">
                    <tr>
                        <td align="center" valign="top">
                            <table class="p100"
                            style="margin: 0; mso-table-lspace: 0; mso-table-rspace: 0; padding: 0; width: 660px;"
                            width="660" cellspacing="0" cellpadding="0" border="0" align="center">
                            <tr>
                                <td style="width: 30px; font-size: 1px;" width="30" valign="top" align="left">&nbsp;
                                </td>
                                <td align="center" valign="top">
                                    <table class="p100"
                                        style="margin: 0; mso-table-lspace: 0; mso-table-rspace: 0; padding: 0; width: 600px;"
                                        width="600" cellspacing="0" cellpadding="0" border="0">
                                        <tr>
                                        <td class="space-top"
                                            style="height: 12px; line-height: 12px; font-size: 1px; mso-line-height-rule: exactly;"
                                            valign="top" align="left">&nbsp;</td>
                                        </tr>
                                        <tr>
                                        <td align="center" valign="middle">
                                            <table class="p100"
                                                style="margin: 0; mso-table-lspace: 0; mso-table-rspace: 0; padding: 0; width: 600px;"
                                                width="600" cellspacing="0" cellpadding="0" border="0"
                                                align="left">
                                                <tr>
                                                    <td style="color: #ffffff; font-family: 'Roboto', sans-serif; font-size: 13px; font-weight: normal; letter-spacing: 0.02em; line-height: 20px; text-align: center; mso-line-height-rule: exactly;"
                                                    valign="top" align="left">
                                                    <font face="'Roboto', sans-serif">
                                                        &copy; <script
                                                            type="text/javascript ">
                                                            document.write(new Date().getFullYear());
                                                        </script> {{ config('app.name') }}. All right reserved.
                                                    </font>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="height: 10px; line-height: 10px; font-size: 1px; mso-line-height-rule: exactly;"
                                                    valign="top" align="left">&nbsp;</td>
                                                </tr>
                                            </table>
                                        </td>
                                        </tr>
                                        <tr>
                                        <td class="space-bottom"
                                            style="height: 5px; line-height: 5px; font-size: 1px; mso-line-height-rule: exactly;"
                                            valign="top" align="left">&nbsp;</td>
                                        </tr>
                                    </table>
                                </td>
                                <td style="width: 30px; font-size: 1px;" width="30" valign="top" align="left">&nbsp;
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
