<table class="table table-bordered">
    <tr>
        <th>Ansar ID</th>
        <th>Rank</th>
        <th>Name</th>
        <th>Birth Date</th>
        <th>Home District</th>
        <th>Thana</th>
        <th>Kpi Name</th>
        <th>Embodiment Date</th>
        <th>Embodiment Id</th>
    </tr>
    <tbody>
    <tr ng-repeat="ansar in data.ansars">
        <td><a href="{{URL::to('HRM/entryreport')}}/[[ansar.id]]">[[ansar.id]]</a></td>
        <td>[[ansar.rank]]</td>
        <td>[[ansar.name]]</td>
        <td>[[ansar.birth_date|dateformat:"DD-MMM-YYYY"]]</td>
        <td>[[ansar.unit]]</td>
        <td>[[ansar.thana]]</td>
        <td>[[ansar.kpi_name]]</td>
        <td>[[ansar.joining_date|dateformat:"DD-MMM-YYYY"]]</td>
        <td>[[ansar.memorandum_id]]</td>
    </tr>
    <tr ng-if="data.ansars.length<=0">
        <td class="warning" colspan="10">No Ansar Found</td>
    </tr>
    </tbody>
</table>