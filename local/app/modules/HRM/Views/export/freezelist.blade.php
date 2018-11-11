<table>
    <tr>
        <th class="text-center"> ক্রঃ নং</th>
        <th class="text-center">আইডি</th>
        <th class="text-center">পদবি</th>
        <th class="text-center">নাম</th>
        <th class="text-center">নিজ জেলা</th>
        <th class="text-center">অঙ্গীভূত তারিখ</th>
        <th class="text-center">ফ্রিজ করনের তারিখ</th>
        <th class="text-center">ফ্রিজকালীন ক্যাম্পের নাম</th>
        <th class="text-center">ফ্রিজকরনের কারণ</th>

    </tr>
    @php($i=1)
    @forelse($allFreezeAnsar as $freezeAnsar)
        <tr>
            <td>{{$i++}}</td>
            <td>{{$freezeAnsar->ansar_id}}</td>
            <td>{{$freezeAnsar->name_bng}}</td>
            <td>{{$freezeAnsar->ansar_name_bng}}</td>
            <td>{{$freezeAnsar->unit_name_bng}}</td>
            <td>{{\Carbon\Carbon::parse($freezeAnsar->reporting_date)->format('d-M-Y')}}</td>
            <td>{{\Carbon\Carbon::parse($freezeAnsar->freez_date)->format('d-M-Y')}}</td>
            <td>{{$freezeAnsar->kpi_name}}</td>
            <td>{{$freezeAnsar->freez_reason}}</td>
        </tr>
    @empty
        <tr>
            <td class="warning" colspan="9">No information found</td>
        </tr>
    @endforelse
</table>