<?php $i = (intVal($applicants->currentPage() - 1) * $applicants->perPage()) + 1; ?>
<div class="table-responsive">
    <table class="table table-bordered">
        <tr>
            <th>#</th>
            <th>Applicant Name</th>
            <th>Gender</th>
            <th>Birth Date</th>
            <th>Division</th>
            <th>District</th>
            <th>Thana</th>
            <th>Height</th>
            <th>Chest</th>
            <th>Weight</th>
            <th>Mobile no</th>
            <th>Status</th>

        </tr>

        @if(count($applicants))
            @foreach($applicants as $a)
                <tr>
                    <td>{{$i++}}</td>
                    <td>{{$a->applicant_name_bng}}</td>
                    <td>{{$a->gender}}</td>
                    <td>{{$a->date_of_birth}}</td>
                    <td>{{$a->division->division_name_bng}}</td>
                    <td>{{$a->district->unit_name_bng}}</td>
                    <td>{{$a->thana->thana_name_bng}}</td>
                    <td>{{$a->height_feet}} feet {{$a->height_inch}} inch</td>
                    <td>{{$a->chest_normal.'-'.$a->chest_extended}} inch</td>
                    <td>{{$a->weight}} kg</td>
                    <td>{{$a->mobile_no_self}}</td>
                    <td>{{$a->status}}</td>
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
</div>
@if(count($applicants))
    <div class="pull-right" paginate ref="loadPage(url)">
        {{$applicants->render()}}
    </div>
@endif
