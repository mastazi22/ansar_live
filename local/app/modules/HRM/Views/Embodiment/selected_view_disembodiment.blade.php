@foreach($ansar_infos as $ansar_info)
    <tr>
        <td>{{$ansar_info->ansar_id}}</td>
        <td>{{$ansar_info->ansar_name_bng}}</td>
        <td>{{$ansar_info->unit_name_bng}}</td>
        <td>{{$ansar_info->thana_name_bng}}</td>
        <td>{{$ansar_info->name_bng}}</td>
        <td>{{$ansar_info->kpi_name}}</td>

        <td>
            <select name="dis-reason" class="form-control dis-reason">
                <option value="">--Select Reason--</option>
                @foreach($reasons as $reason)
                    <option value="{{$reason->id}}">{{$reason->reason_in_bng}}</option>
                @endforeach
            </select>
        </td>
        {{--<td><input type="checkbox" name="ch[]" class="ansar-check" value="{{ $ansar_info->ansar_id }}"--}}
                   {{--style="height: 20px; width: 30px"></td>--}}
        <td><div class="styled-checkbox">
                <input  type="checkbox"  id="a_{{$ansar_info->ansar_id}}" name="ch[]" class="ansar-check" value="{{ $ansar_info->ansar_id }}">
                <label for="a_{{$ansar_info->ansar_id}}"></label>
            </div>
        </td>
    </tr>
@endforeach
<script>
    $('select[name="dis-reason"]').tooltip({
        title:"Select a valid reason",
        trigger:'manual'
    })
    $('select[name="dis-reason"]').on('change', function (e) {
        if(!this.value){
            $(this).parents('tr').find('.ansar-check').prop('checked',false);
            $(this).parents('tr').find('.ansar-check').trigger('change')
        }
    })
    $(".ansar-check").on('click', function (e) {
        var v = $(this).parents('tr').find('select');
        if(!v.val()){
            $(this).prop('checked',false);
            v.tooltip('show');
            setTimeout(function () {
                v.tooltip('hide');
            },1000)
        }

    })
</script>
