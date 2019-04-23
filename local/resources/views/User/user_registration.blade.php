@extends('template/master')
@section('title','User Registration')
@section('breadcrumb')
    {!! Breadcrumbs::render('user_registration') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('userController', function ($scope, $http,httpService) {
            $scope.showDistrict = false;
            $scope.userType = []
            $scope.selectedUserType = ""
            $scope.allDistrict = [];
            $scope.allDivision = [];
            $scope.allCategory = [];
            $scope.district = ''
            $scope.division = ''
            $scope.divisions = []
            $scope.districts = []
            @foreach($types as $type)
             $scope.userType.push({code: '{{$type->type_code}}', name: '{{$type->type_name}}'})
            @endforeach
            $scope.selectedUserType = '{{Request::old('user_type')}}'
            var div = $http({
                url: '{{URL::to('HRM/DivisionName')}}',
                type: 'get'
            });
            var dis = $http({
                url: '{{URL::to('HRM/DistrictName')}}',
                type: 'get'
            });
            var user = $http({
                url: '{{URL::to('load_user')}}',
                type: 'get'
            })
            var catagory = httpService.category({status:'active'})
            Promise.all([
                div,dis,user,catagory
            ]).then(function (response) {
                console.log(response)
                $scope.allDivision = response[0].data;
                $scope.allDistrict = response[1].data;
                $scope.allUsers = response[2].data;
                $scope.allCategory = response[3].data;
            })
            $scope.onUserTypeChange = function () {
                if ($scope.selectedUserType == 22) {
                    $scope.showDistrict = true;
                    $scope.showDivision = false;
                    $scope.showUsers = false;
                    $scope.showDD = false;
                    $scope.district = '{{Request::old('district_name')}}'
                }
                else if ($scope.selectedUserType == 66) {
                    $scope.showDivision = true
                    $scope.showDistrict = false;
                    $scope.showUsers = false;
                    $scope.showDD = false;
                    $scope.division = '{{Request::old('division_name')}}'
                }
                else if ($scope.selectedUserType == 88 ||$scope.selectedUserType == 99) {
                    $scope.showDivision = false
                    $scope.showDistrict = false;
                    $scope.showDD = false;
                    $scope.showUsers = true;
                    $scope.user_parent = '{{Request::old('user_parent')}}'
                }
                else if ($scope.selectedUserType == 111) {
                    $scope.showDD = true
                    $scope.showDivision = false
                    $scope.showDistrict = false;
                    $scope.showUsers = false;

                }
                else {
                    $scope.showDistrict = false;
                    $scope.showDivision = false;
                    $scope.showUsers = false;
                    $scope.showDD = false;
                }
            }
            $scope.onUserTypeChange();
            $scope.isExists=(id)=>{
                var r = $scope.divisions.find(dv=>dv==id);
                return r!=undefined;
            }
        })
    </script>
    <div ng-controller="userController">
        <section class="content">
            <div class="register-page" style="background-color: transparent;background: none;">
                <div class="register-box" style="margin: 4% auto 5% auto;width:50%;">

                    <div class="register-box-body box-info" style="position: relative">

                        <form action="{{action('UserController@handleRegister')}}" method="post"
                              class="form-horizontal">
                            {{csrf_field()}}
                            <div class="form-group has-feedback">
                                <label for="user_name" class="col-sm-3 control-label"
                                       style="text-align: left;padding-top:0">User Name</label>

                                <div class="col-sm-9 @if($errors->has('user_name')) has-error @endif">
                                    <input type="text" name="user_name" value="{{Request::old('user_name')}}"
                                           class="form-control" placeholder="Enter User Name"/>
                                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                                    @if($errors->has('user_name'))
                                        <p class="text-danger">{{$errors->first('user_name')}}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group has-feedback">
                                <label for="password" class="col-sm-3 control-label"
                                       style="text-align: left;padding-top:0">Password</label>

                                <div class="col-sm-9 @if($errors->has('password')) has-error @endif">
                                    <input type="password" name="password" class="form-control" placeholder="Enter Password"/>
                                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                                    @if($errors->has('password'))
                                        <p class="text-danger">{{$errors->first('password')}}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group has-feedback">
                                <label for="r_password" class="col-sm-3 control-label"
                                       style="padding-top:0;margin-top: -2px;text-align: left ">Retype Password</label>

                                <div class="col-sm-9 @if($errors->has('r_password')) has-error @endif">
                                    <input type="password" name="r_password" class="form-control"
                                           placeholder="Retype password"/>
                                    <span class="glyphicon glyphicon-log-in form-control-feedback"></span>
                                    @if($errors->has('r_password'))
                                        <p class="text-danger">{{$errors->first('r_password')}}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group has-feedback">
                                <label for="user_type" class="col-sm-3 control-label"
                                       style="text-align: left;padding-top:0  ">User Type</label>

                                <div class="col-sm-9">
                                    <select name="user_type" class="form-control" ng-model="selectedUserType"
                                            ng-change="onUserTypeChange()">
                                        <option value="">--Select a User Type--</option>
                                        <option ng-repeat="u in userType" value="[[u.code]]">[[u.name]]</option>

                                    </select>
                                    @if($errors->has('user_type'))
                                        <p class="text-danger">{{$errors->first('user_type')}}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group has-feedback" ng-show="showDistrict">
                                <label for="district_name" class="col-sm-3 control-label"
                                       style="text-align: left;padding-top:0  ">User District</label>

                                <div class="col-sm-9">
                                    <select name="district_name" class="form-control" ng-model="district">
                                        <option value="">--Select a District--</option>
                                        <option ng-repeat="district in allDistrict" value="[[district.id]]">
                                            [[district.unit_name_eng]]
                                        </option>
                                    </select>
                                    @if($errors->has('district_name'))
                                        <p class="text-danger">{{$errors->first('district_name')}}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group has-feedback" ng-show="showDivision">
                                <label for="district_name" class="col-sm-3 control-label"
                                       style="text-align: left;padding-top:0  ">User Division</label>

                                <div class="col-sm-9">
                                    <select name="division_name" class="form-control" ng-model="division">
                                        <option value="">--Select a Division--</option>
                                        <option ng-repeat="division in allDivision" value="[[division.id]]">
                                            [[division.division_name_eng]]
                                        </option>
                                    </select>
                                    @if($errors->has('division_name'))
                                        <p class="text-danger">{{$errors->first('division_name')}}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group has-feedback" ng-show="showUsers">
                                <label for="district_name" class="col-sm-3 control-label"
                                       style="text-align: left;padding-top:0  ">User Parent</label>

                                <div class="col-sm-9">
                                    <select name="user_parent" class="form-control" ng-model="user_parent">
                                        <option value="">--Select a User--</option>
                                        <option ng-repeat="user in allUsers" value="[[user.id]]">
                                            [[user.user_name]]
                                        </option>
                                    </select>
                                    @if($errors->has('user_parent'))
                                        <p class="text-danger">{{$errors->first('user_parent')}}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group has-feedback" ng-show="showDD">
                                <label for="district_name" class="col-sm-3 control-label"
                                       style="text-align: left;padding-top:0  ">Select Divisons</label>

                                <div class="col-sm-9">
                                    <div style="max-height: 150px;overflow-y: auto">
                                        <div ng-repeat="d in allDivision">
                                            <label  class="control-label">
                                                <input type="checkbox" name="divisions[]" value="[[d.id]]" ng-true-value="[[d.id]]" ng-false-value="" ng-model="divisions[$index]">&nbsp;[[d.division_name_bng]]
                                            </label>
                                        </div>
                                    </div>
                                    @if($errors->has('divisions'))
                                        <p class="text-danger">{{$errors->first('divisions')}}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group has-feedback" ng-show="showDD">
                                <label for="district_name" class="col-sm-3 control-label"
                                       style="text-align: left;padding-top:0  ">Select Units</label>

                                <div class="col-sm-9">
                                    <div style="max-height: 150px;overflow-y: auto">
                                       <div ng-repeat="d in allDistrict" ng-if="isExists(d.division_id)">
                                           <label class="control-label">
                                               <input type="checkbox" name="districts[]" value="[[d.id]]" ng-true-value="[[d.id]]" ng-false-value="" ng-checked="isExists(d.division_id)" ng-model="districts[$index]">&nbsp;[[d.unit_name_bng]]
                                           </label>
                                       </div>
                                    </div>
                                    @if($errors->has('divisions'))
                                        <p class="text-danger">{{$errors->first('divisions')}}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group has-feedback" ng-show="showDD">
                                <label for="district_name" class="col-sm-3 control-label"
                                       style="text-align: left;padding-top:0  ">Select Circular Category</label>

                                <div class="col-sm-9">
                                    <div style="max-height: 150px;overflow-y: auto">
                                       <div ng-repeat="d in allCategory">
                                           <label class="control-label" style="text-align: left !important;">
                                               <input type="checkbox" name="categories[]" value="[[d.id]]" >&nbsp;[[d.category_name_bng]]
                                           </label>
                                       </div>
                                    </div>
                                    @if($errors->has('categories'))
                                        <p class="text-danger">{{$errors->first('divisions')}}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4 col-sm-offset-8">
                                    <button type="submit" class="btn btn-primary btn-block btn-flat">Register</button>
                                </div>
                                <!-- /.col -->
                            </div>
                        </form>
                    </div>
                    <!-- /.form-box -->
                </div>
                <!-- /.register-box -->
                <!-- iCheck -->
                <script src="../../plugins/iCheck/icheck.min.js" type="text/javascript"></script>
                <script>
                    $(function () {
                        $('Request').iCheck({
                            checkboxClass: 'icheckbox_square-blue',
                            radioClass: 'iradio_square-blue',
                            increaseArea: '20%' // optional
                        });
                    });
                </script>
            </div>
        </section>
    </div>
@endsection