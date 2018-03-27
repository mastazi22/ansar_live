<html>
<head></head>
<body>
<table>
    <tr>
        <th>SL. no</th>
        <th>Ansar ID</th>
        <th>Rank</th>
        <th>Name</th>
        <th>Division</th>
        <th>District</th>
        <th>Thana</th>
        <th>Mobile No</th>
        <th>Birth Date</th>
        <th>Age</th>
    </tr>
    @forelse($ansars as $ansar)
        <tr>
            <td>{{$index++}}</td>
            <td>{{$ansar->id}}</td>
            <td>{{$ansar->name}}</td>
            <td>{{$ansar->rank}}</td>
            <td>{{$ansar->division}}</td>
            <td>{{$ansar->unit}}</td>
            <td>{{$ansar->thana}}</td>
            <td>{{$ansar->mobile_no_self}}</td>
            <td>{{$ansar->birth_date}}</td>
            <td>{{$ansar->age}}</td>
        </tr>
    @empty
        <tr>
            <td class="warning" colspan="8">No Ansar Found</td>
        </tr>
    @endforelse

</table>
</body>
</html>