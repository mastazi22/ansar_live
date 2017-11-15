<?php $i = 1; ?>
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
            <th>Total Mark</th>

        </tr>

        @if(count($applicants))
            @foreach($applicants as $a)
                <tr>
                    <td>{{$i++}}</td>
                    <td>{{$a->applicant->applicant_name_bng}}</td>
                    <td>{{$a->applicant->gender}}</td>
                    <td>{{$a->applicant->date_of_birth}}</td>
                    <td>{{$a->applicant->division->division_name_bng}}</td>
                    <td>{{$a->applicant->district->unit_name_bng}}</td>
                    <td>{{$a->applicant->thana->thana_name_bng}}</td>
                    <td>{{$a->applicant->height_feet}} feet {{$a->applicant->height_inch}} inch</td>
                    <td>{{$a->applicant->chest_normal.'-'.$a->applicant->chest_extended}} inch</td>
                    <td>{{$a->applicant->weight}} kg</td>
                    <td>{{$a->total_mark}}</td>
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
    <div class="text-center" style="margin-top: 10px">
        <button class="btn btn-primary" ng-click="confirmSelectionAsAccepted()">Confirm Applicants as Accepted</button>
    </div>
@endif
