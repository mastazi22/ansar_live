<html>
<head></head>
<body>
<table>
    <tr>
        <th>SL. No</th>
        <th>Ansar ID</th>
        <th>Name</th>
        <th>Rank</th>
        <th>Unit</th>
        <th>Thana</th>
        <th>Date of Birth</th>
        <th>Date of Birth</th>
        <th>Gender</th>
    </tr>
    @forelse($ansars as $ansar)
        <tr>
            <td>{{$index++}}</td>
            <td>{{$ansar->id}}</td>
            <td>{{$ansar->name}}</td>
            <td>{{$ansar->rank}}</td>
            <td>{{$ansar->unit}}</td>
            <td>{{$ansar->thana}}</td>
            <td>{{$ansar->birth_date}}</td>
            <td>{{$ansar->sex}}</td>
        </tr>
    @empty
        <tr>
            <td class="warning" colspan="8">No Ansar Found</td>
        </tr>
    @endforelse

</table>
</body>
</html>