@extends('template.master')
@section('content')
    <script>
        $(document).ready(function (e) {

            $('body').on('click','.toggle-view', function (e) {
                e.preventDefault();
                $(this).children('img').toggleClass('rotate-img-up rotate-img-down')
                console.log($(this).siblings('.p_continer').html())
                $($(this).parents('div')[0]).siblings('.p_continer').slideToggle(300)
            })
        })
        GlobalApp.controller('UserPermission', function ($scope) {
            var type = parseInt('{{\App\models\User::find($id)->userPermission->permission_type}}');
            $scope.routes = JSON.parse('{{$routes}}'.replace(/&quot;/g, '"'));
            $scope.access = JSON.parse('{{$access}}'.replace(/&quot;/g, '"'));
            $scope.grantAll = ($scope.access != 'null' && $scope.access == 'all')||type==1;
            //alert($scope.grantAll)
            $scope.permissionList = []
            $scope.count = $scope.routes.length;
            $scope.permitAll = function () {
                for(var i=0;i<$scope.permissionList.length;i++) {
                    $scope.permissionList[i] = Array.apply(null, new Array($scope.permissionList[i].length)).map(Boolean.prototype.valueOf, $scope.grantAll);
                }
            }
            $scope.$watch('permissionList', function (n,o) {
                var t= 0,length=0;
                //alert(n.length)
                n.forEach(function (e,i,a) {
                    length += e.length;
                    e.forEach(function (f,j,p) {
                        if(n[i][j]) t++;
                    })
                })
                if(t == length&&t>0) $scope.grantAll = true;
                else if(n.length>0) $scope.grantAll = false;
            },true)
        })

        GlobalApp.directive('permissionCheck',function(){
            return {
                restrict:'A',
                link:function(scope,element,attr){
                    if (!Array.isArray(scope.permissionList[scope.$parent.$index])) scope.permissionList[scope.$parent.$index] = [];
                    if(scope.access) {
                        scope.permissionList[scope.$parent.$index][scope.$index] = scope.access.indexOf(attr.value) > -1 || scope.grantAll;
                    }
                    else scope.permissionList[scope.$parent.$index][scope.$index] = scope.grantAll;
                }
            }
        })
    </script>
    <div  ng-controller="UserPermission">
        <form action="{{action('UserController@updatePermission',['id'=>$id])}}" method="post">
            {{csrf_field()}}
            <section class="content-header">
                <div class="box" style="margin-left: 40px;width: 96%;">
                    <div class="box-body">
                        <p>Edit permission of : <strong>{{\App\models\User::find($id)->user_name}}</strong></p>
                        <label class="control-label">
                            Grant All Permission &nbsp;
                            <div class="styled-checkbox">
                                <input type="checkbox" id="all" ng-change="permitAll()" ng-model="grantAll" name="permit_all" value="permit_all">
                                <label for="all"></label>
                            </div>

                        </label>
                        <button type="submit" class="btn btn-primary pull-right">
                            <i class="fa fa-save"></i> Save Permission
                        </button>
                    </div>
                </div>
            </section>
            <section class="content">

                <div class="row" style="margin-left: 25px">
                    <div class=" col-lg-4" >
                        <div style="margin-top: 5px" ng-repeat="route in routes" ng-if="$index%3==0">
                            <div class="legend">
                                [[route.controller.name]]
                                <button class="btn btn-default btn-xs pull-right toggle-view">
                                    <img src="{{asset('dist/img/down_icon.png')}}" class="rotate-img-up" style="width: 18px;height: 20px;">
                                </button>
                            </div>
                            <div class="box-body p_continer" style="background-color: #FFFFFF;">
                                <ul class="permission-list">
                                    <li ng-repeat="p in route.routes">
                                        <label class="control-label">
                                            <div class="styled-checkbox">
                                                <input permission-check type="checkbox" id="p_[[$parent.$index]]_[[$index]]" ng-model="permissionList[$parent.$index][$index]" ng-change=""  name="permission[]" value="[[p.value]]">
                                                <label for="p_[[$parent.$index]]_[[$index]]"></label>
                                            </div>
                                             [[p.name]]
                                        </label>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class=" col-lg-4" >
                        <div style="margin-top: 5px" ng-repeat="route in routes" ng-if="$index%3==2">
                            <div class="legend">
                                [[route.controller.name]]
                                <button class="btn btn-default btn-xs pull-right toggle-view">
                                    <img src="{{asset('dist/img/down_icon.png')}}" class="rotate-img-up" style="width: 18px;height: 20px;">
                                </button>
                            </div>
                            <div class="box-body p_continer" style="background-color: #ffffff">
                                <ul class="permission-list">
                                    <li ng-repeat="p in route.routes">
                                        <label class="control-label">
                                            <div class="styled-checkbox">
                                                <input permission-check type="checkbox" id="[[p.value]]" ng-model="permissionList[$parent.$index][$index]" name="permission[]" value="[[p.value]]">
                                                <label for="[[p.value]]"></label>
                                            </div>
                                            [[p.name]]
                                        </label>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class=" col-lg-4" >
                        <div style="margin-top: 5px" ng-repeat="route in routes" ng-if="$index%3==1">
                            <div class="legend">
                                [[route.controller.name]]
                                <button class="btn btn-default btn-xs pull-right toggle-view">
                                    <img src="{{asset('dist/img/down_icon.png')}}" class="rotate-img-up" style="width: 18px;height: 20px;">
                                </button>
                            </div>
                            <div class="box-body p_continer" style="background-color: #FFFFFF;">
                                <ul class="permission-list">
                                    <li ng-repeat="p in route.routes">
                                        <label class="control-label">
                                            <div class="styled-checkbox">
                                                <input permission-check type="checkbox" id="[[p.value]]" ng-model="permissionList[$parent.$index][$index]" name="permission[]" value="[[p.value]]">
                                                <label for="[[p.value]]"></label>
                                            </div>
                                            [[p.name]]
                                        </label>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </form>
    </div>
@stop