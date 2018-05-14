<div class="table-responsive">
    @if($type=="count")
        <table class="table table-condensed table-bordered">
            <tr>
                <th>Date</th>
                <th>Attendance Status</th>
                <th>Action</th>
            </tr>
            @for($i=0;$i<\Carbon\Carbon::parse($first_date)->daysInMonth;$i++)
                <?php $ad = $data->where('day',$i+1)->first(); ?>
                <tr>
                    <td>{{\Carbon\Carbon::parse($first_date)->addDays($i)->format('d-M-Y')}}</td>
                    @if($ad)
                        <td>{{$ad}}</td>
                        @else
                        <td class="bg-danger">{{"No data available"}}</td>
                    @endif
                    <td>
                        <button class="btn btn-primary btn-xs">
                            <i class="fa fa-eye"></i>&nbsp;View
                        </button>
                    </td>
                </tr>
                @endfor
        </table>
        @elseif($type=='view')
    @endif
</div>