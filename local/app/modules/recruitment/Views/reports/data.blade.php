<?php $i = (intVal($applicants->currentPage() - 1) * $applicants->perPage()) + 1; ?>
<div class="table-responsive">

    <table class="table table-bordered" style="overflow: hidden">
        <caption><span style="font-size: 20px;">Total Applicant({{$applicants->total()}})</span>
            <form action="{{URL::route('report.applicants.status_export')}}" method="post" target="_blank"
                  style="display: inline">
                {!! csrf_field() !!}
                <input type="hidden" ng-repeat="(k,v) in param" name="[[k]]" value="[[v]]">
                <button class="btn btn-primary btn-xs">
                    <i class="fa fa-file-excel-o"></i>&nbsp; Export page
                </button>
            </form>
            <form action="{{URL::route('report.applicants.status_export')}}" method="post" export-all-form
                  style="display: inline;margin-left:10px">
                {!! csrf_field() !!}
                <input type="hidden" ng-repeat="(k,v) in param" ng-if="k!='page'" name="[[k]]" value="[[v]]">
                <button class="btn btn-primary btn-xs">
                    <i class="fa fa-file-excel-o"></i>&nbsp; Export all
                </button>
            </form>
            <form action="{{URL::route('report.applicants.status_export')}}" method="post" export-all-form
                  style="display: inline;margin-left:10px">
                {!! csrf_field() !!}
                <input type="hidden" name="export_template" value="2">
                <input type="hidden" ng-repeat="(k,v) in param" ng-if="k!='page'" name="[[k]]" value="[[v]]">
                <button class="btn btn-primary btn-xs">
                    <i class="fa fa-file-excel-o"></i>&nbsp; Export all(Template 2)
                </button>
            </form>
            <form action="{{URL::route('report.applicants.status_export_pdf')}}" method="post" export-all-form-pdf
                  style="display: inline;margin-left:10px">
                {!! csrf_field() !!}
                <input type="hidden" ng-repeat="(k,v) in param" ng-if="k!='page'" name="[[k]]" value="[[v]]">
                <button class="btn btn-primary btn-xs">
                    <i class="fa fa-file-pdf-o"></i>&nbsp; Export all (Pdf)
                </button>
            </form>
            @if(count($applicants))
                <div class="pull-right" style="margin: -20px 0" paginate ref="loadPage(url)">
                    {{$applicants->render()}}
                </div>
            @endif
        </caption>

        <tr>
            <th>#</th>
            <th>Applicant Name</th>
            <th>Applicant ID</th>
            <th>Father Name</th>
            <th>Birth Date</th>
            <th>National ID No.</th>
            <th>Division</th>
            <th>District</th>
            <th>Thana</th>
            <th>Height</th>
            <th>Weight</th>
            @if(Auth::user()->type==11)
                <th>Mobile no</th>
            @endif
            @if(isset($status)&&$status=='accepted')
                <th>Total mark</th>
            @endif
            <th>Status</th>

        </tr>

        @if(count($applicants))
            @foreach($applicants as $a)
                <tr>
                    <td>{{$i++}}</td>
                    <td>{{$a->applicant_name_bng}}</td>
                    <td>{{$a->applicant_id}}</td>
                    <td>{{$a->father_name_bng}}</td>
                    <td>{{$a->date_of_birth}}</td>
                    <td>{{$a->national_id_no}}</td>
                    <td>{{$a->division->division_name_bng}}</td>
                    <td>{{$a->district->unit_name_bng}}</td>
                    <td>{{$a->thana->thana_name_bng}}</td>
                    <td>{{$a->height_feet}} feet {{$a->height_inch}} inch</td>
                    <td>{{$a->weight}} kg</td>
                    @if(Auth::user()->type==11)
                        <td>{{$a->mobile_no_self}}</td>
                    @endif
                    @if(isset($status)&&$status=='accepted')
                        @if($a->marks->is_bn_candidate)
                            <td>
                                <strong>Bn Candidate</strong>
                            </td>
                        @elseif($a->marks->specialized)
                            <td><strong>Special Candidate</strong></td>
                        @else
                            <td>{{$a->marks->written+$a->marks->viva+$a->marks->physical+$a->marks->edu_training}}</td>
                        @endif
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
    <form action="{{URL::route('report.applicants.status_export')}}" method="post" target="_blank"
          style="display: inline">
        {!! csrf_field() !!}
        <input type="hidden" ng-repeat="(k,v) in param" name="[[k]]" value="[[v]]">
        <button class="btn btn-primary btn-xs">
            <i class="fa fa-file-excel-o"></i>&nbsp; Export page
        </button>
    </form>
    <div class="pull-right" style="margin: -20px 0" paginate ref="loadPage(url)">
        {{$applicants->render()}}
    </div>
@endif
