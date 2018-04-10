@extends('template.master')
@section('title','Sub Training Setting')
{{--@section('small_title','DG')--}}
@section('breadcrumb')
    {!! Breadcrumbs::render('range.index') !!}
@endsection
@section('content')
    @if(Session::has('success'))
        <div class="alert alert-success">
            <i class="fa fa-check"></i>&nbsp;{{Session::get('success_message')}}
        </div>
        @endif
    @if(Session::has('error'))
        <div class="alert alert-danger">
            <i class="fa fa-remove"></i>&nbsp;{{Session::get('error_message')}}
        </div>
    @endif
    <section class="content">
        <div class="box box-solid">
            <div class="box-header">
                <a href="{{URL::route('HRM.sub_training.create')}}" title="New Training Info" class="btn btn-primary btn-sm pull-right">
                    <i class="fa fa-plus"></i>&nbsp;New Sub Training Info
                </a>
                <h3 class="box-title">Total : {{count($data)}}</h3>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>#</th>
                            <th>Main Training Name</th>
                            <th>Sub Training Name ENG</th>
                            <th>Sub Training Name BNG</th>
                            <th>Action</th>
                        </tr>
                        <?php $i=1; ?>
                        @forelse($data as $training)
                            <tr>
                                <td>{{$i++}}</td>
                                <td>{{$training->mainTraining->training_name_bng}}</td>
                                <td>{{$training->training_name_eng}}</td>
                                <td>{{$training->training_name_bng}}</td>
                                <td>
                                    <a title="Edit" href="{{URL::route('HRM.sub_training.edit',['sub_training'=>$training->id])}}" class="btn btn-primary btn-xs">
                                        <i class="fa fa-edit"></i>&nbsp;Edit
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="bg-warning">No Data Available</td>
                            </tr>
                        @endforelse
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection