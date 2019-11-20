<table class="table table-bordered">
    <tr>
        <th>Ansar ID</th>
        <th>Name</th>
        <th>Rank</th>
        <th>Unit</th>
        <th>Thana</th>
        <th>Date of Birth</th>
        <th>Gender</th>
    </tr>
    <tr ng-repeat="ansar in data.ansars">
        <td>[[ansar.id]]</td>
        <td>[[ansar.name]]</td>
        <td>[[ansar.rank]]</td>
        <td>[[ansar.unit]]</td>
        <td>[[ansar.thana]]</td>
        <td>[[ansar.birth_date]]</td>
        <td>[[ansar.sex]]</td>
    </tr>
    <tr ng-if="data.ansars==undefined||ansars.length<=0">
        <td class="warning" colspan="8">No Ansar Found</td>
    </tr>
    </tbody>
</table>