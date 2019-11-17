<table class="table table-bordered">
    <tr>
        <th>Ansar ID</th>
        <th>Name</th>
        <th>Rank</th>
        <th>Current KPI Name</th>
        <th>KPI Unit</th>
        <th>KPI Thana</th>
        <th>Embodiment Date</th>
        <th>Service Ended Date</th>
    </tr>
    <tbody>
    <tr ng-repeat="ansar in data.ansars">
        <td><a href="{{URL::to('HRM/entryreport')}}/[[ansar.id]]">[[ansar.id]]</a></td>
        <td>[[ansar.name]]</td>
        <td>[[ansar.rank]]</td>
        <td>[[ansar.kpi]]</td>
        <td>[[ansar.unit]]</td>
        <td>[[ansar.thana]]</td>
        <td>[[ansar.j_date|dateformat:"DD-MMM-YYYY"]]</td>
        <td>[[ansar.se_date|dateformat:"DD-MMM-YYYY"]]</td>
    </tr>
    <tr ng-if="data.ansars.length<=0">
        <td class="warning" colspan="9">No Ansar Found</td>
    </tr>
    </tbody>
</table>