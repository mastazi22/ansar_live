@extends('template.master')
@section('title','Range Setting')
{{--@section('small_title','DG')--}}
@section('breadcrumb')
    {!! Breadcrumbs::render('range.index') !!}
@endsection
@section('content')
    @if(Session::has('success'))
        <div class="alert alert-success">
            <i class="fa fa-check"></i>&nbsp;{{Session::get('success')}}
        </div>
        @endif
    @if(Session::has('error'))
        <div class="alert alert-danger">
            <i class="fa fa-remove"></i>&nbsp;{{Session::get('error')}}
        </div>
    @endif
    <section class="content">
        <div class="box box-solid">
            <div class="box-header">
                <a href="{{URL::route('HRM.range.create')}}" title="New Range" class="btn btn-primary btn-sm pull-right">
                    <i class="fa fa-plus"></i>&nbsp;New Range
                </a>
                <h3 class="box-title">Total Range : {{count($data)}}</h3>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>#</th>
                            <th>Range Name ENG</th>
                            <th>Range Name BNG</th>
                            <th>Range Code</th>
                            <th>Action</th>
                        </tr>
                        <?php $i=0; ?>
                        @forelse($data as $range)
                            <tr>
                                <td>{{$i++}}</td>
                                <td>{{$range->division_name_eng}}</td>
                                <td>{{$range->division_name_bng}}</td>
                                <td>{{$range->division_code}}</td>
                                <td>
                                    <a title="Edit" href="{{URL::route('HRM.range.edit',['range'=>$range->id])}}" class="btn btn-primary btn-xs">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                        @endforelse
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection