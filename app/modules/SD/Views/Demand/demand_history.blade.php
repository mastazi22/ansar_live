@extends('template.master')
@section('content')
    <section class="content-header">
        <h1>Demand History</h1>
    </section>
    <section class="content" ng-controller="demandSheetController">
        <div class="box box-primary">
            <!-- form start -->

            <div class="box-body">
                @foreach($logs as $log)
                    @endforeach
            </div><!-- /.box-body -->

            <div class="box-footer">

            </div>

        </div>
    </section>
@endsection