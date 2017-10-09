@extends('template.master')
@section('title','Disembodiment Letter')
@section('breadcrumb')
    {!! Breadcrumbs::render('disembodiment_letter_view') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('DisEmbodiedLetterController', function ($scope, $http, $sce,$rootScope,httpService) {
            $scope.letterView = $sce.trustAsHtml("&nbsp;")
            $scope.printType = "smartCardNo"
            $scope.unit = {
                selectedUnit: []
            };
            $scope.units = [];
            $scope.allLoading = false;
            $scope.q = '';
            $scope.isDc = $rootScope.user.type==22 ? true : false;
            if ($scope.isDc) {
                $scope.unit.selectedUnit = $rootScope.user.district_id
            }
            else {
                httpService.unit().then(function (response) {
                    $scope.units = response.data;
                }, function (response) {

                })
            }
            $scope.loadData = function (url,q) {
                $scope.allLoading = true;
                $http({
                    method: 'get',
                    params: {type: "DISEMBODIMENT",q:q},
                    url: url==undefined?'{{URL::route('letter_data')}}':url
                }).then(function (response) {
                    if (!$scope.isDc) $scope.unit.selectedUnit = [];
                    $scope.letterView = $sce.trustAsHtml(response.data);
                    $scope.allLoading = false;
                },function (res) {
                    $scope.allLoading = false;
                })
            }
        })
        GlobalApp.directive('compileHtml',function ($compile) {
            return {
                restrict:'A',
                link:function (scope,elem,attr) {
                    scope.$watch('letterView',function(n){

                        if(attr.ngBindHtml) {
                            $compile(elem[0].children)(scope)
                        }
                    })

                }
            }
        })
        GlobalApp.directive('paginate',function () {
            return {
                restrict:'A',
                scope:{
                    ref:'&'
                },
                link:function (scope,elem,attr) {
                    $(elem).find('.pagination a').on('click',function (e) {
                        e.preventDefault();
                        var urll = $(this).attr('href')
                        scope.ref({url:urll})
                    })

                }
            }
        })
    </script>
    <div ng-controller="DisEmbodiedLetterController" ng-init="loadData()">
        <section class="content">

            <div class="box box-solid">
                <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-3 col-xs-6">
                            <div class="form-group">
                                <input type="radio" ng-model="printType" value="smartCardNo">
                                <span class="text text-bold" style="vertical-align: top">Print by Smart card no.</span>
                            </div>
                        </div>
                        <div class="col-sm-3 col-xs-6">
                            <div class="form-group">
                                <input type="radio" ng-model="printType" value="memorandumNo">
                                <span class="text text-bold" style="vertical-align: top">Print by Memorandum no.</span>
                            </div>
                        </div>
                    </div>
                    <div ng-if="printType=='smartCardNo'">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="">Smart Card no.</label>
                                    <input type="text" ng-model="smartCardNo" placeholder="Enter Smart Card no." class="form-control">
                                </div>
                                <div class="form-group">
                                    <filter-template
                                            show-item="['unit']"
                                            type="single"
                                            data="unit.param"
                                            start-load="unit"
                                            layout-vertical="1"
                                    >
                                    </filter-template>
                                </div>
                                <div class="form-group">
                                    {!! Form::open(['route'=>'print_letter','target'=>'_blank']) !!}
                                    {!! Form::hidden('option','smartCardNo') !!}
                                    {!! Form::hidden('id','[[smartCardNo]]') !!}
                                    {!! Form::hidden('type','DISEMBODIMENT') !!}
                                    @if(auth()->user()->type!=22)
                                        {!! Form::hidden('unit','[[unit.param.unit ]]') !!}
                                    @else
                                        {!! Form::hidden('unit',auth()->user()->district?auth()->user()->district->id:'') !!}
                                    @endif
                                    <button class="btn btn-primary">Generate DisEmbodiment Letter</button>
                                    {!! Form::close() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div ng-if="printType=='memorandumNo'">
                        <div id="letter_data_view" ng-bind-html="letterView" compile-html></div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@stop