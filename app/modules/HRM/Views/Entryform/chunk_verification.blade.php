@extends('template.master')
@section('content')
    <script>
        GlobalApp.controller('ChunkVerificationController', function ($scope, $http, $interval) {
            $scope.showAnsar = '10';
            $scope.ansars = []
            $scope.selectAll = false
            $scope.selected = [];
            $scope.loadAnsar = function () {
                $scope.loading = true;
                $http({
                    method: 'get',
                    url: '{{action('FormSubmitHandler@getNotVerifiedAnsar')}}',
                    params: {chunk: 'chunk', limit: $scope.showAnsar, offset: 0}
                }).then(function (response) {
                    $scope.loading = false;
                    $scope.ansars = response.data
                    $scope.selected = Array.apply(null, new Array($scope.ansars.length)).map(Boolean.prototype.valueOf, false)
                    var d = response.data;
                    var c = Math.ceil(d.length/100);
                    var i=0;
                    $interval(function () {
                        console.log(d.slice(i,i+100))
                        Array.prototype.push.apply($scope.ansars,d.slice(i,i+100))
                        i = i+100;
                    },100,c)
                    $scope.selected = Array.apply(null, new Array(d.length)).map(Boolean.prototype.valueOf, false)
                    $scope.selectAll = false
                }, function (response) {

                })
            }
            $scope.$watch('selected', function (n, o) {
                if (n.length == 0) return;
                var t = 0, f = 0;
                $scope.selectAll = n.every(function (value,index) {
                    return value;
                })
            }, true)
            $scope.changeSelectedAll = function () {
                $scope.selected = Array.apply(null, new Array($scope.ansars.length)).map(Boolean.prototype.valueOf, $scope.selectAll)
            }
        })
        GlobalApp.directive('formSubmit', function () {
            return{
                restrict:'AC',
                link: function (scope,elem,attr) {

                    $(elem).on('click', function (e) {
                        e.preventDefault();
                        scope.loading = true;
                        $("#not-verified-form").ajaxSubmit({
                            success: function (response) {
                                console.log(response)
                                if(response.status){
                                    scope.loadAnsar();
                                    $('body').notifyDialog({type: 'success', message: 'Verify successfully'}).showDialog()
                                }
                                else{
                                    $('body').notifyDialog({type: 'error', message: response.message}).showDialog()
                                }
                            },
                            error:function(response){

                            }
                        })
                    })
                }
            }
        })
        $(document).ready(function (e) {
            $("#button-top").on('click', function (e) {
                $('html,body').animate({scrollTop: 0}, 'slow')
            })
            var t = $('#ppp').offset().top
            var l = $('#ppp').offset().left
            console.log({top: l})
            $(document).scroll(function (e) {
                if (t - $(document).scrollTop() <= 0) {
                    $("#button-top").css('display', 'block')
                }
                else {
                    $("#button-top").css('display', 'none')
                }
            })
        })
    </script>
    <div class="content-wrapper" ng-controller="ChunkVerificationController">
        <div class="breadcrumbplace">
            {!! Breadcrumbs::render('chunk_verification') !!}
        </div>
        <button id="button-top" class="btn btn-primary"
                style="position: fixed;bottom: 10px;right: 20px;z-index: 1000000000000000;display: none">
            <i class="fa fa-arrow-up fa-2x"></i>
        </button>
        <div class="loading-report animated" ng-show="loading">
            <img src="{{asset('dist/img/ring-alt.gif')}}" class="center-block">
            <h4>Loading...</h4>
        </div>
        <section class="content" ng-init="loadAnsar()">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a>Ansar Verification</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active">
                        <div class="box box-solid">
                            <div id="ppp" style="margin-right: 0" class="row margin-bottom">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Show Ansar :</label>

                                        <div class="col-sm-9">
                                            <select class="form-control" ng-model="showAnsar" ng-change="loadAnsar()">
                                                <option value="10">10</option>
                                                <option value="20">20</option>
                                                <option value="30">30</option>
                                                <option value="40">40</option>
                                                <option value="50">50</option>
                                                <option value="60">60</option>
                                                <option value="70">70</option>
                                                <option value="80">90</option>
                                                <option value="90">90</option>
                                                <option value="100">100</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <button class="btn btn-primary pull-right" id="verify-ansar" form-submit>
                                    <i class="fa fa-check"></i>&nbsp;Verify Ansar
                                </button>
                            </div>
                            <div class="table-responsive">
                                <form id="not-verified-form" method="post" action="{{action('EntryFormController@entryVerify')}}">
                                    <input type="hidden" name="chunk_verification" value="chunk_verification">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>SL. No</th>
                                            <th>Ansar Id</th>
                                            <th>Ansar Name</th>
                                            <th>Ansar District</th>
                                            <th>Ansar Thana</th>
                                            <th>Rank</th>
                                            <th>Sex</th>
                                            <th><input type="checkbox" ng-model="selectAll"
                                                       ng-change="changeSelectedAll()" value="all" name="select_all">
                                            </th>
                                        </tr>
                                        <tr ng-if="ansars.length==0">
                                            <td class="warning" colspan="8">No Not Verified Ansar Found</td>
                                        </tr>
                                        <tr ng-repeat="a in ansars" ng-if="ansars.length>0">
                                            <td>[[$index+1]]</td>
                                            <td>[[a.ansar_id]]</td>
                                            <td>[[a.ansar_name_bng]]</td>
                                            <td>[[a.unit_name_bng]]</td>
                                            <td>[[a.thana_name_bng]]</td>
                                            <td>[[a.name_bng]]</td>
                                            <td>[[a.sex]]</td>
                                            <td><input type="checkbox" ng-model="selected[$index]"
                                                       value="[[a.ansar_id]]" name="not_verified[]"></td>
                                        </tr>
                                    </table>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </div>
@stop