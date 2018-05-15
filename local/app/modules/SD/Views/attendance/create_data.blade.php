<div class="table-responsive">
    <table class="table table-bordered table-condensed">
        <caption>
            <caption style="padding: 0 10px">
                <h4 style="    box-shadow: 1px 1px 1px #c5bfbf;padding: 10px 0;" class="text-bold text-center">
                    Attendance of "{{$data->kpi_name}}"
                    <br>{{\Carbon\Carbon::parse($date)->format("d F, Y")}}
                </h4>
            </caption>
        </caption>
        <tr>
            <th>SL. NO</th>
            <th>Ansar ID</th>
            <th>Name</th>
            <th>Is Present</th>
            <th>Is Leave</th>
        </tr>
        <?php $i=1; ?>
        @forelse($data->attendance as $attendance)
            <tr>
                <td>{{$i++}}</td>
                <td>{{$attendance->ansar_id}}</td>
                <td>{{$attendance->ansar->ansar_name_bng}}</td>
                <td>
                    <div class="styled-checkbox">
                        <input id="is_present" name="is_present" type="checkbox" value="1" >
                        <label for="is_present"></label>
                    </div>
                </td>
                <td>
                    <div class="styled-checkbox">
                        <input id="is_leave" name="is_leave" type="checkbox" value="1">
                        <label for="is_leave"></label>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="bg-warning">
                    No Data Available
                </td>
            </tr>
        @endforelse
    </table>
    <script>
        $(document).ready(function () {
            $("#is_leave").on('change',function () {
                if($(this).is(":checked")){
                    $("#is_present").prop('checked',false).prop('disabled',true);
                } else{
                    $("#is_present").prop('disabled',false);
                }
            })
            $("#is_present").on('change',function () {
                if($(this).is(":checked")){
                    $("#is_leave").prop('checked',false).prop('disabled',true);
                } else{
                    $("#is_leave").prop('disabled',false);
                }
            })
        })
    </script>
</div>