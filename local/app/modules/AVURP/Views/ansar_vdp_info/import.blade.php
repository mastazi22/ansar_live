@extends('template.master')
@section('title','Import Data')
@section('breadcrumb')
    {!! Breadcrumbs::render('entry.list') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('VDPController', function ($scope, $http, $sce) {
            $scope.param = {};
            $scope.allLoading = false;
            $scope.hide = true;
            $scope.errorLink = false;
            $scope.entryUnits = {
                1:"উপজেলা পুরুষ আনসার কোম্পানি",
                2:"উপজেলা মহিলা আনসার প্লাটুন",
                3:"ইউনিয়ন আনসার প্লাটুন(পুরুষ)",
                4:"ইউনিয়ন ভিডিপি প্লাটুন",
                5:"ওয়ার্ড ভিডিপি প্লাটুন"
            }
        })
        GlobalApp.directive('compileHtml', function ($compile) {
            return {
                restrict: 'A',
                link: function (scope, elem, attr) {
                    var newScope;
                    scope.$watch('vdpList', function (n) {

                        if (attr.ngBindHtml) {
                            if (newScope) newScope.$destroy();
                            newScope = scope.$new();
                            $compile(elem[0].children)(newScope)
                        }
                    })

                }
            }
        })
        GlobalApp.directive('fileUpload', function (notificationService) {
            return {
                restrict: 'A',
                link: function (scope, elem, attr) {
                    $(elem).ajaxForm({
                        beforeSubmit: function () {
                            scope.hide = false;
                            scope.$apply()
                            $("button.fileinput-upload-button").prop('disabled', true)
                        },
                        success: function (response) {
                            var data;
                            try{
                                data = JSON.parse(response)
                            }catch(e){
                                data = response
                            }
                            scope.errorLink = data.error
                            scope.hide = true;
                            scope.allLoading = false;
                            scope.$apply()
                            notificationService.notify("success",`Success ${data.data.success}, error ${data.data.fail}`)

                        },
                        error: function (response) {
                            scope.hide = true;
                            scope.allLoading = false;
                            scope.$apply()
                            if(response.status===422){
                                alert("Invalid request. Please check input data")
                            } else{
                                alert("Unknown error. Contact with system admin")
                            }
                        },
                        uploadProgress: function (e, p, t, pc) {
                            var w = (p / t) * 100
                            console.log($(elem).find("#progress-bar"))
                            $("#progress-bar").css({
                                width: w + "%"
                            })
                            $("#p-text").text(parseInt(w) + "% Complete")
                            if (p >= t) {
                                //scope.hide = true;
                                scope.allLoading = true;
                                scope.$apply()
                            }
                        }
                    })

                }
            }
        })
    </script>
    <style>
        .loading-overlay {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
        }
        .fileinput-upload-button{
            display: none;
        }
    </style>
    <section class="content">
        @if(Session::has('success_message'))
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-ok"></span> {{Session::get('success_message')}}
                </div>
            </div>
        @endif
        @if(Session::has('error_message'))
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-danger">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    {{Session::get('error_message')}}
                </div>
            </div>
        @endif
        <div class="box box-solid" ng-controller="VDPController">
            <div class="box-header">
                {{--<filter-template
                        show-item="['range','unit','thana']"
                        type="all"
                        range-change="loadPage()"
                        unit-change="loadPage()"
                        thana-change="loadPage()"
                        data="param"
                        start-load="range"
                        on-load="loadPage()"
                        field-width="{range:'col-sm-4',unit:'col-sm-4',thana:'col-sm-4'}"
                >

                </filter-template>--}}
            </div>
            <div class="box-body">
                <div class="overlay loading-overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>
                <div class="row">
                    <div class="col-sm-6 col-sm-offset-3">
                        <form file-upload action="{{URL::route('AVURP.info.import_upload')}}" method="post"
                              enctype="multipart/form-data">
                            {!! csrf_field() !!}
                            <div class="form-group">
                                <label for="entry_unit" class="control-label">ইউনিট নির্বাচন করুন<sup class="text-red">*</sup>
                                    <span class="pull-right">:</span>
                                </label>
                                <select class="form-control" name="entry_unit" ng-model="param.entry_unit" id="entry_unit">
                                    <option value="">--ইউনিট নির্বাচন করুন--</option>
                                    <option ng-repeat="(k,v) in entryUnits" value="[[k]]">[[v]]</option>
                                </select>
                            </div>
                            <filter-template
                                    show-item="['range','unit','thana','union']"
                                    type="single"
                                    range-change="loadPage()"
                                    unit-change="loadPage()"
                                    thana-change="loadPage()"
                                    data="param"
                                    field-name="{range:'division_id',unit:'unit_id',thana:'thana_id',union:'union_id'}"
                                    start-load="range"
                                    on-load="loadPage()"
                                    layout-vertical="1"

                            >

                            </filter-template>
                            <div class="form-group">
                                <label for="" class="control-label">
                                    Select file to import:
                                </label>
                                <input type="file" name="import_file" class="file" data-show-preview="false">
                            </div>
                            <button type="submit" class="btn btn-primary" ng-disabled="!hide">
                                Upload
                            </button>
                        </form>
                        <div class="progress" ng-hide="hide"
                             style="margin-top: 10px;margin-bottom: 0px;border-radius: 10px;height: 10px;">
                            <div class="progress-bar progress-bar-striped active" id="progress-bar">

                            </div>
                        </div>
                        <p id="p-text" ng-hide="hide" class="text-center text-bold"></p>
                    </div>
                </div>
                <div style="padding: 20px;display: flex;justify-content: center;" ng-if="errorLink">
                    <a href="{{URL::route("AVURP.info.import.download",['file_name'=>''])}}/[[errorLink]]" class="btn btn-primary">
                        <i class="fa fa-download"></i>&nbsp;Download error data
                    </a>
                </div>
                <div ng-bind-html="vdpList" compile-html>

                </div>
            </div>
        </div>
    </section>

@endsection