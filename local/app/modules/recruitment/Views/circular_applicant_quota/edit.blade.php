@extends('template.master')
@section('title','Edit Applicant Quota Type')
@section('breadcrumb')
    {!! Breadcrumbs::render('recruitment.applicant.list') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('ApplicantQuotaTYpe', ($scope) => {
            @if($quota->has_own_form)
                $scope.customForm = [];
            @forelse($quota->form_details as $f)
                $scope.customForm.push(
                {
                    @if($f->type=="dropdown")values: JSON.parse('{{$f->options}}'), @endif
                    title: '{{$f->title}}',
                    type: '{{$f->type}}',
                    isRequired: parseInt('{{$f->isRequired}}')?true:false,
                    name: '{{$f->name}}'
                }
            )
            @empty
                $scope.customForm = [{
                title: '',
                type: '',
                isRequired: '',
                name: ''
            }];
            @endforelse
                    @else
                $scope.customForm = [{
                title: '',
                type: '',
                isRequired: '',
                name: ''
            }];
            @endif
                $scope.addNewField = () => {
                $scope.customForm.push({
                    title: '',
                    type: '',
                    isRequired: '',
                    name: ''
                })
            }
            $scope.has_own_form = parseInt('{{$quota->has_own_form}}')?true:false
            $scope.removeField = (i) => {
                if ($scope.customForm.length > 1) $scope.customForm.splice(i, 1);
            }
        })
    </script>
    <section class="content" ng-controller="ApplicantQuotaTYpe">
        <div class="box box-solid">
            <div class="box body">
                <div class="container" style="padding-bottom: 20px">
                    <div class="row">
                        <div class="col-sm-6">
                            {!! Form::model($quota,['route'=>['recruitment.quota.update',$quota],'method'=>'patch']) !!}
                            {!! Form::hidden('type',0) !!}
                            <div class="form-group">
                                {!! Form::label('quota_name_eng','Quota Type Name Eng',['class'=>'control-lable']) !!}
                                {!! Form::text('quota_name_eng',null,['class'=>'form-control','placeholder'=>'Quota Type Name Eng']) !!}
                                @if(isset($errors)&&$errors->first('quota_name_eng'))
                                    <p class="text-danger">
                                        {{$errors->first('quota_name_eng')}}
                                    </p>
                                @endif
                            </div>
                            <div class="form-group">
                                {!! Form::label('quota_name_bng','Quota Type Name Bng',['class'=>'control-lable']) !!}
                                {!! Form::text('quota_name_bng',null,['class'=>'form-control','placeholder'=>'Quota Type Name Bng']) !!}
                                @if(isset($errors)&&$errors->first('quota_name_bng'))
                                    <p class="text-danger">
                                        {{$errors->first('quota_name_bng')}}
                                    </p>
                                @endif
                            </div>
                            <div class="form-group">
                                <label class="control-label">
                                    <div class="styled-checkbox">
                                        <input id="has_own_form" name="has_own_form" type="checkbox"
                                               ng-model="has_own_form" value="1">
                                        <label for="has_own_form"></label>
                                    </div>
                                    Has Own Form
                                </label>
                            </div>
                            <fieldset ng-if="has_own_form">
                                <legend>Custom Form<a class="btn btn-primary btn-xs" ng-click="addNewField()"><i
                                                class="fa fa-plus"></i>&nbsp;Add New Field</a></legend>
                                <div class="form-group" ng-repeat="f in customForm">
                                    <label for="title" class="control-label">Field Title</label>
                                    <input type="text" name="customForm[ [[$index]] ][title]" ng-model="f.title"
                                           class="form-control" id="title" placeholder="Enter title">
                                    <input type="hidden" name="customForm[ [[$index]] ][name]"
                                           ng-value="f.title.split(' ').join('_').toLowerCase()" class="form-control"
                                           id="title" placeholder="Enter title">
                                    <label for="type" class="control-label">Field Type</label>
                                    <select ng-model="f.type" name="customForm[ [[$index]] ][type]"
                                            class="form-control" id="type" style="margin-bottom: 5px">
                                        <option value="">--Select a value--</option>
                                        <option value="text">Text</option>
                                        <option value="dropdown">Dropdown</option>
                                        <option value="textarea">Text Area</option>
                                    </select>
                                    <div style="margin-bottom: 5px;padding-left: 10px" ng-if="f.type=='dropdown'">
                                        <h5 ng-init="f.values=[];i=$index">Value for drop down&nbsp;<a
                                                    class="btn btn-primary btn-xs" ng-click="f.values.push('');"><i
                                                        class="fa fa-plus"></i>&nbsp;Add value</a></h5>
                                        <div style="padding: 20px;border: 1px solid #eee" ng-if="f.values.length>0">
                                            <div class="input-group input-group-sm"
                                                 ng-repeat="v in f.values track by $index" style="margin-bottom: 5px">
                                                <span class="input-group-addon"
                                                      style="background: transparent !important;">
                                                    [[$index+1]]
                                                </span>
                                                <input type="text"
                                                       name="customForm[ [[i]] ][options]['[[f.values[$index].split(' ').join('_').toLowerCase()]]']"
                                                       class="form-control" ng-model="f.values[$index]">
                                                <span class="input-group-btn">
                                                <button onclick="return false" class="btn btn-danger"
                                                        ng-click="f.values.splice($index,1);">Remove</button>
                                            </span>
                                            </div>
                                        </div>
                                    </div>
                                    <label class="control-label">
                                        <div class="styled-checkbox">
                                            <input id="is_required_[[$index]]"
                                                   name="customForm[ [[$index]] ][isRequired]" type="checkbox"
                                                   ng-model="f.isRequired" value="1">
                                            <label for="is_required_[[$index]]"></label>
                                        </div>
                                        Is This Field Required?
                                    </label>
                                    <a class="btn btn-danger btn-xs pull-right" ng-if="customForm.length>1"
                                       ng-click="removeField($index)"><i class="fa fa-minus"></i>&nbsp;Remove Field</a>
                                    <hr style="border: 1px solid #cccccc"/>
                                </div>
                            </fieldset>
                            <div style="padding-top: 20px">
                                <button class="btn btn-success btn-sm" type="submit">
                                    <i class="fa fa-save"></i>&nbsp;Update Quota TYpe
                                </button>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection