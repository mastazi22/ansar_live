@extends('template.master')
@section('content')
    <script>
        GlobalApp.controller('QuotaController', function ($scope, $http) {
            $scope.saveOrUpdating = [];
            $http({
                url: '{{URL::route('get_offer_quota')}}',
                method: 'get'
            }).then(function (response) {
                $scope.districts = response.data;
            })
        })
        $(document).ready(function (e) {
            $("#update-quota").on('click', function (e) {
                e.preventDefault();
                $(this).children('i').removeClass('fa-save').addClass('fa-spinner fa-pulse');
                $("#offer-quota-form").ajaxSubmit({
                    success: function (response,status,xhr) {
                        $('#update-quota').children('i').addClass('fa-save').removeClass('fa-spinner fa-pulse');
                        console.log(response)
                        if(response.status){
                            $('body').notifyDialog({type: 'success', message: "Offer Quota Updated Successfully"}).showDialog()
                        }
                        else{
                            $('body').notifyDialog({type: 'error', message: "An error occur while updating. Please try again later"}).showDialog()
                        }
                    },
                    error: function (response,status,xhr) {
                        console.log(response)
                        $('#update-quota').children('i').addClass('fa-save').removeClass('fa-spinner fa-pulse');
                        $('body').notifyDialog({type: 'error', message: "An server error occur.Please contact with your server Administration.Error Code:"+response.status}).showDialog()
                    }
                })
            })
        })
    </script>
    <div ng-controller="QuotaController">
        {{--<div class="breadcrumbplace">--}}
            {{--{!! Breadcrumbs::render('offer_quota') !!}--}}
        {{--</div>--}}
        <section class="content">
            <div class="box box-solid">
                <div class="box-body" style="width: 70%;margin: 0 auto">
                    <form id="offer-quota-form" action="{{URL::route('update_offer_quota')}}" method="post">
                        {{csrf_field()}}
                        <div class="row margin-bottom-input form-group" ng-repeat="district in districts">
                            <div class="col-sm-4">
                                <label class="control-label">[[district.unit_name_eng]]</label>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <input type="hidden" name="quota_id[]" value="[[district.unit]]">
                                    <input type="text"  class="form-control" name="quota_value[]"
                                           placeholder="Enter quota" ng-model="district.quota">
                                </div>
                            </div>
                        </div>
                        <button id="update-quota"  type="submit" class="btn btn-primary">
                            <i id="ni" class="fa fa-save"></i></i>&nbsp; Save</button>
                    </form>
                </div>
            </div>
        </section>
    </div>
@stop