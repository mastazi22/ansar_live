@extends('template.master')
@section('title','Applicant Editable fields')
@section('breadcrumb')
    {!! Breadcrumbs::render('recruitment.point.index') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('applicantQuota', function ($scope, $http, $q, httpService,notificationService) {
            $scope.mapping = {
                "applicant_name_eng":"Applicant name(English)",
                "applicant_name_bng":"Applicant name(Bangle)",
                "date_of_birth":"Birth Date",
                "father_name_bng":"Father name(Bangle)",
                "mother_name_bng":"Mother name(Bangle)",
                "marital_status":"Marital status",
                "national_id_no":"National id no.",
                "division_id":"Division",
                "unit_id":"District",
                "thana_id":"Thana",
                "post_office_name_bng":"Post office name(Bangle)",
                "village_name_bng":"Village name(Bangle)",
                "union_name_bng":"Union name(Bangle)",
                "height_feet":"Height in feet",
                "height_inch":"Height in inch",
                "chest_normal":"Chest normal",
                "chest_extended":"Chest extended",
                "weight":"Weight",
                "gender":"Gender",
                "mobile_no_self":"Mobile no(self)",
                "training_info":"Training info",
                "connection_name":"Reference name",
                "connection_relation":"Relation with reference",
                "connection_address":"Reference address",
                "connection_mobile_no":"Reference mobile",
                "education":"Education"
            }
            $scope.pointFields = [];
            $scope.rows = [];
            $scope.exFields = ''
            $scope.allLoading = true;
            $q.all([
                $http({method: 'get', url: '{{URL::route('recruitment.applicant.getfieldstore')}}'}),
                $http({
                    url:'{{URL::route('recruitment.point.fields')}}',
                    method:'post'
                })

            ]).then(function (response) {
                $scope.allLoading = false;
                $scope.pointFields = response[1].data;
                $scope.exFields = response[0].data.field_value.split(',')
                var keys = Object.keys($scope.mapping)
                $scope.rows = new Array(keys.length);
                for(var i=0;i<keys.length;i++){
                    if($scope.exFields.indexOf(keys[i])>=0){
                        $scope.rows[i] = keys[i];
                    }
                    else{
                        $scope.rows[i] = false;
                    }
                }
            },function (response) {
                $scope.allLoading = false;
            })
            $scope.saveField = function () {
                $scope.allLoading = true;
                $http({
                    url:'{{URL::route('recruitment.applicant.editfieldstore')}}',
                    data:{
                        fields:$scope.rows
                    },
                    method:'post'
                }).then(function (response) {
                    $scope.allLoading = false;
                    notificationService.notify(response.data.status,response.data.message);
                },function (response) {
                    $scope.allLoading = false;
                })
                console.log($scope.rows);
            }
        })
    </script>
    <section class="content" ng-controller="applicantQuota">
        <div class="box box-solid">
            <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
            </div>
            <div class="box-body">
                <div>
                    <div style="display: inline-block;margin-right: 50px" ng-repeat="(k,v) in mapping">
                        <input type="checkbox" id="[[k]]" ng-model="rows[$index]" ng-true-value="'[[k]]'"
                               class="fancy-checkbox">
                        <label for="[[k]]" class="control-label">[[v]]</label>
                    </div>
                </div>
                <div class="row" style="margin-top: 20px">
                    <div class="col-sm-12">
                        <button ng-click="saveField()" class="bt btn-primary pull-right"><i class="fa fa-save"></i>&nbsp;Save</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection