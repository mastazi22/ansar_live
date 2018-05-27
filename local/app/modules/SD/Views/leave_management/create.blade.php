@extends('template.master')
@section('title','Grant Leave')
@section('breadcrumb')
    {!! Breadcrumbs::render('grant_leave') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller("GrantLeave", function ($scope, $http, $sce) {
        })
    </script>

    <section class="content" ng-controller="GrantLeave">
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
        <div class="box box-solid">
            <div class="box-header">
            </div>
            <div class="box-body">
                <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <form >
                            <div class="form-group">
                                <label for="" class="control-label" style="display: block">Ansar ID</label>
                                <div class="input-group">

                                    <input type="text" class="form-control" placeholder="Ansar ID">
                                    <span class="input-group-btn">
                                        <button class="btn btn-primary">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Select Dates</label>
                                <div id="dates">

                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-sm-8">

                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        .ui-widget-content{
            width:auto !important;
        }
    </style>
    <script>
        $(document).ready(function () {
            alert(1);
            $("#dates").multiDatesPicker()
        })
    </script>
@endsection