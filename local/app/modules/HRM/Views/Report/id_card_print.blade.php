
<h3 style="margin-top: 0;"><a href="#" id="print-report"><i class="glyphicon glyphicon-print"></i></a></h3>
<div id="ansar_id_card">

    <img src="{{$image}}">
</div>

<div class="ansar_history" style="margin-top: 20px">
    <div class="table-responsive">
        <table class="table table-bordered">
            <tr>
                <th>SL.No</th>
                <th>Print Type</th>
                <th>Issue Date</th>
                <th>Expire Date</td>
                <th>Status</th>
            </tr>
            <?php $i=1; ?>
            @forelse($history as $h)
                <tr>
                    <td>{{$i++}}</td>
                    <td>{{strcasecmp($h->type,"eng")==0?"English":"Bangla"}}</td>
                    <td>{{\Carbon\Carbon::parse($h->issue_date)->format("d-M-Y")}}</td>
                    <td>{{\Carbon\Carbon::parse($h->expire_date)->format("d-M-Y")}}</td>
                    <td>{{$h->status?"Active":"Blocked"}}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No history found</td>
                </tr>
            @endforelse
        </table>
    </div>
</div>