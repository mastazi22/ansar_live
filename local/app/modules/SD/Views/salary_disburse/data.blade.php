<style>
    .bg-danger{
        background: red !important;
        color:white !important;
    }
    .append_extra *{
        text-decoration: line-through !important;
        color: #cccccc !important;
    }
</style>
{!! Form::open(['route'=>'SD.salary_disburse.store','id'=>'disburse-form',]) !!}
{!! Form::hidden('salary_sheet_id',\Illuminate\Support\Facades\Crypt::encrypt($sheet->id)) !!}
<div class="table-responsive">
    {{--{{dump($sheet->summery)}}--}}
    <h4 style="    box-shadow: 1px 1px 1px #c5bfbf;padding: 10px 0;line-height: 25px;"
        class="text-bold text-center">
        Salary Disbursement of<br>{{$sheet->kpi->kpi_name}}<br>{{\Carbon\Carbon::parse($sheet->generated_for_month)->format("F, Y")}}
    </h4>
    <table class="table table-condensed table-bordered">
        <caption style="text-align: center;
    font-size: 18px;
    font-weight: bold;">
            Ansar Net Salary
        </caption>
        <tr>
            <th>SL. No</th>
            <th>Ansar ID</th>
            <th>Name</th>
            <th>Rank</th>
            <th>KPI Name</th>
            <th>Account No</th>
            <th>Bank name/Mobile bank type</th>
            <th>Amount to disburse</th>
        </tr>
        <?php $i = 0;?>
        @forelse($salary_histories as $history)
            <tr>
                <td>{{++$i}}</td>
                <td>{{$history->ansar->ansar_id}}</td>
                <td>{{$history->ansar->ansar_name_eng}}</td>
                <td>{{$history->ansar->designation->name_eng}}</td>
                <td>{{$history->kpi->kpi_name}}</td>
                <td @if(!$history->ansar->account) class="bg-danger" @endif>{{$history->ansar->account?$history->ansar->account->getAccountNo():'n\a'}}</td>
                <td @if(!$history->ansar->account) class="bg-danger" @endif>{{$history->ansar->account?$history->ansar->account->getBankName():'n\a'}}</td>
                <td>{{$history->amount}}</td>
            </tr>
        @empty
            <tr>
                <td colspan="10" class="bg-warning">No attendance data available for this month</td>
            </tr>
        @endforelse

    </table>
    <table class="table table-condensed table-bordered">
        <caption style="text-align: center;
    font-size: 18px;
    font-weight: bold;">
            Disbursement Of Welfare, Regimental & Share
        </caption>
        <tr>
            <th>Title</th>
            <td>Welfare</td>
            <td>Regimental</td>
            <td>Share</td>
        </tr>
        <tr>
            <th>Account no</th>
            <td>{{\App\modules\SD\Models\BankAccountList::getAccount("WELFARE")}}</td>
            <td>{{\App\modules\SD\Models\BankAccountList::getAccount("REGIMENTAL")}}</td>
            <td>{{\App\modules\SD\Models\BankAccountList::getAccount("SHARE")}}</td>
        </tr>
        <tr>
            <th>Amount</th>
            <td>{{$sheet->summery["welfare_fee"]}}</td>
            <td>{{$sheet->summery["reg_amount"]}}</td>
            <td>{{$sheet->summery["share_amount"]}}</td>
        </tr>
    </table>
    <table class="table table-condensed table-bordered">
        <caption style="text-align: center;
    font-size: 18px;
    font-weight: bold;">
            <input type="checkbox" value="1" name="append_extra" checked>&nbsp;Disbursement Of {{$sheet->kpi->details->with_weapon?"20%":"15%"}} Of Daily Salary
        </caption>
        <tr>
            <th>Title</th>
            <td>DG`s Account({{DC::getValue('DGEP')->cons_value}}%)</td>
            <td>RC`s Account({{DC::getValue('RCEP')->cons_value}}%)</td>
            <td>DC`s Account({{DC::getValue('DCEP')->cons_value}}%)</td>
        </tr>
        <tr>
            <th>Bank Name</th>
            <td>DBBL</td>
            <td>{{$sheet->kpi->division->rc->userProfile->bank_name}}</td>
            <td>{{$sheet->kpi->unit->dc->userProfile->bank_name}}</td>
        </tr>
        <tr>
            <th>Account no</th>
            <td>{{\App\modules\SD\Models\BankAccountList::getAccount("DG")}}</td>
            <td>{{$sheet->kpi->division->rc->userProfile->account_no}}</td>
            <td>{{$sheet->kpi->unit->dc->userProfile->account_no}}</td>
        </tr>
        <tr>
            <th>Amount</th>
            <td>{{sprintf("%.2f",(($sheet->summery["extra"]*DC::getValue('DGEP')->cons_value)/100))}}</td>
            <td>{{sprintf("%.2f",(($sheet->summery["extra"]*DC::getValue('RCEP')->cons_value)/100))}}</td>
            <td>{{sprintf("%.2f",(($sheet->summery["extra"]*DC::getValue('DCEP')->cons_value)/100)+$sheet->summery["revenue_stamp"])}}</td>
        </tr>
    </table>
    <table class="table table-bordered table-condensed">
        @php($total_deposit = $sheet->deposit?$sheet->deposit->paid_amount:0)
        <tr>
            <th>Total Available Amount : </th>
            <td>{{$total_deposit?$total_deposit:'n\a'}}</td>
            <th>Total Amount Need To Pay : </th>
            <td>
                @php($t_min_amount = collect($salary_histories)->sum('amount')+$sheet->summery["welfare_fee"]+$sheet->summery["reg_amount"]+$sheet->summery["share_amount"]+$sheet->summery["revenue_stamp"])
                @php($t_max_amount = collect($salary_histories)->sum('amount')+$sheet->summery["welfare_fee"]+$sheet->summery["reg_amount"]+$sheet->summery["share_amount"]+$sheet->summery["revenue_stamp"]+$sheet->summery["extra"])
                <span id="t_min_amount" class="hidden @if($total_deposit>=$t_min_amount) text-success @else text-danger @endif">{{$t_min_amount}}</span>
                <span id="t_max_amount" class="@if($total_deposit>=$t_max_amount) text-success @else text-danger @endif">{{$t_max_amount}}</span>
            </td>
        </tr>
    </table>

</div>
<button type="submit" id="disburse_salary" @if(($sheet->deposit&&$sheet->deposit->paid_amount<$salary_histories->sum('amount'))||!$sheet->deposit) disabled="disabled" @endif  class="btn btn-primary pull-right">Confirm & Disburse Salary</button>
{{--<button type="submit" id="cancel_disburse_salary"  class="btn btn-primary pull-right" style="margin-right: 10px">Cancel Disbursement</button>
</button>--}}
{!! Form::close() !!}
<script>
    $(document).ready(function () {
        $("#disburse_salary").confirmDialog({
            message: "<div style='text-align: center'>Before submit make sure all data is correct.<br>Once you submit it, those data will be sent to respective bank for disburse.<br>Do you want to disburse salary</div>",
            ok_button_text: 'Confirm',
            cancel_button_text: 'Cancel',
            event: 'click',
            ok_callback: function (element) {
                $(element).prop('disabled',true);
                $("#disburse-form").submit()
            },
            cancel_callback: function (element) {
            }
        })
        $("input[name='append_extra']").on('change',function () {
            if($(this).prop("checked")){
                $(this).parents('table').removeClass('append_extra')
                $("#t_min_amount").addClass('hidden')
                $("#t_max_amount").removeClass('hidden')
            } else{
                $(this).parents('table').addClass('append_extra')
                $("#t_max_amount").addClass('hidden')
                $("#t_min_amount").removeClass('hidden')
            }
        })
    })
</script>