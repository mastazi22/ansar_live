@extends('template.master')
@section('title','Applicant Quota Type')
@section('breadcrumb')
    {!! Breadcrumbs::render('recruitment.setting.applicant_quota') !!}
@endsection
@section('content')
    <section class="content">
        @if(Session::has('success_message'))
            <div class="alert alert-success">
                {{Session::get('success_message')}}
            </div>
        @elseif(Session::has('error_message'))
            <div class="alert alert-danger">
                {{Session::get('error_message')}}
            </div>
        @endif
        <div class="box box-solid">
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-6">
                        <h3>All Applicant Quota Type</h3>
                    </div>
                    <div class="col-sm-6">
                        <a href="{{URL::route('recruitment.quota.create')}}" class="btn btn-info btn-sm pull-right"
                           style="margin-top: 20px">Create New TYpe</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th>Sl. No</th>
                            <th>Quota Type Name Eng</th>
                            <th>Quota Type Name Bng</th>
                            <th>Action</th>
                        </tr>
                        <?php $i = 1; ?>
                        @forelse($quotas as $quota)
                            <tr>
                                <td>{{$i++}}
                                    @if($quota->trashed())
                                        <span class="label label-danger">deleted</span>
                                    @endif
                                </td>
                                <td>{{$quota->quota_name_eng}}</td>
                                <td>{{$quota->quota_name_bng}}</td>
                                @if($quota->trashed())
                                    <td>
                                        {!! Form::model($quota,['method'=>'delete','route'=>['recruitment.quota.destroy',$quota],'style'=>'float:left;margin-right:5px']) !!}
                                        <input type="hidden" name="type" value="1"/>
                                        <button class="btn btn-danger btn-xs">Delete Permanently</button>
                                        {!! Form::close() !!}
                                        {!! Form::model($quota,['route'=>['recruitment.quota.update',$quota],'method'=>'patch','style'=>'float:left']) !!}
                                        <input type="hidden" name="type" value="1"/>
                                        <button class="btn btn-success btn-xs">Restore</button>
                                        {!! Form::close() !!}
                                        <span style="clear: both"></span>
                                    </td>
                                @else
                                    <td>
                                        <a href="{{URL::route('recruitment.quota.edit',['id'=>$quota->id])}}"
                                           class="btn btn-info btn-xs pull-left" style="margin-right:5px;margin-top: 1.4px;">Edit Quota
                                            Type</a>
                                        {!! Form::model($quota,['method'=>'delete','route'=>['recruitment.quota.destroy',$quota],'style'=>'float:left;margin-right:5px']) !!}
                                        <input type="hidden" name="type" value="0"/>
                                        <button class="btn btn-danger btn-xs">Delete</button>
                                        {!! Form::close() !!}
                                        {!! Form::model($quota,['method'=>'delete','route'=>['recruitment.quota.destroy',$quota],'style'=>'float:left']) !!}
                                        <input type="hidden" name="type" value="1"/>
                                        <button class="btn btn-danger btn-xs">Delete Permanently</button>
                                        {!! Form::close() !!}
                                        <span style="clear: both"></span>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="bg-warning">No Quota Type Available</td>
                            </tr>
                        @endforelse
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection