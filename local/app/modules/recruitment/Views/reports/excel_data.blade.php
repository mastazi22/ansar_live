<table style="width: 100%" border="1">
    <tr>
        <th style="width: 10px">SL No.</th>
        @if($ctype=='apc_training')
            <th>Ansar ID</th>
        @endif
        <th>Applicant Name</th>
        <th>Applicant ID</th>
        <th>Father Name</th>
        <th>Mother Name</th>
        <th>Birth Date</th>
        @if($ctype=='apc_training')
            <th>Age</th>
        @endif
        <th>National ID No.</th>
        @if($ctype=='apc_training')
            <th>HRM Status</th>
            <th>Job Experience</th>
        @endif
        <th>Division</th>
        <th>District</th>
        <th>Thana</th>
        <th>Village</th>
        <th>Post Office</th>
        <th>Union</th>
        <th>Height</th>
        <th>Education(Max)</th>
        <th>Training Info</th>
        <th>Weight</th>

        @if(Auth::user()->type==11)
            <th>Mobile no</th>
        @endif
        @if(isset($status)&&$status=='accepted')
            <th>Total mark</th>
        @endif
        <th>Status</th>
		<th>Photo</th>

    </tr>

    @if(count($applicants))
        @foreach($applicants as $a)
            <tr>
                <td style="width: 10px">{{($index++).''}}</td>
                @if($ctype=='apc_training')
                    <td>{{$a->ansar_id}}</td>
                @endif
                <td>{{$a->applicant_name_bng}}</td>
                <td>{{$a->applicant_id}}</td>
                <td>{{$a->father_name_bng}}</td>
                <td>{{$a->mother_name_bng}}</td>
                <td>{{$a->date_of_birth}}</td>
                @if($ctype=='apc_training')
                    <td>{{$a->ansar->calculateAge()}}</td>
                @endif
                <td>{{$a->national_id_no}}&nbsp;</td>
                @if($ctype=='apc_training')
                    <td>{{$a->ansar->status->getStatus()[0]}}</td>
                    <td>{{$a->ansar->getExperience()}}</td>
                @endif
                <td>{{$a->division->division_name_bng}}</td>
                <td>{{$a->district->unit_name_bng}}</td>
                <td>{{$a->thana->thana_name_bng}}</td>
                <td>{{$a->village_name_bng}}</td>
                <td>{{$a->post_office_name_bng}}</td>
                <td>{{$a->union_name_bng}}</td>
                <td>{{$a->height_feet}} feet {{$a->height_inch}} inch</td>
                <th>{{$a->education()->orderBy('priority','desc')->first()->education_deg_eng}}</th>
                <th>{{$a->training_info}}</th>
                <td>{{$a->weight}} kg</td>

                @if(Auth::user()->type==11)
                    <td>{{$a->mobile_no_self}}</td>
                @endif
                @if(isset($status)&&$status=='accepted')
                    <td>{{$a->marks->written+$a->marks->viva+$a->marks->physical+$a->marks->edu_training+$a->marks->physical_age}}</td>
                @endif
                <td>{{$a->status}}</td>
				<td><img src="{{$a->profile_pic}}" width="100" height="100"></td>
            </tr>
        @endforeach
    @else
        <tr>
            <td colspan="12" class="bg-warning">
                No applicants found
            </td>
        </tr>
    @endif

</table>