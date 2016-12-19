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
            $scope.printType = "smartCardNo"

            $scope.unit = {
                selectedUnit: [],
                param:''
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
            $scope.generateLetter = function (id,i) {
                $scope.allLoading = true;
                console.log($scope.param);
                var unit = ''
                if($scope.printType=='smartCardNo'){
                    unit = $scope.unit.param.unit;
                }
                else{
                    unit=$scope.unit.selectedUnit[i] == undefined ? $scope.unit.selectedUnit : $scope.unit.selectedUnit[i]
                }
                $http({
                    method:'get',
                    url:'{{URL::route('print_letter')}}',
                    params:{
                        id:id,
                        option:$scope.printType,
                        type:'EMBODIMENT',
                        unit:unit
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
                                   {!! Form::hidden('type','EMBODIMENT') !!}
                                   @if(auth()->user()->type!=22)
                                       {!! Form::hidden('unit','[[unit.param.unit ]]') !!}
                                   @else
                                       {!! Form::hidden('unit',auth()->user()->district?auth()->user()->district->id:'') !!}
                                   @endif
                                   <button class="btn btn-primary">Generate Embodied Letter</button>
                                   {!! Form::close() !!}
                               </div>
                           </div>
                       </div>
                   </div>
                   <div ng-if="printType=='memorandumNo'">
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
                                   <td>
                                       [[d.memorandum_id]]
                                   </td>
                                   <td>[[d.mem_date?(d.mem_date):'n/a']]</td>
                                   <td>
                                       <select ng-if="!isDc" class="form-control" name="unit" ng-model="unit.selectedUnit[$index]"
                                               ng-disabled="units.length==0">
                                           <option value="">--@lang('title.unit')--</option>
                                           <option ng-repeat="u in units" value="[[u.id]]">[[u.unit_name_bng]]</option>
                                       </select>

                                       <div ng-if="isDc">
                                           {{auth()->user()->district?auth()->user()->district->unit_name_eng:''}}
                                       </div>
                                   </td>
                                   <td>
                                       {!! Form::open(['route'=>'print_letter','target'=>'_blank']) !!}
                                       {!! Form::hidden('option','memorandumNo') !!}
                                       {!! Form::hidden('id','[[d.memorandum_id]]') !!}
                                       {!! Form::hidden('type','EMBODIMENT') !!}
                                       @if(auth()->user()->type!=22)
                                           {!! Form::hidden('unit','[[unit.selectedUnit[$index] ]]') !!}
                                           @else
                                       {!! Form::hidden('unit',auth()->user()->district?auth()->user()->district->id:'') !!}
                                       @endif
                                       <button class="btn btn-primary">Generate Embodied Letter</button>
                                       {!! Form::close() !!}
                                   </td>
                               </tr>

                               <tr ng-if="datas==undefined||datas.length<=0||results.length<=0">
                                   <td class="warning" colspan="5">No Memorandum no. available</td>
                               </tr>
                           </table>
                       </div>
                   </div>
                   <div ng-bind-html="letterPrintView"></div>
               </div>
            </div>
        </section>
    </div>
@stop