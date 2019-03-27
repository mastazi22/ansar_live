<?php $i = (intVal($applicants->currentPage() - 1) * $applicants->perPage()) + 1; ?>
<div>
    <div class="table-responsive">
        <table class="table table-bordered table-condensed">
            <caption style="font-size: 20px;color:#111111">All selected applicants({{$applicants->total()}})
                <div class="input-group" style="margin-top: 10px">
                    <input ng-keyup="$event.keyCode==13?loadApplicant():''" class="form-control" ng-model="param.q"
                           type="text" placeholder="Search by id,mobile no or national id">
                    <span class="input-group-btn">
                        <button class="btn btn-primary" ng-click="loadApplicant()">
                            <i class="fa fa-search"></i>
                        </button>
                    </span>
                </div>
            </caption>
            <tr>
                <th>Sl. No</th>
                <th>Applicant Name</th>
                <th>Applicant ID</th>
                <th>Physical Fitness</th>
                <th>Education & Training</th>
                <th>Education & Experience</th>
                <th>Physical & Age</th>
                <th>Written</th>
                <th>Viva</th>
                @if($mark_distribution&&is_array($mark_distribution->additional_marks))
                    @foreach($mark_distribution->additional_marks as $key=>$fields)
                        <th>{{$fields['label']}}</th>
                        @endforeach
                @endif
                <th>Total</th>
                <th style="width: 150px;">Action</th>
            </tr>
            @forelse($applicants as $a)
                <tr>
                    <td>{{$i++}}</td>
                    <td>{{$a->applicant_name_bng}}{{$a->circular_applicant_quota_id?"(".$a->quotaType->quota_name_bng.")":''}}</td>
                    <td>{{$a->applicant_id}}</td>
                    @if(auth()->user()->type==11)
                        <td>{{$a->marks?($a->marks->physical?$a->marks->physical:$a->physicalPoint()):$a->physicalPoint()}}</td>
                        <td>{{$a->marks?($a->marks->edu_training?$a->marks->edu_training:$a->educationTrainingPoint()):$a->educationTrainingPoint()}}</td>
                        <td>{{$a->marks?($a->marks->edu_experience?$a->marks->edu_experience:$a->educationExperiencePoint()):$a->educationExperiencePoint()}}</td>
                        <td>{{$a->marks?($a->marks->physical_age?$a->marks->physical_age:$a->physicalAgePoint()):$a->physicalAgePoint()}}</td>

                    @else
                        <td>--</td>
                        <td>--</td>
                        <td>--</td>
                        <td>--</td>
                    @endif
                    <td style="white-space: nowrap;">{{$a->marks?($a->marks->written?round($a->marks->convertedWrittenMark(),2)."(".$a->marks->showOriginalWrittenMark().")":'--'):'--'}}</td>
                    <td>{{$a->marks?($a->marks->viva?$a->marks->viva:'--'):'--'}}</td>
                    @if($a->marks&&is_array($a->marks->additional_marks))
                        @foreach($a->marks->additional_marks as $key=>$value)
                            <td>{{array_values($value)[0]}}</td>
                        @endforeach
                    @elseif($mark_distribution&&is_array($mark_distribution->additional_marks))
                        @foreach($mark_distribution->additional_marks as $key=>$fields)
                            <td>--</td>
                        @endforeach
                    @endif
                    <td>{{$a->marks?(round($a->marks->totalMarks(),2)):'--'}}</td>

                    @if($a->marks)
                        <td>
                            <button ng-click="editMark('{{$a->applicant_id}}')" class="btn btn-primary btn-xs">
                                <i class="fa fa-edit"></i>&nbsp;Edit
                            </button>
                            {!! Form::open(['route'=>['recruitment.marks.destroy',$a->marks->id],'method'=>'delete','form-submit','loading'=>'allLoading','confirm-box'=>'1','on-reset'=>'loadApplicant()','style'=>'display:inline']) !!}
                            <button class="btn btn-danger btn-xs" type="submit">
                                <i class="fa fa-trash"></i>&nbsp;Delete mark
                            </button>
                            {!! Form::close() !!}
                        </td>
                    @else
                        <td>
                            <button ng-click="editMark('{{$a->applicant_id}}')" class="btn btn-primary btn-xs">
                                <i class="fa fa-plus"></i>&nbsp;Add mark
                            </button>
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    @if($mark_distribution&&is_array($mark_distribution->additional_marks))
                        <td class="bg-warning" colspan="{{11+count($mark_distribution->additional_marks)}}">No data available</td>
                    @else
                    <td class="bg-warning" colspan="11">No data available</td>
                    @endif
                </tr>
            @endforelse
        </table>
    </div>
    @if(count($applicants))
        <div class="row">
            <div class="col-sm-3">
                <div class="form-group">
                    <label for="" class="control-label">Load limit</label>
                    <select class="form-control" ng-model="param.limit" ng-change="loadApplicant()">
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="150">150</option>
                        <option value="200">200</option>
                        <option value="300">300</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-9">
                <div class="pull-right" paginate ref="loadApplicant(url)">
                    {{$applicants->render()}}
                </div>
            </div>
        </div>
    @endif
</div>