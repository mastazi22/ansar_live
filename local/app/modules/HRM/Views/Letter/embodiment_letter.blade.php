@extends('template.master')
@section('title','Embodiment Letter')
@section('breadcrumb')
    {!! Breadcrumbs::render('embodiment_letter_view') !!}
@endsection
@section('content')
    <script>
        $(function () {
            $(document).on('click','#print-report', function (e) {
                e.preventDefault();
                $('body').append('<div id="print-area" class="letter">'+$(".letter").html()+'</div>')
                window.print();
                $("#print-area").remove()
            })
        })
        GlobalApp.controller('EmbodimentLetterController', function ($scope,$http,$sce) {
            $scope.letterPrintView = $sce.trustAsHtml("&nbsp;")
            $scope.unit = {
                selectedUnit: []
            };
            $scope.units = [];
            $scope.loadingLetter=false;
            $scope.memId = ""
            $scope.allLoading = false;
            $scope.isDc = parseInt("{{auth()->user()->type}}")==22?true:false;
            if($scope.isDc){
                $scope.unit.selectedUnit = parseInt("{{auth()->user()->district_id}}")
            }
            else{
                $http({
                    method:'get',
                    url:'{{URL::to('HRM/DistrictName')}}'
                }).then(function (response) {
                    $scope.units = response.data;
                }, function (response) {

                })
            }
            $scope.loadData = function () {
                $http({
                    method: 'get',
                    params: {type: "EMBODIED"},
                    url: '{{URL::route('letter_data')}}'
                }).then(function (response) {
                    if (!$scope.isDc) $scope.unit.selectedUnit = [];
                    $scope.datas = response.data;
                    console.log($scope.datas)
                })
            }
            $scope.generateLetter = function (i) {
                $scope.allLoading = true;
                $http({
                    method:'get',
                    url:'{{URL::route('print_letter')}}',
                    params:{
                        id:$scope.datas[i].memorandum_id,
                        type:'EMBODIMENT',
                        unit:$scope.unit.selectedUnit[i] == undefined ? $scope.unit.selectedUnit : $scope.unit.selectedUnit[i]
                    }
                }).then(function (response) {
                    $scope.letterPrintView = $sce.trustAsHtml(response.data);
                    $scope.allLoading = false;
                },function(response){
                    $scope.letterPrintView = $sce.trustAsHtml("<h4 class='text-danger' style='text-align: center'>"+response.data+"</h4>");
                    $scope.allLoading = false;
                })
            }
        })
    </script>
    <div ng-controller="EmbodimentLetterController" ng-init="loadData()">
        <section class="content">
            <div class="box box-solid">
                <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>
               <div class="box-body">
                   {{--<div class="row">--}}
                       {{--<div class="col-md-4 col-sm-12 col-xs-12">--}}
                           {{--<div class="form-group">--}}
                               {{--<label class="control-label">Enter Memorandum No.</label>--}}
                               {{--<input class="form-control" ng-model="memId" type="text" placeholder="Memorandum No">--}}
                           {{--</div>--}}
                       {{--</div>--}}
                       {{--<div class="col-md-4 col-sm-12 col-xs-12">--}}
                           {{--<div class="form-group" ng-if="!isDc">--}}
                               {{--<label class="control-label">@lang('title.unit')</label>--}}
                               {{--<select class="form-control" ng-model="unit.selectedUnit" ng-disabled="units.length==0">--}}
                                   {{--<option value="">--@lang('title.unit')--</option>--}}
                                   {{--<option ng-repeat="u in units" value="[[u.id]]">[[u.unit_name_bng]]</option>--}}
                               {{--</select>--}}
                           {{--</div>--}}
                       {{--</div>--}}
                       {{--<div class="col-md-4 col-sm-12 col-xs-12" style="margin-top: 25px">--}}
                           {{--<button class="btn btn-primary" ng-click="generateLetter(memId)" ng-disabled="isGenerating">--}}
                               {{--<i ng-show="isGenerating " class="fa fa-spinner fa-spin"></i><span ng-class="{'blink-animation':isGenerating}">Generate Embodiment Letter</span>--}}
                           {{--</button>--}}
                       {{--</div>--}}
                   {{--</div>--}}
                   <div class="table-responsive">
                       <table class="table table-bordered table-striped">
                           <caption>
                               <table-search q="q" results="results" place-holder="Search Memorandum no."></table-search>
                           </caption>
                           <tr>
                               <th>#</th>
                               <th>Memorandum no.</th>
                               <th>Memorandum Date</th>
                               <th>Unit</th>
                               <th>Action</th>
                           </tr>
                           <tr ng-repeat="d in datas|filter: q as results">
                               <td>[[$index+1]]</td>
                               <td>[[d.memorandum_id]]</td>
                               <td>[[d.mem_date?(d.mem_date):'n/a']]</td>
                               <td>
                                   <select ng-if="!isDc" class="form-control" ng-model="unit.selectedUnit[$index]"
                                           ng-disabled="units.length==0">
                                       <option value="">--@lang('title.unit')--</option>
                                       <option ng-repeat="u in units" value="[[u.id]]">[[u.unit_name_bng]]</option>
                                   </select>

                                   <div ng-if="isDc">
                                       {{auth()->user()->district?auth()->user()->district->unit_name_eng:''}}
                                   </div>
                               </td>
                               <td>
                                   <button class="btn btn-primary" ng-click="generateLetter($index)"
                                           ng-disabled="isGenerating">
                                       <i ng-show="isGenerating " class="fa fa-spinner fa-spin"></i><span
                                               ng-class="{'blink-animation':isGenerating}">Generate Embodied Letter</span>
                                   </button>
                               </td>
                           </tr>
                       </table>
                   </div>
                   <div ng-bind-html="letterPrintView"></div>
               </div>
            </div>
        </section>
    </div>
@stop