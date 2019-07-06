<table class="table table-bordered">
    <tr>
        <th>SL. No</th>
        <th>Ansar ID</th>
        <th>Rank</th>
        <th>Name</th>
        <th>Birth Date</th>
        <th>Home District</th>
        <th>Thana</th>
        <th>Panel Date & Time</th>
        <th>Panel Id</th>
        @if(Auth::user()->type==11)
            <th>Global Position</th>
            <th>Regional Position</th>
            <th>Last Offer Region</th>
            @endif

    </tr>
    <tbody>
    <tr ng-repeat="ansar in data.ansars">
        <td>[[data.index+$index]]</td>
        <td><a href="{{URL::to('HRM/entryreport')}}/[[ansar.id]]">[[ansar.id]]</a></td>
        <td>[[ansar.rank]]</td>
        <td>[[ansar.name]]</td>
        <td>[[ansar.birth_date|dateformat:"DD-MMM-YYYY"]]</td>
        <td>[[ansar.unit]]</td>
        <td>[[ansar.thana]]</td>
        <td>[[ansar.panel_date|dateformat:"DD-MMM-YYYY"]]</td>
        <td>[[ansar.memorandum_id]]</td>
        @if(Auth::user()->type==11)
            <td>[[ansar.go_panel_position]]</td>
            <td>[[ansar.re_panel_position]]</td>
            <td>[[ansar.offer_type]]</td>
        @endif
    </tr>
    <tr ng-if="data.ansars.length<=0">
        <td class="warning" colspan="9">No Ansar Found</td>
    </tr>
    </tbody>
</table>