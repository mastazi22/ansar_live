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
                <th>Physical Fitness</th>
                <th>Education & Training</th>
                <th>Written</th>

                <th>Viva</th>
                <th>Total</th>
                <th>Action</th>
            </tr>
            @forelse($applicants as $a)
                <tr>
                    <td>{{$i++}}</td>
                    <td>{{$a->applicant_name_bng}}</td>
                    @if(auth()->user()->type==11)
                        <td>{{$a->marks?($a->marks->physical?$a->marks->physical:$a->physicalPoint()):$a->physicalPoint()}}</td>
                        <td>{{$a->marks?($a->marks->edu_training?$a->marks->edu_training:$a->educationTrainingPoint()):$a->educationTrainingPoint()}}</td>
                    @else
                        <td>--</td>
                        <td>--</td>
                    @endif

                    <td>{{$a->marks?($a->marks->written?round($a->marks->convertedWrittenMark(),2):'--'):'--'}}</td>


                    <td>{{$a->marks?($a->marks->viva?$a->marks->viva:'--'):'--'}}</td>
                    <td>{{$a->marks?($a->marks->total?round($a->marks->total,2):'--'):'--'}}</td>
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
                    <td class="bg-warning" colspan="11">No data available</td>
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