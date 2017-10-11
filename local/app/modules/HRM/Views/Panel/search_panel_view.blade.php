<div class="table-responsive">
    <table class="table table-bordered">
        <tr>
            <th>SL. No</th>
            <th>Id</th>
            <th>Rank</th>
            <th>Name</th>
            <th>Own District</th>
            <th>Thana</th>
            <th>Panel Date &amp; Time</th>
            <th>Panel Id</th>
        </tr>
        <?php $i = 1; ?>
        @forelse($ansarList as $ansar)
            <tr>
                <td>{{$i++}}</td>
                <td>{{$ansar->ansar_id}}</td>
                <td>{{$ansar->rank}}</td>
                <td>{{$ansar->ansar_name_bng}}</td>
                <td>{{$ansar->unit_name_bng}}</td>
                <td>{{$ansar->thana_name_bng}}</td>
                <td>{{\Carbon\Carbon::parse($ansar->created_at)->format("d-M-Y  h:i:s A")}}</td>
                <td>{{$ansar->memorandum_id}}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="bg-warning">No Ansar Found</td>
            </tr>
        @endforelse
    </table>
</div>