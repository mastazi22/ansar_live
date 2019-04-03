@extends('template.master')
@section('title','Training Date')
@section('small_title')
    <a href="{{URL::route('recruitment.training.create')}}" class="btn btn-primary btn-sm"><i
                class="fa fa-clipboard"></i>&nbsp;Add New Training Date</a>
@endsection
@section('breadcrumb')
    {!! Breadcrumbs::render('recruitment.setting.hrm_training_date') !!}
@endsection
@section('content')
    <div>
        <section class="content">
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
            <div class="box box-solid">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h4 class="text text-bold">All Job Circular Mark Distribution</h4>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>SL. No</th>
                                <th>Job Circular Title</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Action</th>
                            </tr>
                            <?php $i=1;?>
                            @forelse($mark_distributions as $mark_distribution)
                                <tr>
                                    <td>{{$i++}}</td>
                                    <td>{{$mark_distribution->circular->circular_name}}</td>
                                    <td>{{$mark_distribution->start_date}}</td>
                                    <td>{{$mark_distribution->end_date}}</td>
                                    <td>
                                        <a class="btn btn-primary btn-xs" href="{{URL::route('recruitment.training.edit',['id'=>$mark_distribution->id])}}">
                                            <i class="fa fa-edit"></i>&nbsp;Edit
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="warning" colspan="7">No Training Date</td>
                                </tr>
                            @endforelse
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
@stop