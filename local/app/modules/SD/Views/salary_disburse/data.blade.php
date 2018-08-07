<style>
    .bg-danger{
        background: red !important;
        color:white !important;
    }
</style>
{!! Form::open(['route'=>'SD.salary_disburse.store','id'=>'disburse-form','form-submit-local']) !!}
{!! Form::hidden('salary_sheet_id',\Illuminate\Support\Facades\Crypt::encrypt($sheet->id)) !!}
<div class="table-responsive">
    <table class="table table-condensed table-bordered">
        <caption style="padding: 0 10px">
            <h4 style="    box-shadow: 1px 1px 1px #c5bfbf;padding: 10px 0;line-height: 25px;"
                class="text-bold text-center">
                Salary Disbursement of<br>{{$sheet->kpi->kpi_name}}<br>{{\Carbon\Carbon::parse($sheet->generated_for_month)->format("F, Y")}}
            </h4>
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

        @if(count($salary_histories)>0)
            <tr>
                <th colspan="3" class="text-right">
                    Total available amount :
                </th>
                <td colspan="1">
                    {{$sheet->deposit?$sheet->deposit->paid_amount:'n\a'}}
                </td>
                <th colspan="3" class="text-right">
                    Total amount need to disburse :
                </th>
                <td colspan="1">
                    @if($sheet->deposit&&$sheet->deposit->paid_amount<$salary_histories->sum('amount'))
                        <span style="color: red;font-weight: bold">{{$salary_histories->sum('amount')}}</span>
                    @elseif($sheet->deposit&&$sheet->deposit->paid_amount>=$salary_histories->sum('amount'))
                        <span style="color: green;font-weight: bold">{{$salary_histories->sum('amount')}}</span>
                    @else
                        <span style="color: red;font-weight: bold">{{$salary_histories->sum('amount')}}</span>
                    @endif

                </td>
            </tr>
        @endif
    </table>

</div>
<button type="submit" id="disburse_salary" @if(($sheet->deposit&&$sheet->deposit->paid_amount<$salary_histories->sum('amount'))||!$sheet->deposit) disabled="disabled" @endif  class="btn btn-primary pull-right">Confirm & Disburse Salary</button>
{{--<button type="submit" id="cancel_disburse_salary"  class="btn btn-primary pull-right" style="margin-right: 10px">Cancel Disbursement</button>
</button>--}}
{!! Form::close() !!}
<script>
    $(document).ready(function () {
        /*$("#disburse_salary").confirmDialog({
            message: "<div style='text-align: center'>Before submit make sure all data is correct.<br>Once you submit it, those data will be sent to respective bank for disburse.<br>Do you want to disburse salary</div>",
            ok_button_text: 'Confirm',
            cancel_button_text: 'Cancel',
            event: 'click',
            ok_callback: function (element) {
                $("#salary-form").attr('action', "{{URL::route('SD.salary_management.store')}}").submit()
            },
            cancel_callback: function (element) {
            }
        })*/
    })
</script>