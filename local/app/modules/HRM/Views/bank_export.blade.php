<table>
    <tr>
        <td>Shareholder Name</td>
        <td>Father Name</td>
        <td>Mother name</td>
        <td>Present vill</td>
        <td>Present post</td>
        <td>Present upzilla</td>
        <td>Present zilla</td>
        <td>Member Type</td>
        <td>Gender</td>
        <td>Date of Birth</td>
        <td>Occupation</td>
        <td>Designation</td>
        <td>Mobile</td>
        <td>Email</td>
        <td>National Id</td>
        <td>Employee_number</td>
        <td>Track_number</td>
        <td>Bank AC Number</td>
        <td>AC Name</td>
        <td>Branch Name</td>
        <td>Ac Type</td>
        <td>MFS Number</td>
        <td>permanent vill</td>
        <td>permanent post</td>
        <td>permanent upzilla</td>
        <td>permanent zilla</td>
        <td>work at</td>
    </tr>
    @foreach($ansars as $ansar)
        <tr>
            <td>{{$ansar->ansar_name_eng}}</td>
            <td>{{$ansar->father_name_eng}}</td>
            <td>{{$ansar->mother_name_eng}}</td>
            <td>{{$ansar->village_name}}</td>
            <td>{{$ansar->post_office_name}}</td>
            <td>{{$ansar->thana->thana_name_eng}}</td>
            <td>{{$ansar->district->unit_name_eng}}</td>
            <td>{{'A'}}</td>
            <td>{{strtoupper(substr($ansar->sex,0,1))}}</td>
            <td>{{\Carbon\Carbon::parse($ansar->data_of_birth)->format('d/m/Y')}}</td>
            <td></td>
            <td>{{$ansar->designation->name_eng}}</td>
            <td>{{'88'.$ansar->mobile_no_self}}</td>
            <td>{{$ansar->email_self}}</td>
            <td>{{$ansar->national_id_no}}</td>
            <td>{{$ansar->ansar_id}}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>

            <td>{{$ansar->village_name}}</td>
            <td>{{$ansar->post_office_name}}</td>
            <td>{{$ansar->thana->thana_name_eng}}</td>
            <td>{{$ansar->district->unit_name_eng}}</td>
            <td></td>
        </tr>
        @endforeach
</table>