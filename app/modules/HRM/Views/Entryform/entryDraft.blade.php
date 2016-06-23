@extends('template/master')

@section('content')
    <script>
        GlobalApp.controller('draftController', function ($scope, getDraftService) {
            getDraftService.getAllDraftValues().then(function (response) {

                $scope.draft = response.data;

//            alert(JSON.stringify($scope.draft));
                console.log($scope.draft);
            });

        });
        GlobalApp.factory('getDraftService', function ($http) {
            return {
                getAllDraftValues: function () {
                    return $http.get("{{URL::to('HRM/getdraftlist')}}");
                }
            }
        })
    </script>
    <div ng-controller="draftController">
        {{--<div class="breadcrumbplace">--}}
            {{--{!! Breadcrumbs::render('draft_entry') !!}--}}
        {{--</div>--}}
        @if (Session::has('success'))
            <div style="width:87%;margin:0 auto;">
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-ok"></span>Ansar with ID: {{Session::get('success')}} Added Successfully
                </div>
            </div>
        @endif
        @if(Session::has('add_success'))
            <div style="width:90%;margin: 0 auto">
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-ok"></span>{{Session::get('add_success')}}
                </div>
            </div>
        @endif
        <section class="content" style="width: 90%;">

            <div class="box table-list">

                <div class="table-list-title">Draft entry list</div>

                <div class="box-body" id="change-body">
                    <div class="loading-data"><i class="fa fa-4x fa-refresh fa-spin loading-icon"></i>
                    </div>

                    <table class="table table-bordered table-striped" id="ansar-table">

                        <tr>

                            <th>Name</th>
                            <th>Father name</th>
                            <th>Sex</th>
                            <th>Mobile</th>
                            <th style="width:140px">Action</th>
                        </tr>
                        <tr ng-show="!draft">
                            <td colspan="5">No draft data</td>
                        </tr>
                        <tr ng-repeat="drafts in draft">
                            <td>[[ drafts.ansar_name_eng ]]</td>
                            <td>[[ drafts.father_name_eng ]]</td>
                            <td>[[ drafts.sex]]</td>
                            <td>[[ drafts.mobile_no_self ]]</td>
                            <td>
                                <a class="btn btn-success btn-xs "
                                   href="{{URL::to('HRM/singledraftedit')}}/[[ drafts.filename ]]" title="Edit"><span
                                            class="glyphicon glyphicon-edit"></span></a>
                                <a class="btn btn-danger btn-xs " title="Delete"
                                   href="{{URL::to('HRM/draftdelete')}}/[[ drafts.filename ]]"><i
                                            class="glyphicon glyphicon-trash"></i></a></td>
                        </tr>
                    </table>
                </div>
            </div>
        </section>

    </div>
@stop