<!DOCTYPE html>

<html>
<head>
    <meta charset="utf-8">
    <style>
        @font-face{
            font-family: syamrupali;
            src: url('{{asset('dist/fonts/Siyamrupali.ttf')}}');
        }
        .value1{
            display: block;
            padding: 5px;
            /* background: #ababab; */
            position: absolute;
            top: -10px;
            left: 10px;
        }
        .value2{
            display: block;
            padding: 5px;
            /* background: #ababab; */
            position: absolute;
            top: -12px;
            right: 40px;
        }
        .value3{
            display: block;
            padding: 5px;
            /* background: #ababab; */
            position: absolute;
            top: -12px;
            right: 149px;
        }
        .value4{
            display: block;
            padding: 5px;
            /* background: #ababab; */
            position: absolute;
            top: 2px;
            right: 148px;
        }
        .subject{
            display: inline-block;
            vertical-align: inherit;
            padding: 0 10px;
            margin: 0 5px;
            width: 40px;
        }
        .kpi_address{
            padding: 5px 8px;
            vertical-align: inherit;
            width: 250px;
            margin-left: 21px;
            line-height: 29px;
        }
        .heading{
            border-bottom: 1px solid #000000;
            /* padding-left: 5px; */
            margin-left: 10px;
        }
        .table{
            width: 850px;
            margin: 0 auto
        }
        tr>td{
            position: relative;
            font-size: 15px;
        }
        .footer{
            border-bottom: 1px dotted #000000;
            width: 236px;
            vertical-align: text-bottom;
            display: inline-block;
            text-align: center;
        }
    </style>
</head>
<body style="font-family: syamrupali">
    <table class="table" cellpadding="0" cellspacing="0">
        <tr>
            <th colspan="8" style="text-align: center;padding: 20px 0;font-weight: normal;font-size: 18px">গণপ্রজাতন্ত্রী বাংলাদেশ সরকার<br>
                জেলা কমান্ড্যন্ট এর কার্যালয়<br>
                আনসার ও ভিডিপি, {{$unit->unit_name_bng}}
            </th>
        </tr>
        <tr style="">
            <td colspan="8">স্মারক নং-{{$mem_no}}</td>
        </tr>
        <tr  style="text-align: right;">
            <td colspan="8"><span class="value2">{{LanguageConverter::engToBng(\Carbon\Carbon::now()->format('d-m-Y'))}}</span>তারিখঃ ................................. </td>
        </tr>
        <tr >
            <td style="vertical-align: top;width: 30px">প্রতিঃ</td>
            <td colspan="7">
                <table  cellpadding="0" cellspacing="0">
                    <tr>
                        <td><span class="value1">{{$address}}</span>...................................................................................................</td>
                    </tr>
                    <tr style="height: 40px">
                        <td>....................................................................................................</td>
                    </tr>
                    <tr>
                        <td>....................................................................................................</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="8" style="padding: 15px"></td>
        </tr>
        <tr  style=""  >
            <td style="width: 30px">বিষয়ঃ </td>
            <td colspan="7">
                <table  cellpadding="0" cellspacing="0" style="width: 100%">
                    <tr style="">
                        <td style="border-bottom: 1px solid #1b1b1b;padding-left: 5px"><span class="value1">{{LanguageConverter::engToBng($total_pc)}}</span>.................</td>
                        <td style="border-bottom: 1px solid #1b1b1b;">জন পিসি,</td>
                        <td style="border-bottom: 1px solid #1b1b1b;"><span class="value1">{{LanguageConverter::engToBng($total_apc)}}</span>................</td>
                        <td style="border-bottom: 1px solid #1b1b1b;">জন এপিসি ও</td>
                        <td style="border-bottom: 1px solid #1b1b1b;"><span class="value1">{{LanguageConverter::engToBng($total_ansar)}}</span>................</td>
                        <td style="border-bottom: 1px solid #1b1b1b;">জন আনসার এর অঙ্গীভূতকালীন সময়ের ভাতাদির প্রাক্কলন।</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="8" style="padding: 14px"></td>
        </tr>
        <tr>
            <th style="width: 30px;vertical-align: top">১ ।</th>
            <td colspan="8" style="padding-bottom: 20px">
                <table width="100%"  cellpadding="0" cellspacing="0">
                    <tr>
                        <th colspan="8" style="text-align: left">
                            <span style="border-bottom: 1px solid #1b1b1b;" >
                                দৈনিক ভাতাঃ-
                            </span>
                        </th>
                    </tr>
                    <tr>
                        <td style="padding: 5px"></td>
                    </tr>
                    <tr style="">
                        <td>(ক)</td>
                        <td><span class="value1">{{LanguageConverter::engToBng($total_pc+$total_apc)}}</span>............</td>
                        <td colspan="1"> পিসি /এপিসি</td>
                        <td><span class="value1">{{LanguageConverter::engToBng($total_day)}}</span>............</td>
                        <td>দিনের  {{LanguageConverter::engToBng(DC::getValue('DPA')->cons_value)}} টাকা হিসাবে মোট টাকা</td>
                        <td style="text-align: right"><span class="value3">{{LanguageConverter::engToBng($st1)}}</span>.........................................................</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px"></td>
                    </tr>
                </table>
                <table style="width: 100%"  cellpadding="0" cellspacing="0">
                    <tr>
                    <tr style="">
                        <td>(খ)</td>
                        <td><span class="value1">{{LanguageConverter::engToBng($total_ansar)}}</span>............</td>
                        <td> আনসার</td>
                        <td><span class="value1">{{LanguageConverter::engToBng($total_day)}}</span>....................</td>
                        <td>দিনের  {{LanguageConverter::engToBng(DC::getValue('DA')->cons_value)}} টাকা হিসাবে মোট টাকা</td>
                        <td style="text-align: right"><span class="value3">{{LanguageConverter::engToBng($st2)}}</span>.........................................................</td>
                    </tr>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="3"></td>
            <td style="border-bottom: 1px solid #000000;width: 296px"></td>
        </tr>
        <tr style="">
            <td colspan="8" style="text-align: right;padding-top: 20px"><span class="value4">{{LanguageConverter::engToBng($st3)}}</span>মোট টাকা....................................................</td>
        </tr>
        <tr>
            <td colspan="8" style="padding-top: 40px;padding-bottom: 20px">
                <table style="width: 100%">
                    <tr>
                        <th style="width: 30px;">২ ।</th>
                        <th style="text-align: left;">
                            <span style="border-bottom: 1px solid #1b1b1b;" >
                                আনুষাঙ্গিকঃ -
                            </span>
                        </th>
                        <td style="padding-left: 10px">
                            মোট দৈনিক ভাতার ২০%-১৫% হারে মোট টাকা
                        </td>
                        <td style="text-align: right">
                            <span class="value3">{{LanguageConverter::engToBng($st4)}}</span> =........................................................
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <th style="width: 30px;vertical-align: top">৩ ।</th>
            <td colspan="8" style="padding-bottom: 20px">
                <table width="100%"  cellpadding="0" cellspacing="0">
                    <tr>
                        <th colspan="8" style="text-align: left">
                            <span style="border-bottom: 1px solid #1b1b1b;" >
                                রেশন ভাতাঃ-
                            </span>
                        </th>
                    </tr>
                    <tr>
                        <td style="padding: 5px"></td>
                    </tr>
                    <tr style="">
                        <td>(ক)</td>
                        <td><span class="value1">{{LanguageConverter::engToBng($total_ansar+$total_pc+$total_apc)}}</span>............</td>
                        <td colspan="1"> জন</td>
                        <td><span class="value1">{{LanguageConverter::engToBng($total_day)}}</span>..................</td>
                        <td>দিনের  {{LanguageConverter::engToBng(DC::getValue('R')->cons_value)}} টাকা হিসাবে মোট টাকা</td>
                        <td style="text-align: right"><span class="value3">{{LanguageConverter::engToBng($st5)}}</span>=.........................................................</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <th style="width: 30px;vertical-align: top">৪ ।</th>
            <td colspan="8" style="padding-bottom: 20px">
                <table width="100%"  cellpadding="0" cellspacing="0">
                    <tr>
                        <th colspan="8" style="text-align: left">
                            <span style="border-bottom: 1px solid #1b1b1b;" >
                                ধৌত ও চুলকাটা ভাতাঃ-
                            </span>
                        </th>
                    </tr>
                    <tr>
                        <td style="padding: 5px"></td>
                    </tr>
                    <tr >
                        <td>(ক)</td>
                        <td><span class="value1">{{LanguageConverter::engToBng($total_ansar+$total_pc+$total_apc)}}</span>............</td>
                        <td colspan="1"> জন</td>
                        <td><span class="value1">{{LanguageConverter::engToBng($total_day)}}</span>..................</td>
                        <td>দিনের  {{LanguageConverter::engToBng(DC::getValue('CB')->cons_value)}} টাকা হিসাবে মোট টাকা</td>
                        <td style="text-align: right"><span class="value3">{{LanguageConverter::engToBng($st6)}}</span>=.........................................................</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <th style="width: 30px;vertical-align: top">৫ ।</th>
            <td colspan="8" style="padding-bottom: 20px">
                <table width="100%"  cellpadding="0" cellspacing="0">
                    <tr>
                        <th colspan="8" style="text-align: left">
                            <span style="border-bottom: 1px solid #1b1b1b;" >
                                যাতায়াত ভাতাঃ-
                            </span>
                        </th>
                    </tr>
                    <tr>
                        <td style="padding: 5px"></td>
                    </tr>
                    <tr  style="">
                        <td>(ক)</td>
                        <td><span class="value1">{{LanguageConverter::engToBng($total_ansar+$total_pc+$total_apc)}}</span>............</td>
                        <td colspan="1"> জন</td>
                        <td><span class="value1">{{LanguageConverter::engToBng($total_day)}}</span>..................</td>
                        <td>দিনের  {{LanguageConverter::engToBng(DC::getValue('CV')->cons_value)}} টাকা হিসাবে মোট টাকা</td>
                        <td style="text-align: right"><span class="value3">{{LanguageConverter::engToBng($st7)}}</span>=.........................................................</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <th style="width: 30px;vertical-align: top">৬ ।</th>
            <td colspan="8" style="padding-bottom: 20px">
                <table width="100%"  cellpadding="0" cellspacing="0">
                    <tr>
                        <th colspan="8" style="text-align: left">
                            <span style="border-bottom: 1px solid #1b1b1b;" >
                                চিকিৎসা ভাতাঃ-
                            </span>
                        </th>
                    </tr>
                    <tr>
                        <td style="padding: 5px"></td>
                    </tr>
                    <tr >
                        <td>(ক)</td>
                        <td><span class="value1">{{LanguageConverter::engToBng($total_ansar+$total_pc+$total_apc)}}</span>............</td>
                        <td colspan="1"> জন</td>
                        <td><span class="value1">{{LanguageConverter::engToBng($total_day)}}</span>..................</td>
                        <td>দিনের  {{LanguageConverter::engToBng(DC::getValue('DV')->cons_value)}} টাকা হিসাবে মোট টাকা</td>
                        <td style="text-align: right"><span class="value3">{{LanguageConverter::engToBng($st8)}}</span>=.........................................................</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="8" style="padding-top: 10px;padding-bottom: 20px">
                <table style="width: 100%">
                    <tr>
                        <th style="width: 30px;">৭ ।</th>
                        <th style="text-align: left;">
                            <span style="border-bottom: 1px solid #1b1b1b;" >
                                মহার্ঘ ভাতা:-
                            </span>
                        </th>
                        <td style="text-align: right">
                            <span class="value3">{{LanguageConverter::engToBng($st9)}}</span>=.........................................................
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="3"></td>
            <td style="border-bottom: 1px solid #000000;width: 296px"></td>
        </tr>
        <tr>
            <td colspan="8" style="text-align: right;padding-top: 20px"><span class="value4">{{LanguageConverter::engToBng($st3+$st4+$st5+$st6+$st7+$st8+$st9)}}</span>সর্বমোট টাকা....................................................</td>
        </tr>
        <tr  style="">
            <td colspan="8" style="padding: 20px 0;line-height: normal;text-align: justify;word-wrap: break-word;word-break: break-all">



                <span class="footer">{{LanguageConverter::engToBng($form)}}</span>তারিখ হইতে <span class="footer">{{LanguageConverter::engToBng($to)}}</span> তারিখ পর্যন্ত অঙ্গিভুতকালিন পিসি/এপিসি ও আনসারদের/মহিলা আনসারদের বেতন ভাতাদি ও আনুসাঙ্গিক এর টাকা সহ মোট টাকা <span class="footer" style="width: 400px">{{LanguageConverter::engToBng($st3+$st4+$st5+$st6+$st7+$st8+$st9)}}</span> ডিডি’র/পে অর্ডার এর মাধ্যমে জেলা কমান্ড্যন্ট,আনসার ও ভিডিপি ,{{$unit->unit_name_bng}} এর বরাবরে <span class="footer">{{LanguageConverter::engToBng($p_date)}}</span>তারিখের মধ্যে জমা দেওয়ার জন্য অনুরোধ করা হ’ল।
            </td>
        </tr>
        <tr>
            <td colspan="3"></td>
            <td  style="text-align: center;line-height: 30px;width: 1px">
                <span style="font-weight: bold;">জেলা কমান্ড্যন্ট</span><br>
                আনসার ও ভিডিপি ,{{$unit->unit_name_bng}}
            </td>
        </tr>
    </table>
</body>
</html>