@extends('template.master')
@section('title','Ansar Info Detail')
@section('breadcrumb')
    {!! Breadcrumbs::render('orginal_info') !!}
@endsection
@section('content')
    <script>
        {{--GlobalApp.controller('originalInfo', function ($scope, $http) {--}}
            {{--$scope.isSearching = false;--}}
            {{--$scope.fullInfo = function (keyEvent, id) {--}}
                {{--if (keyEvent.type == 'keypress') {--}}
                    {{--if (keyEvent.which === 13) {--}}
                        {{--$scope.ID = id;--}}
                        {{--$scope.isSearching = true;--}}
                        {{--$http({--}}
                            {{--url: "{{URL::to('HRM/idsearch')}}",--}}
                            {{--method: 'post',--}}
                            {{--data: {ansarId: id}--}}
                        {{--}).then(function (response) {--}}
{{--//                        alert(JSON.stringify(response.data));--}}
                            {{--$scope.searchedAnsar = response.data;--}}
                            {{--console.log($scope.searchedAnsar);--}}
                        {{--})--}}
                    {{--}--}}
                {{--}--}}
                {{--else if (keyEvent.type == 'click') {--}}
                    {{--$scope.ID = id;--}}
                    {{--$scope.isSearching = true;--}}
                    {{--$http({--}}
                        {{--url: "{{URL::to('HRM/idsearch')}}",--}}
                        {{--method: 'post',--}}
                        {{--data: {ansarId: id}--}}
                    {{--}).then(function (response) {--}}
                        {{--$scope.searchedAnsar = response.data;--}}
                        {{--$scope.fontURL = $scope.searchedAnsar.url.font--}}
                        {{--$scope.backURL = $scope.searchedAnsar.url.back--}}
                        {{--console.log($scope.searchedAnsar);--}}
                    {{--}, function (response) {--}}
                        {{--$scope.searchedAnsar = {status: false}--}}
                    {{--})--}}
                {{--}--}}
            {{--}--}}
        {{--})--}}
    </script>

    <div>
        <section class="content">
            <div class="box box-solid">
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-8 col-md-8 col-xs-12 col-lg-6 col-centered">
                            <form method="post" action="{{URL::route('generate_ansar_detail_info')}}">
                                {!! csrf_field() !!}
                                <div class="center-search">
                                    <textarea class="form-control" name="ansar_ids" id="" cols="30" rows="10" placeholder="Enter Ansar ID separated by comma(,) "></textarea>
                                    <button type="submit" class="btn btn-primary" style="display: block;margin: 20px auto;">Generate Excel File</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <script>
    </script>
@stop