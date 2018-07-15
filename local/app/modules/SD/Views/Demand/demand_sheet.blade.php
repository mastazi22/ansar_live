@extends('template.master')
@section('content')
    <script>
        GlobalApp.controller('demandSheetController', function ($scope) {
            $scope.showSuccess = false;
            $scope.showError = false;
            $scope.data = '';
            $scope.errors  = '';

        })
        GlobalApp.directive('submitForm',function(){
            return{
                restrict:'A',
                link: function (scope,element,attrs) {
                    $(element).ajaxForm({
                        beforeSubmit: function () {
                            $("#llllll").show();
                            $("#llllll").parents("button").attr("disabled",true)
                        },
                        success: function (response) {
                            //console.log(response);
                            $("#llllll").parents("button").attr("disabled",false)
                            $("#llllll").hide();
                            if(response.error){
                                scope.errors = response.messages
                                console.log(scope.errors)
                            }
                            else if(response.status) scope.showSuccess = true;
                            else scope.showError = true;
                            if(response.data!=undefined) scope.data = response.data;
                            scope.$apply()
                        },
                        error:function (response) {
                            $("#llllll").parents("button").disable(false)
                            $("#llllll").hide();
                        }
                    })
                }
            }
        })
    </script>
    <section class="content-header">
        <h1>Demand Sheet</h1>
    </section>
    <section class="content" ng-controller="demandSheetController">
        <div class="box box-primary">
            <!-- form start -->

            <div class="box-body">
                <div class="row">

                    <div class="col-sm-4">
                        <div ng-if="showSuccess" class="alert alert-success" id="alert-success">Demand sheet generation complete</div>
                        <div ng-if="showError" class="alert alert-danger" id="alert-error">[[data]]</div>
                        <form role="form" submit-form id="demand_sheet_form" action="{{URL::to('SD/generatedemandsheet')}}"
                              method="post">

                            {!! csrf_field() !!}
                            <filter-template
                                    show-item="['kpi','unit','thana']"
                                    type="single"
                                    data="param"
                                    start-load="unit"
                                    layout-vertical="1"
                                    field-name="{unit:'unit',kpi:'kpi'}"

                            >
                            </filter-template>
                            <div class="form-group" ng-class="{'has-error':errors.mem_id!=undefined}">
                                <label for="memid">Memorandum no.</label>
                                <input class="form-control" id="memid" name="mem_id" type="text" placeholder="Enter memorandum no">
                                <p ng-if="errors.mem_id!=undefined" class="text text-danger">[[errors.mem_id[0] ]]</p>
                            </div>
                            <div class="form-group" ng-class="{'has-error':errors.form_date!=undefined}">
                                <label for="from_date">From date</label>
                                <input class="form-control dddd" id="from_date" name="form_date" type="text">
                                <p ng-if="errors.form_date!=undefined" class="text text-danger">[[errors.form_date[0] ]]</p>
                            </div>
                            <div class="form-group" ng-class="{'has-error':errors.to_date!=undefined}">
                                <label for="to_date">To date</label>
                                <input class="form-control dddd" id="to_date" name="to_date" type="text">
                                <p ng-if="errors.to_date!=undefined" class="text text-danger">[[errors.to_date[0] ]]</p>
                            </div>
                            <div class="form-group" ng-class="{'has-error':errors.other_date!=undefined}">
                                <label for="Other_date">Request payment date</label>
                                <input class="form-control dddd" id="Other_date" name="other_date" type="text">
                                <p ng-if="errors.other_date!=undefined" class="text text-danger">[[errors.other_date[0] ]]</p>
                            </div>
                            <div class="form-group">
                                <label class="control-label">
                                    <input type="checkbox" value="1" name="no_margha_fee">&nbsp;Don`t add margha fee.
                                </label>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i id="llllll" style="display: none" class="fa fa-refresh fa-spin"></i>&nbsp;&nbsp;Generate
                                    Demand Sheet
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="col-sm-8">
                            <a style="display: block;margin: 30px auto;width: 50%" ng-if="!isNaN(data)&&data" ng-class="{jello:!isNaN(data)&&data}"  class="btn btn-info btn-lg animated" href="{{URL::to('SD/download_demand_sheet')}}/[[data]]">
                                <i class="fa fa-download"></i>&nbsp;Download Demand Sheet
                            </a>
                    </div>
                </div>
            </div><!-- /.box-body -->

            <div class="box-footer">

            </div>

        </div>
    </section>
    <script>
        $(".dddd").datepicker({                dateFormat:'dd-M-yy'            })(false)
    </script>
@endsection