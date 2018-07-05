<?php $i = (intVal($applicants->currentPage() - 1) * $applicants->perPage()) + 1; ?>
<div>
    <div class="table-responsive">
        <table class="table table-bordered table-condensed">
            <caption style="font-size: 20px;color:#111111">All applied applicants({{$applicants->total()}})
                <button class="btn btn-primary btn-xs" ng-click="selectAllApplicant()">Select all applicant</button>
                <button class="btn btn-primary btn-xs" ng-disabled="selectedList.length<=0" ng-click="confirmSelectionOrRejection()">Confirm selection</button>
                <button class="btn btn-danger btn-xs" ng-disabled="selectedList.length<=0" ng-click="selectApplicants('rejection')">Reject selection</button>
            <div class="input-group" style="margin-top: 10px">
                <input ng-keyup="$event.keyCode==13?loadApplicant():''" class="form-control" ng-model="param.q" type="text" placeholder="Search by national id">
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
                <th>Gender</th>
                <th>Birth Date</th>
                <th>Division</th>
                <th>District</th>
                <th>Thana</th>
                <th>Height</th>
                <th>Chest</th>
                <th>Weight</th>
                <th>Action</th>
            </tr>
            @forelse($applicants as $a)
                <tr>
                    <td>{{$i++}}</td>
                    <td>{{$a->applicant_name_bng}}
                        <a href="#" ng-click="editApplicant('{{URL::route('recruitment.applicant.detail_view',['id'=>$a->applicant_id])}}')" class="">
                            <i class="fa fa-edit"></i>
                        </a>
                    </td>
                    <td>{{$a->gender}}</td>
                    <td>{{$a->date_of_birth}}</td>
                    <td>{{$a->division_name_bng}}</td>
                    <td>{{$a->unit_name_bng}}</td>
                    <td>{{$a->thana_name_bng}}</td>
                    <td>{{$a->height_feet}} feet {{$a->height_inch}} inch</td>
                    <td>{{$a->chest_normal.'-'.$a->chest_extended}} inch</td>
                    <td>{{$a->weight}} kg</td>
                    <td>
                        <button ng-if="selectedList.indexOf('{{$a->applicant_id}}')<0" class="btn btn-primary btn-xs" ng-click="addToSelection('{{$a->applicant_id}}')"><i class="fa fa-plus"></i>&nbsp; Add To Selection </button>
                        <button ng-if="selectedList.indexOf('{{$a->applicant_id}}')>=0" class="btn btn-danger btn-xs" ng-click="removeToSelection('{{$a->applicant_id}}')"><i class="fa fa-minus"></i>&nbsp; Remove Selection </button>
                        <button class="btn btn-xs btn-primary" ng-click="acceptedAsSpecial('{{$a->applicant_id}}')">Accepted as special</button>
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
                    <select class="form-control"  ng-model="param.limit" ng-change="loadApplicant()">
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