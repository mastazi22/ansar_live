<?php $i = (intVal($applicants->currentPage() - 1) * $applicants->perPage()) + 1; ?>
<div>
    <div class="table-responsive">
        <table class="table table-bordered table-condensed">
            <caption style="font-size: 20px;color:#111111">Total applicants({{$applicants->total()}})
                <button class="btn btn-xs btn-primary">
                    Move this page to HRM
                </button>
                <div class="input-group" style="margin-top: 10px">
                    <input ng-keyup="$event.keyCode==13?loadApplicant():''" class="form-control" ng-model="param.q"
                           type="text" placeholder="Search by national id,name,mobile_no or applicant id">
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
                <th>National ID No.</th>
                <th>Gender</th>
                <th>Birth Date</th>
                <th>Division</th>
                <th>District</th>
                <th>Thana</th>
                <th>Height</th>
                <th>Action</th>
            </tr>
            @forelse($applicants as $a)
                <tr>
                    <td>{{$i++}}</td>
                    <td>{{$a->ansar_name_bng}}</td>
                    <td>{{$a->national_id_no}}</td>
                    <td>{{$a->sex}}</td>
                    <td>{{$a->data_of_birth}}</td>
                    <td>{{$a->division->division_name_bng}}</td>
                    <td>{{$a->district->unit_name_bng}}</td>
                    <td>{{$a->thana->thana_name_bng}}</td>
                    <td>{{$a->hight_feet}} feet {{$a->hight_inch}} inch</td>
                    <td>
                        <a target="_blank" title="download"
                           href="{{URL::route('recruitment.hrm.view_download',['type'=>'download','circular_id'=>$a->job_circular_id,'id'=>$a->id])}}"
                           class="btn btn-primary btn-xs"><i class="fa fa-download"></i></a>
                        <a target="_blank" title="view"
                           href="{{URL::route('recruitment.hrm.view_download',['type'=>'view','circular_id'=>$a->job_circular_id,'id'=>$a->id])}}"
                           class="btn btn-primary btn-xs"><i class="fa fa-eye"></i></a>
                        <button ng-click="moveToHRM('{{URL::route('recruitment.hrm.move',['id'=>$a->id])}}')" class="btn btn-primary btn-xs"><i class="fa fa-circle"></i>&nbsp;Move to HRM</button>
                    </td>

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
                    <select class="form-control" ng-model="limitList">
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