@forelse($ansar_status as $rest)
    <tr>
        <td>{{ $rest->ansar_id }}</td>
        <td>{{ $rest->ansar_name_eng }}</td>
        <td>{{ $rest->name_eng }}</td>
        <td>{{ $rest->unit_name_eng }}</td>
        <td>{{ $rest->thana_name_eng }}</td>
        <td>{{\Carbon\Carbon::createFromFormat('Y-m-d',$rest->data_of_birth)->format('d-M-Y')}}</td>
        <td>{{ $rest->sex }}</td>
        {{--<td>{{ ->name_of_degree }}</td>--}}
        <td>{!! Form::text($rest->ansar_id.'ml', $value = '1', ['size' => '4x5'], $attributes = array('class' => 'form-control', 'id' => 'ansar_merit_list', 'placeholder' =>'1', 'required')) !!}
        </td>
        <td>
            <div class="styled-checkbox">
                <input type="checkbox" ng-model="formData.a_{{$rest->ansar_id}}" id="a_{{$rest->ansar_id}}" name="ch[]" class="check-panel"
                       value="{{ $rest->ansar_id }}">
                <label for="a_{{$rest->ansar_id}}"></label>
            </div>
        </td>
        {{--<td><input type="checkbox" name="ch[]" class="select-panel" value="{{ $rest->ansar_id }}"--}}
        {{--style="height: 20px; width: 30px"></td>--}}
    </tr>
    @empty
        <tr class="warning">
            <td colspan="9">No Ansar found</td>
        </tr>
@endforelse




