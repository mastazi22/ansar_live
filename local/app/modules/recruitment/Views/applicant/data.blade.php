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
            @if(Auth::user()->type==11)
                <th>Mobile no</th>
            @endif
            <th>Status</th>

        </tr>

        @if(count($applicants))
            @foreach($applicants as $a)
                <tr>
                    <td>{{$i++}}</td>
                    <td>{{$a->applicant_name_bng or 'n\a'}}</td>
                    <td>{{$a->gender or 'n\a'}}</td>
                    <td>{{$a->date_of_birth or 'n\a'}}</td>
                    <td>{{$a->division?$a->division->division_name_bng:'n\a'}}</td>
                    <td>{{$a->district?$a->district->unit_name_bng:'n\a'}}</td>
                    <td>{{$a->thana?$a->thana->thana_name_bng:'n\a'}}</td>
                    <td>{{$a->height_feet or 'n\a'}} feet {{$a->height_inch or 'n\a'}} inch</td>
                    <td>{{$a->chest_normal or 'n\a'}}-{{$a->chest_extended or 'n\a'}} inch</td>
                    <td>{{$a->weight or 'n\a'}} kg</td>
                    @if(Auth::user()->type==11)
                        <td>{{$a->mobile_no_self}}</td>
                    @endif
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
