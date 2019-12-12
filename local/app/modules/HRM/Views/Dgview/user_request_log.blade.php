@extends('template.master')
@section('title','User Request Log')
@section('breadcrumb')
    {!! Breadcrumbs::render('user_request_log') !!}
@endsection
@section('content')
    <style>
        table th,
        table td {
            white-space: nowrap;
            line-break: normal;
        }

        .specifictd {
            white-space: pre-wrap;
            white-space: -moz-pre-wrap;
            white-space: -pre-wrap;
            white-space: -o-pre-wrap;
            word-wrap: break-word;
        }
    </style>
    <script>
        $(document).ready(function () {
            $('#s_date').datepicker({dateFormat: 'yy-mm-dd',});
            $('#e_date').datepicker({dateFormat: 'yy-mm-dd',});
        });
        GlobalApp.controller('UserRequestLog', function ($scope, $http, $sce) {
            $scope.loading = false;
            $scope.result = [];
            $scope.formData = {
                user: '',
                ip: '',
                url: '',
                sDate: '',
                eDate: ''
            };
            $scope.loadLog = function () {
                $scope.loading = true;
                $http({
                    url: '{{URL::route('user_request_log')}}',
                    data: angular.toJson($scope.formData),
                    method: 'post'
                }).then(function (response) {
                    $scope.result = response.data.success;
                    $scope.loading = false;
                }, function (response) {
                    console.log("%c Error", "background:red;color:white", response);
                    $scope.loading = false;
                })
            };
            $scope.getRequestObjToString = function (obj) {
                return Object.keys(obj).map(function (k) {
                    return " " + k + " : " + obj[k];
                }).join(",");
            };
            $scope.sanitizeURL = function (url) {
                return new URL(url).pathname;
            };
        })
    </script>
    <section class="content" ng-controller="UserRequestLog">
        <div class="box box-solid">
            <div class="overlay" ng-if="loading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
            </div>
            <div class="box-title">
                <h3 style="margin: 1%;">Search</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="user">User ID/Name:</label>
                            <input ng-model="formData.user" class="form-control" type="text" id="user">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="r_ip">Request IP:</label>
                            <input ng-model="formData.ip" class="form-control" type="text" id="r_ip">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="r_url">Request URL:</label>
                            <input ng-model="formData.url" class="form-control" type="text" id="r_url">
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="s_date">Start date:</label>
                            <input ng-model="formData.sDate" class="form-control" type="text" id="s_date">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="e_date">End date:</label>
                            <input ng-model="formData.eDate" class="form-control" type="text" id="e_date">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <button ng-click="loadLog()" class="btn btn-primary" style="float: right;margin-right: 2%;">search
                    </button>
                </div>
            </div>
        </div>
        <div class="box box-solid" ng-if="result.length>0">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>User</th>
                                <th>Type</th>
                                <th>IP</th>
                                <th>URL</th>
                                <th>Data</th>
                                <th>Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr ng-repeat="user in result">
                                <td>[[user.user.user_name]]([[user.user_id]])</td>
                                <td>[[user.user.type]]</td>
                                <td>[[user.request_ip]]</td>
                                <td>[[sanitizeURL(user.request_url)]]</td>
                                <td class="specifictd">[[getRequestObjToString(user.request_data)]]</td>
                                <td>[[user.created_at]]</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection