@extends('template/master')
@section('title','Entry Report')
@section('breadcrumb')
    {!! Breadcrumbs::render('entry_report',$ansarAllDetails->ansar_id) !!}
    @endsection
@section('content')

    <script>
        GlobalApp.controller("EntryReportController", function ($scope) {
            $scope.changeToLocal = function (v) {
                var b = moment(v);
                return b.locale('bn').format('DD-MMMM-YYYY');
            }
        })
        $(document).ready(function () {
            $("#print-report").on('click', function (e) {
                e.preventDefault();
                $('body').append('<div id="print-area" class="letter">' + $("#entry-report").html() + '</div>')
                window.print();
                $("#print-area").remove()
            })
        })
    </script>

    <div ng-controller="EntryReportController">
        <section class="content">
            <div class="row " id="entry-report">
                <div class="box box-solid" style="width:70%;margin:0 auto;">
                    <div class="box-body">
                        @include('HRM::EntryForm.entry_info',compact('ansarAllDetails','label'))
                    </div>
                </div>
            </div>
            {{--<div style="width: 70%;margin: 12px auto;position: relative;left: -10px">--}}
            {{--<button id="print-report" class="btn btn-primary" style="display: block;">--}}
            {{--<i class="fa fa-print"></i> Print Report--}}
            {{--</button>--}}
            {{--</div>--}}
        </section>
    </div>


@stop