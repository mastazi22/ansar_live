@extends('template.master')
@section('title','Entry List')
@section('breadcrumb')
    {!! Breadcrumbs::render('entry.list') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('VDPController',function ($scope, $http, $sce) {
            $scope.param = {};
            $scope.allLoading = false;
            $scope.hide = true;
        })
        GlobalApp.directive('compileHtml',function ($compile) {
            return {
                restrict:'A',
                link:function (scope,elem,attr) {
                    var newScope;
                    scope.$watch('vdpList', function (n) {

                        if (attr.ngBindHtml) {
                            if(newScope) newScope.$destroy();
                            newScope = scope.$new();
                            $compile(elem[0].children)(newScope)
                        }
                    })

                }
            }
        })
        GlobalApp.directive('fileUpload',function () {
            return {
                restrict:'A',
                link:function (scope,elem,attr) {
                    $(elem).ajaxForm({
                        beforeSubmit:function () {
                          scope.hide = false;
                          scope.$apply()
                        },
                        success:function (response) {

                        },
                        error:function (response) {

                        },
                        uploadProgress:function (e, p, t, pc) {
                            var w = (p/t)*100
                            console.log($(elem).find("#progress-bar"))
                            $("#progress-bar").css({
                                width:w+"%"
                            })
                            $("#p-text").text(parseInt(w)+"% Complete")
                        }
                    })

                }
            }
        })
    </script>
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
                <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>
                <div class="row">
                    <div class="col-sm-6 col-sm-offset-3">
                        <form file-upload action="{{URL::route('AVURP.info.import_upload')}}" method="post" enctype="multipart/form-data">
                            {!! csrf_field() !!}
                            <div class="form-group">
                                <label for="" class="control-label">
                                    Select File :
                                </label>
                                <input type="file" name="import_file" class="file" data-show-preview="false">
                            </div>
                        </form>
                        <div class="progress" ng-hide="hide" style="margin-top: 10px;margin-bottom: 0px;border-radius: 10px;height: 10px;">
                            <div class="progress-bar progress-bar-striped active" id="progress-bar">

                            </div>
                        </div>
                        <p id="p-text" ng-hide="hide" class="text-center text-bold"></p>
                    </div>
                </div>
                <div ng-bind-html="vdpList" compile-html>

                </div>
            </div>
        </div>
    </section>

@endsection