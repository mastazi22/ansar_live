@extends('template/master')
@section('content')
    <style>
        .content-header h1::after{
            content: '';
            display: block;
            clear: both;
        }
    </style>
    <script>

        GlobalApp.controller('UserController',function($scope,$http){
            var totalCount = 10;
            var total = '{{$total_user}}';
            $scope.totalPages = Math.ceil(parseInt(total)/totalCount);
            $scope.pages = [];
            $scope.currentPage = 0;
            $scope.showDialog = false;
            $scope.result = '';
            $scope.blockStatus = []
            $scope.confirmURL = "";
            $scope.isSearching = false;
            $scope.noFound = false;
//            alert($scope.showDialog)
            for(var i=0;i<$scope.totalPages;i++) $scope.pages[i]={pageNum:i,totalCount:totalCount}
            $scope.loadPage = function(pageNum,event){
                if(event!=null) event.preventDefault();
                $scope.currentPage = pageNum;
                $http({
                    url:'{{action('UserController@getAllUser')}}',
                    method:'get',
                    params:{limit:totalCount,offset:pageNum*totalCount}
                }).then(function (response) {
                    $scope.users = response.data;
                    $scope.blockStatus = [];
                    $scope.users.forEach(function (v) {
                        $scope.blockStatus.push(v.status)
                    })
                })
            }
            $scope.blockUser = function(id,index){
                $http({
                    method:'post',
                    url:'{{URL::to('/block_user')}}',
                    data:{user_id:id}
                }).then(function(response){
                    $scope.result = response.data.status;
                    if(response.data.status)$scope.blockStatus[index] = 0
                })
            }
            $scope.unblockUser = function(id,index){
                $http({
                    method:'post',
                    url:'{{URL::to('/unblock_user')}}',
                    data:{user_id:id}
                }).then(function(response){
                    $scope.result = response.data.status;
                    if(response.data.status)$scope.blockStatus[index] = 1
                })
            }
            $scope.searchId = function(keyEvent,username){
                $scope.noFound = false;
                if (keyEvent.which === 13)
                {
                    $scope.loading = true;
                    $scope.isSearching = true;
                    $http({
                        url : "{{URL::to('/user_search')}}",
                        method: 'get',
                        params: {user_name : username}
                    }).then(function(response){
                        $scope.blockStatus=[]
                        $scope.loading = false;
                        $scope.searchedUser = response.data;
                        $scope.searchedUser.forEach(function (v) {
                            $scope.blockStatus.push(v.status)
                        })
                       // console.log($scope.searchedUser);
                    })
                }
            }
            $scope.clearSearch = function(){
                $scope.searchedUser = "";
                $scope.userName = "";
                $scope.isSearching = false;
                $scope.loadPage(0,null);
            }
            $scope.loadPage(0,null);
        })
        GlobalApp.directive('confirmDialog', function () {
            return{
                restrict:'A',
                link: function (scope,elem,attr) {
                    var d = JSON.parse(attr.confirmDialog)
                    $(elem).confirmDialog({
                        message: 'Are you sure want to '+ d.type +' this user',
                        ok_callback: function (element) {

                            switch(d.type){
                                case 'block':
                                    scope.blockUser(d.id, d.index)
                                    break;
                                case 'unblock':
                                    scope.unblockUser(d.id, d.index)
                                    break;
                            }
                            //scope.blockUser(attr.confirmDialog);
                        },
                        cancel_callback: function (element) {
                        }
                    })
                }
            }

        })
    </script>

    <div  ng-controller="UserController">
        @if(Session::has('success_message'))
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-ok"></span> {{Session::get('success_message')}}
                </div>
            </div>
        @endif
            <section class="content-header">
                <h1>Demand Constant
                    <a href="{{action('UserController@userRegistration')}}" class="btn btn-primary btn-sm">
                        <span class="glyphicon glyphicon-user"></span> Add New User
                    </a>
                    <div class="table-search pull-right" style="right:-5%;">

                        <form method="get">
                            <label for="submitted">
                                <input style="padding: 6px 34px 5px 10px;border: 1px solid;font-size: 16px;font-weight: normal;/* border-radius: 5px; */" ng-keypress="searchId($event,userName)" ng-model="userName" type="text" class="ng-valid ng-dirty ng-valid-parse ng-touched ng-empty" name="table-search" id="entry-search" placeholder="Search by Username" value="">
                                <button class="btn btn-sm btn-info" style="right: 0px;position: absolute;top: 0px;border-radius: 0;">
                                    <i class="fa fa-search"></i>
                                </button>
                            </label>
                        </form>

                    </div>
                </h1>
            </section>
        <section class="content">
            <div class="box box-primary" style="padding: 10px 20px;overflow: hidden">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-condensed" id="user-table">

                        <tr>
                            <th>SL. No</th>
                            <th>User Name</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Activity</th>
                            <th>Action</th>
                        </tr>
                        <tr ng-show="isSearching&&searchedUser.length==0">
                            <td colspan="7">No user found</td>
                        </tr>
                        <tr ng-show="isSearching&&searchedUser.length!=0" ng-repeat="user in searchedUser">
                            <td>[[$index+1]]</td>
                            <td>[[user.user_name]]</td>
                            <td>
                                [[user.first_name+" "+user.last_name]]
                            </td>
                            <td>[[user.email]]</td>
                            <td ng-switch on="user.user_status">
                                <span ng-switch-when="0"> New. Not login yet</span>
                                <span ng-switch-when="1"> Last Login at&nbsp;[[user.last_login]]</span>
                                <span ng-switch-default>Blocked</span>
                            </td>
                            <td style="width: 100px">
                                <div class="row" style="margin-right: 0;min-width: 100px">
                                    <div class="col-xs-1">
                                        <a class="btn btn-primary btn-xs" href="{{URL::to('/edit_user')}}/[[user.id]]" title="edit"><span
                                                    class="glyphicon glyphicon-edit"></span></a>
                                    </div>

                                    <div class="col-xs-1">
                                        <a class="btn btn-danger btn-xs" ng-show="blockStatus[$index]" confirm-dialog='{"id":[[user.id]],"index":[[$index]],"type":"block"}'  class="block-user" title="block">
                                            <span  class="fa fa-ban"></span>
                                        </a>
                                        <a  ng-show="!blockStatus[$index]" class="btn btn-success btn-xs" confirm-dialog='{"id":[[user.id]],"index":[[$index]],"type":"unblock"}'  class="block-user" title="unblock">
                                            <span class="fa fa-unlock"></span>
                                        </a>
                                    </div>
                                    <div class="col-xs-1">
                                        <a class="btn btn-success btn-xs" href="{{URL::to('/edit_user_permission')}}/[[user.id]]" title="edit permission"><span
                                                    class="glyphicon glyphicon-lock"></span></a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr ng-show="!isSearching" ng-repeat="user in users">
                            <td>[[$index+1]]</td>
                            <td>[[user.user_name]]</td>
                            <td>
                                [[user.first_name+" "+user.last_name]]
                            </td>
                            <td>[[user.email]]</td>
                            <td ng-switch on="user.user_status">
                                <span ng-switch-when="0"> New. Not login yet</span>
                                <span ng-switch-when="1"> Last Login at&nbsp;[[user.last_login]]</span>
                                <span ng-switch-default>Blocked</span>
                            </td>
                            <td style="width: 100px">
                                <div class="row" style="margin-right: 0;min-width: 100px">
                                    <div class="col-xs-1">
                                        <a class="btn btn-primary btn-xs" href="{{URL::to('/edit_user')}}/[[user.id]]" title="edit"><span
                                                    class="glyphicon glyphicon-edit"></span></a>
                                    </div>

                                    <div class="col-xs-1">
                                        <a class="btn btn-danger btn-xs" ng-show="blockStatus[$index]" confirm-dialog='{"id":[[user.id]],"index":[[$index]],"type":"block"}'  class="block-user" title="block">
                                            <span  class="fa fa-ban"></span>
                                        </a>
                                        <a  ng-show="!blockStatus[$index]" class="btn btn-success btn-xs" confirm-dialog='{"id":[[user.id]],"index":[[$index]],"type":"unblock"}'  class="block-user" title="unblock">
                                            <span class="fa fa-unlock"></span>
                                        </a>
                                    </div>
                                    <div class="col-xs-1">
                                        <a class="btn btn-success btn-xs" href="{{URL::to('/edit_user_permission')}}/[[user.id]]" title="edit permission"><span
                                                    class="glyphicon glyphicon-lock"></span></a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="table_pagination" ng-show="totalPages>1&& !searchedUser">
                    <ul class="pagination">
                        <li ng-class="{disabled:currentPage==0}">
                            <span ng-show="currentPage==0">&laquo;</span>
                            <a href="#" ng-click="loadPage(currentPage-1,$event)" ng-hide="currentPage==0">&laquo;</a>
                        </li>
                        <li ng-repeat="page in pages" ng-class="{active:currentPage==page.pageNum}">
                            <span ng-show="currentPage==page.pageNum">[[page.pageNum+1]]</span>
                            <a href="#" ng-click="loadPage(page.pageNum,$event)" ng-hide="currentPage==page.pageNum">[[page.pageNum+1]]</a>
                        </li>
                        <li ng-class="{disabled:currentPage==totalPages-1}">
                            <span ng-show="currentPage==totalPages-1">&raquo;</span>
                            <a href="#" ng-click="loadPage(currentPage+1,$event)" ng-hide="currentPage==totalPages-1">&raquo;</a>
                        </li>
                    </ul>
                </div>
            </div>
        </section>
    </div>
    <script>
        $(document).ready(function () {
            $("#user-table").sortTable({
                exclude:4
            })

            $(document).on('click','.block-user', function (event) {


            })

        })
    </script>
@stop