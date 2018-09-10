{!! Form::open(['route'=>'SD.salary_management.store','id'=>'salary-form']) !!}
{!! Form::hidden('kpi_id',$kpi_id) !!}
{!! Form::hidden('generated_for_month',$for_month) !!}
{!! Form::hidden('generated_type',$generated_type) !!}
<div class="table-responsive">
    @if($generated_type=='salary')
        <table class="table table-condensed table-bordered">
            <caption style="padding: 0 10px">
                <h4 style="    box-shadow: 1px 1px 1px #c5bfbf;padding: 10px 0;line-height: 25px;"
                    class="text-bold text-center">
                    Salary of<br>{{$kpi_name}}<br>{{\Carbon\Carbon::parse($for_month)->format("F, Y")}}
                </h4>
            </caption>
            <tr>
                <th>SL. No</th>
                <th>Ansar ID</th>
                <th>Name</th>
                <th>Rank</th>
                <th>Total Present</th>
                <th>Total Leave(paid)</th>
                <th>Total Absent</th>
                <th>Total Salary</th>
                <th>Welfare Fund</th>
                <th>Regimental Fund</th>
                <th>Revenue Stamp</th>
                <th>AVUB Share</th>
                <th>Net Amount</th>
            </tr>
            <?php $i = 0;?>
            @forelse($datas as $data)
                {!! Form::hidden("attendance_data[$i][kpi_name]",$kpi_name) !!}
                {!! Form::hidden("attendance_data[$i][ansar_id]",$data['ansar_id']) !!}
                {!! Form::hidden("attendance_data[$i][ansar_name]",$data['ansar_name']) !!}
                {!! Form::hidden("attendance_data[$i][ansar_rank]",$data['ansar_rank']) !!}
                {!! Form::hidden("attendance_data[$i][net_amount]",$data['total_amount']-($data['welfare_fee']+$data['reg_amount']+$data['revenue_stamp']+$data['share_amount'])) !!}
                {!! Form::hidden("attendance_data[$i][total_amount]",$data['total_amount']) !!}
                {!! Form::hidden("attendance_data[$i][total_present]",$data['total_present']) !!}
                {!! Form::hidden("attendance_data[$i][total_leave]",$data['total_leave']) !!}
                {!! Form::hidden("attendance_data[$i][welfare_fee]",$data['welfare_fee']) !!}
                {!! Form::hidden("attendance_data[$i][reg_amount]",$data['reg_amount']) !!}
                {!! Form::hidden("attendance_data[$i][revenue_stamp]",$data['revenue_stamp']) !!}
                {!! Form::hidden("attendance_data[$i][share_fee]",$data['share_amount']) !!}
                {!! Form::hidden("attendance_data[$i][month]",$for_month) !!}
                {!! Form::hidden("attendance_data[$i][account_no]",$data['account_no']) !!}
                {!! Form::hidden("attendance_data[$i][bank_type]",$data['bank_type']) !!}
                <tr>
                    <td>{{++$i}}</td>
                    <td>{{$data['ansar_id']}}</td>
                    <td>{{$data['ansar_name']}}</td>
                    <td>{{$data['ansar_rank']}}</td>
                    <td>{{$data['total_present']}}</td>
                    <td>{{$data['total_leave']}}</td>
                    <td>{{$data['total_absent']}}</td>
                    <td>{{$data['total_amount']}}</td>
                    <td>{{$data['welfare_fee']}}</td>
                    <td>{{$data['reg_amount']}}</td>
                    <td>{{$data['revenue_stamp']}}</td>
                    <td>{{$data['share_amount']}}</td>
                    <td>{{$data['total_amount']-($data['welfare_fee']+$data['share_amount']+$data['reg_amount']+$data['revenue_stamp'])}}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="bg-warning">No attendance data available for this month</td>
                </tr>
            @endforelse
            @if(count($datas)>0)
                <tr>
                    <th colspan="11" class="text-right">

                        {{$is_special?"$special_amount% of daily allowance":($withWeapon?"20% of daily allowance":"15% of daily allowance")}}:
                    </th>
                    <td colspan="2">
                        {{$extra}}
                    </td>
                </tr>
            @endif
        </table>
        @if(count($datas)>0)
            <h3 class="text-center">Summary</h3>
            <table class="table table-bordered table-condensed">
                <tr>
                    <th>{{$is_special?"$special_amount% of daily allowance":($withWeapon?"20% of daily allowance":"15% of daily allowance")}}</th>
                    <th>Total Welfare Fee</th>
                    <th>Total Regimental Fee</th>
                    <th>Total Revenue Stamp</th>
                    <th>Total Share Fee</th>
                    <th>Total Net Salary</th>
                    <th>Total Amount Need To Deposit</th>
                    <th>Total Min Amount Need To
                        Deposit<br>(without {{$is_special?"$special_amount% of daily allowance":($withWeapon?"20% of daily allowance":"15% of daily allowance")}})
                    </th>
                </tr>
                <tr>
                    <td>
                        {{sprintf("%.2f",$extra)}}
                        {!! Form::hidden('summery[extra]',$extra) !!}
                    </td>
                    <td>
                        {{sprintf("%.2f",collect($datas)->sum('welfare_fee'))}}
                        {!! Form::hidden('summery[welfare_fee]',collect($datas)->sum('welfare_fee')) !!}
                    </td>
                    <td>
                        {{sprintf("%.2f",collect($datas)->sum('reg_amount'))}}
                        {!! Form::hidden('summery[reg_amount]',collect($datas)->sum('reg_amount')) !!}
                    </td>
                    <td>
                        {{sprintf("%.2f",collect($datas)->sum('revenue_stamp'))}}
                        {!! Form::hidden('summery[revenue_stamp]',collect($datas)->sum('revenue_stamp')) !!}
                    </td>
                    <td>
                        {{sprintf("%.2f",collect($datas)->sum('share_amount'))}}
                        {!! Form::hidden('summery[share_amount]',collect($datas)->sum('share_amount')) !!}
                    </td>
                    <td>
                        <?php
                        $total_net_amount = collect($datas)->sum(function ($data) {
                            return $data['total_amount'] - ($data['welfare_fee'] + $data['share_amount']+ $data['revenue_stamp']+ $data['reg_amount']);
                        });?>

                        {{sprintf("%.2f",$total_net_amount)}}
                        {!! Form::hidden('summery[total_net_amount]',$total_net_amount) !!}
                    </td>
                    <td>
                        {{sprintf("%.2f",collect($datas)->sum('total_amount')+$extra)}}
                        {!! Form::hidden('summery[total_max_amount]',collect($datas)->sum('total_amount')+$extra) !!}
                    </td>
                    <td>
                        {{sprintf("%.2f",collect($datas)->sum('total_amount'))}}
                        {!! Form::hidden('summery[total_min_amount]',collect($datas)->sum('total_amount')) !!}
                    </td>
                </tr>
            </table>
        @endif
    @else
        <table class="table table-condensed table-bordered">
            <caption style="padding: 0 10px">
                <h4 style="    box-shadow: 1px 1px 1px #c5bfbf;padding: 10px 0;line-height: 25px;"
                    class="text-bold text-center">
                    Bonus of<br>{{$bonus_for=="eiduladah"?"ঈদ-উল-আযহা":"ঈদ-উল-ফিতর"}}<br>{{$kpi_name}}
                </h4>
            </caption>
            <tr>
                <th>SL. No</th>
                <th>Ansar ID</th>
                <th>Name</th>
                <th>Rank</th>
                <th>Joining Date</th>
                <th>Total Bonus</th>
                <th>Net Amount</th>
                <th>Bonus For</th>
            </tr>
            <?php $i = 0;?>
            @forelse($datas as $data)
                {!! Form::hidden("attendance_data[$i][kpi_name]",$kpi_name) !!}
                {!! Form::hidden("attendance_data[$i][ansar_id]",$data['ansar_id']) !!}
                {!! Form::hidden("attendance_data[$i][ansar_name]",$data['ansar_name']) !!}
                {!! Form::hidden("attendance_data[$i][ansar_rank]",$data['ansar_rank']) !!}
                {!! Form::hidden("attendance_data[$i][total_amount]",$data['total_amount']) !!}
                {!! Form::hidden("attendance_data[$i][bonus_for]",$data['bonus_for']=="eidulfitr"?"Eid-ul-fitr":"Eid-ul-adah") !!}
                {!! Form::hidden("attendance_data[$i][month]",$for_month) !!}
                {!! Form::hidden("attendance_data[$i][account_no]",$data['account_no']) !!}
                {!! Form::hidden("attendance_data[$i][bank_type]",$data['bank_type']) !!}
                <tr>
                    <td>{{++$i}}</td>
                    <td>{{$data['ansar_id']}}</td>
                    <td>{{$data['ansar_name']}}</td>
                    <td>{{$data['ansar_rank']}}</td>
                    <td>{{$data['joining_date']}}</td>
                    <td>{{$data['total_amount']}}</td>
                    <td ng-init="net_amount[{{($i-1)}}] = '{{$data['total_amount']}}'">
                        {!! Form::text("attendance_data[".($i-1)."][net_amount]",$data['total_amount'],['placeholder'=>"Enter net amount","ng-value"=>$data['total_amount'], 'ng-model'=>'net_amount['.($i-1).']']) !!}
                    </td>
                    <td>{{$data['bonus_for']=="eidulfitr"?"Eid-ul-fitr":"Eid-ul-adah"}}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="bg-warning">No attendance data available for this month</td>
                </tr>
            @endforelse
            @if(count($datas)>0)
                <tr>
                    <th colspan="6" style="text-align: right">Total : </th>
                    <td colspan="2">
                        [[!net_amount?0:sumArray(net_amount) ]]
                    </td>
                </tr>
                @endif
        </table>
    @endif
</div>
<button type="submit" id="generate_sheet" class="btn btn-primary pull-right">Confirm & Generate Salary Sheet</button>
<button type="submit" id="view_payroll" class="btn btn-primary pull-right" style="margin-right: 20px">View Payroll</button>
{!! Form::close() !!}
<script>
    $(document).ready(function () {
        $("#view_payroll").on('click',function (evt) {
            evt.preventDefault();
            $("#salary-form").attr('action',"{{URL::route('SD.salary_management.view_payroll')}}").submit()
        })
        $("#generate_sheet").confirmDialog({
            message: "<div style='text-align: center'>Before submit make sure all data is correct.<br>You can`t edit data once you submit it.<br>Do you want to generate salary sheet</div>",
            ok_button_text: 'Confirm',
            cancel_button_text: 'Cancel',
            event: 'click',
            ok_callback: function (element) {
                $("#salary-form").attr('action',"{{URL::route('SD.salary_management.store')}}").submit()
            },
            cancel_callback: function (element) {
            }
        })
    })
</script>