@extends('template.master')
@section('title','Offer Zone')
@section('breadcrumb')
    {!! Breadcrumbs::render('recruitment.point.index') !!}
@endsection
@section('content')
    <style>
        .tree-parent {
            position: relative;
        }

        .tree-parent:before {
            content: '';
            position: absolute;
            left: 3px;
            bottom: 11px;
            top: 15px;
            border-left: 1px solid #ababab;
        }

        .tree-child {
            list-style: none;
            position: relative;
        }

        .tree-child > li {
            position: relative;
        }

        .tree-child > li:before {
            content: '';
            position: absolute;
            left: -37px;
            width: 37px;
            bottom: 0;
            top: 8px;
            height: 1px;
            border-top: 1px solid #ababab;
        }
    </style>
    <script>
        GlobalApp.controller('offerZoneController', function ($scope, $http,$sce) {
            $scope.params={};
            $scope.offerZones = [];
            $scope.getUrl = function(u){
                return $sce.trustAsResourceUrl(u)
            };

            $scope.loadOfferZoneDetail = function () {
                $http({
                    url:'{{URL::route("HRM.offer_zone.index")}}',
                    params:$scope.params,
                    method:'get'
                }).then(function(res){
                    $scope.offerZones = res.data;
                },function(res){

                })
            }
        })
    </script>
    <section class="content" ng-controller="offerZoneController">
        <div class="box box-solid" style="padding: 10px;">
            @if(Session::has('session_error'))
                <div class="alert alert-danger">
                    <i class="fa fa-warning"></i>&nbsp;{{Session::get('session_error')}}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                </div>
            @elseif(Session::has('session_success'))
                <div class="alert alert-success">
                    <i class="fa fa-check"></i>&nbsp;{{Session::get('session_success')}}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                </div>
            @endif

            <div class="box-body">
                <filter-template
                        show-item="['range','unit']"
                        type="all"
                        data="params"
                        start-load="range"
                        unit-load="resetData()"
                        unit-change="loadOfferZoneDetail()"
                        range-change="loadOfferZoneDetail()"
                        on-load="loadOfferZoneDetail()"
                        field-width="{range:'col-sm-4',unit:'col-sm-4'}"
                >
                </filter-template>
                <table class="table table-bordered">
                    <caption><h3>
                            Offer Zone&nbsp;&nbsp;<a href="{{URL::route('HRM.offer_zone.create')}}" class="btn btn-xs btn-primary">
                                <i class="fa fa-plus"></i>&nbsp;Create offer zone
                            </a>
                        </h3> </caption>
                    <tr>
                        <th>SL. No</th>
                        <th>Range Name</th>
                        <th>Unit Name</th>
                        <th>Offer Zone Area</th>
                        <th>Action</th>
                    </tr>
                    <tr ng-if="offerZones.length<=0">
                        <td colspan="5" class="bg-warning">No Data Available</td>
                    </tr>
                    <tr ng-if="offerZones.length>0" ng-repeat="offerZone in offerZones">
                        <td>[[$index+1]]</td>
                        <td>[[offerZone.range]]</td>
                        <td>[[offerZone.unit]]</td>
                        <td>
                            <ul style="list-style: none;padding: 0">
                                <li class="tree-parent" ng-repeat="d in offerZone.areas" >
                                        <span style="display: flex;align-items: center">
                                            <i ng-click="toggleClick[$index]=!toggleClick[$index]" ng-class="{'fa fa-plus':!toggleClick[$index],'fa fa-minus':toggleClick[$index]}" style="font-size: 10px;font-weight: normal !important;cursor: pointer"></i>[[d.division.division_name_bng]]
                                        </span>
                                    <ul class="tree-child" ng-if="toggleClick[$index]">
                                        <li ng-repeat="u in d.units">
                                            <span>[[u.unit_name_bng]]</span>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </td>
                        <td >
                            <a href="/HRM/offer_zone/[[offerZone.unitId]]/edit" class="btn btn-xs btn-primary">
                                <i class="fa fa-edit"></i>&nbsp;Edit
                            </a>
                            <form action="[[getUrl('/HRM/offer_zone'+'/'+offerZone.unitId)]]" method="post" style="display: inline">
                                <input name="_method" type="hidden" value="DELETE" autocomplete="off">
                                {!! csrf_field()!!}
                                <button type="submit" class="btn btn-xs btn-danger">
                                    <i class="fa fa-close"></i>&nbsp;Delete
                                </button>
                            </form>
                        </td>
                    </tr>

                </table>

            </div>
        </div>
        </div>
    </section>
@endsection