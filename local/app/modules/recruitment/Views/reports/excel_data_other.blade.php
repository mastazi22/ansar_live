<table style="width: 100%" border="1">
    <tr>
        <th style="width: 10px">SL No.</th>
        <th>Applicant ID</th>
        <th>Roll No</th>
        <th>Applicant Name</th>
        <th>Father Name</th>
        <th>Mother Name</th>
        <th>Birth Date</th>
        <th>National ID No.</th>
        <th>Gender</th>
        <th>Present Division</th>
        <th>Present District</th>
        <th>Present Thana</th>
        <th>Present Post Office</th>
        <th>Present Village</th>
        <th>Present Union</th>
        <th>Permanent Division</th>
        <th>Permanent District</th>
        <th>Permanent Thana</th>
        <th>Permanent Post Office</th>
        <th>Permanent Village</th>
        <th>Permanent Union</th>
        <th>Height</th>
        <th>Chest</th>
        <th>Mobile No</th>
        <th>Quota</th>
        <th>Education(Max)</th>
        <th>Experience</th>
        <th>Computer Knowledge</th>
		<th>Photo</th>

    </tr>

    @if(count($applicants))
        @foreach($applicants as $a)
            <?php $pic =$a->profile_pic ?>
            <tr>
                <td style="width: 10px">{{($index++).''}}</td>
                <td>{{$a->applicant_id}}</td>
                <td>{{$a->roll_no}}</td>
                <td>{{$a->applicant_name_bng}}</td>
                <td>{{$a->father_name_bng}}</td>
                <td>{{$a->mother_name_bng}}</td>
                <td>{{$a->date_of_birth}}</td>
                <td>{{$a->national_id_no}}</td>
                <td>{{$a->sex}}</td>
                <td>{{$a->present_division->division_name_bng}}</td>
                <td>{{$a->present_district->unit_name_bng}}</td>
                <td>{{$a->present_thana->thana_name_bng}}</td>
                <td>{{$a->present_village_name_bng}}</td>
                <td>{{$a->present_post_office_name_bng}}</td>
                <td>{{$a->present_union_name_bng}}</td>
                <td>{{$a->division->division_name_bng}}</td>
                <td>{{$a->district->unit_name_bng}}</td>
                <td>{{$a->thana->thana_name_bng}}</td>
                <td>{{$a->village_name_bng}}</td>
                <td>{{$a->post_office_name_bng}}</td>
                <td>{{$a->union_name_bng}}</td>
                <td>{{$a->height_feet}} feet {{$a->height_inch}} inch</td>
                <td>
                    Normal:{{$a->chest_normal}}<br>
                    Extended:{{$a->chest_extended}}
                </td>
                <td>{{$a->mobile_no_self}}</td>
                <td>
                    {{$a->circular_applicant_quota_id?$a->circularQuota->quota_name_bng:'N\A'}}
                </td>
                <th>{{$a->education()->orderBy('priority','desc')->first()->education_deg_eng}}</th>
                <th>{{$a->experience}}</th>
                <th>{{$a->computer_knowledge}}</th>
                <td>{{$a->weight}} kg</td>
				<td>@if($pic&&file_exists($pic)&&getimagesize($pic))<img src="{{$pic}}" width="100" height="100">@endif</td>
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