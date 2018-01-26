<?php $i = (intVal($applicants->currentPage() - 1) * $applicants->perPage()) + 1; ?>
<div>
    <div class="table-responsive">
        <table class="table table-bordered table-condensed">
            <caption style="font-size: 20px;color:#111111">Total applicants({{$applicants->total()}})
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
                <th>Ansar ID</th>
                <th>Ansar Name</th>
                <th>Gender</th>
                <th>Rank</th>
                <th>Blood Group</th>
                <th>Division</th>
                <th>District</th>
                <th>Issue Date</th>
                <th>Expire Date</th>
                <th>Action</th>
            </tr>
            @forelse($applicants as $a)
                <tr>
                    <td>{{$i++}}</td>
                    <td>{{$a->ansar_id}}</td>
                    <td>{{$a->ansar_name_bng}}</td>
                    <td>{{$a->sex}}</td>
                    <td>{{$a->designation->name_bng}}</td>
                    <td>{{$a->bloodGroup->blood_group_name_bng}}</td>
                    <td>{{$a->division->division_name_bng}}</td>
                    <td>{{$a->district->unit_name_bng}}</td>
                    <td>{{\Carbon\Carbon::now()->format('d-M-Y')}}</td>
                    <td>{{\Carbon\Carbon::now()->addYears(10)->subDay()->format('d-M-Y')}}</td>
                    <td>
                        {!! Form::open(['route'=>'print_card_id','style'=>'display:inline-block','target'=>'_blank']) !!}
                        {!! Form::hidden('ansar_id',intval($a->ansar_id)) !!}
                        {!! Form::hidden('type','bng') !!}
                        {!! Form::hidden('issue_date',\Carbon\Carbon::now()->format('d-M-Y')) !!}
                        {!! Form::hidden('expire_date',\Carbon\Carbon::now()->addYears(10)->subDay()->format('d-M-Y')) !!}
                        <button type="submit" class="btn btn-primary btn-xs">
                            <i class="fa fa-print"></i>&nbsp;Print Card(Bng)
                        </button>
                        {!! Form::close() !!}
                        {!! Form::open(['route'=>'print_card_id','style'=>'display:inline-block','target'=>'_blank']) !!}
                        {!! Form::hidden('ansar_id',intval($a->ansar_id)) !!}
                        {!! Form::hidden('type','bng') !!}
                        {!! Form::hidden('issue_date',\Carbon\Carbon::now()->format('d-M-Y')) !!}
                        {!! Form::hidden('expire_date',\Carbon\Carbon::now()->addYears(10)->subDay()->format('d-M-Y')) !!}
                        <button type="submit" class="btn btn-primary btn-xs">
                            <i class="fa fa-print"></i>&nbsp;Print Card(Eng)
                        </button>
                        {!! Form::close() !!}
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