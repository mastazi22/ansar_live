@extends('template.master')
@section('title','Offer Quota')
@section('breadcrumb')
    {!! Breadcrumbs::render('offer_quota') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('QuotaController', function ($scope, $rootScope, $http) {
            $scope.quotas = [];
            $scope.param = {};
            $scope.loadQuota = function () {
                console.log($scope.param)
                $http({
                    url: '/HRM/get_offer_quota',
                    method: 'get',
                    params: {range: $scope.param.range}
                }).then(function (response) {

                    $scope.quotas  = response.data;
                },function (response) {

                })
            }
        })
    </script>
    <div ng-controller="QuotaController">
        {{--<div class="breadcrumbplace">--}}
        {{--{!! Breadcrumbs::render('offer_quota') !!}--}}
        {{--</div>--}}
        <section class="content">

            <div class="box box-solid">
                <div class="box-body">
                    <filter-template
                            show-item="['range']"
                            type="all"
                            range-change="loadQuota()"
                            on-load="loadQuota()"
                            data="param"
                            start-load="range"
                            field-width="{range:'col-sm-5'}"
                    ></filter-template>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">

                            <tr>
                                <th>SL. NO</th>
                                <th>Unit name</th>
                                <th>Total Offer quota</th>
                                <th>Used Offer quota</th>
                                <th>Offer quota Left</th>
                            </tr>
                            <tr ng-repeat="q in quotas">
                                <td>[[$index+1]]</td>
                                <td>[[q.unit_name_bng]]</td>
                                <td>[[q.total_quota]]</td>
                                <td>[[q.quota_used]]</td>
                                <td>[[q.total_quota-q.quota_used]]</td>
                            </tr>
                            <tr ng-if="quotas.length<=0">
                                <td class="text text-yellow" colspan="5">No data available</td>
                            </tr>

                        </table>

                    </div>
                    {{--<form id="offer-quota-form" action="{{URL::route('update_offer_quota')}}" method="post">
                        {{csrf_field()}}
                        @foreach($quota as $q)
                        <div class="row margin-bottom-input form-group">
                            <div class="col-sm-4">
                                <label class="control-label">{{$q->unit_name_eng}}</label>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <input type="hidden" name="quota_id[]" value="{{$q->unit}}">
                                    <input type="text"  class="form-control" name="quota_value[]"
                                           placeholder="Enter quota" value="{{$q->quota}}">
                                </div>
                            </div>
                        </div>
                        @endforeach
                        <button id="update-quota"  type="submit" class="btn btn-primary">
                            <i id="ni" class="fa fa-save"></i></i>&nbsp; Save</button>
                    </form>--}}
                </div>
            </div>
        </section>
    </div>
@stop