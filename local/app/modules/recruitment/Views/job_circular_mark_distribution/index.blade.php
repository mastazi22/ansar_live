@extends('template.master')
@section('title','Mark Distribution')
@section('small_title')
    <a href="{{URL::route('recruitment.mark_distribution.create')}}" class="btn btn-primary btn-sm"><i
                class="fa fa-clipboard"></i>&nbsp;Add New Mark Distribution</a>
@endsection
@section('breadcrumb')
    {!! Breadcrumbs::render('job_circular') !!}
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
                                <th>Physical</th>
                                <th>Education & Training</th>
                                <th>Written</th>
                                <th>Viva</th>
                                <th>Action</th>
                            </tr>
                            <?php $i=1;?>
                            @forelse($mark_distributions as $mark_distribution)
                                <tr>
                                    <td>{{$i++}}</td>
                                    <td>{{$mark_distribution->circular->circular_name}}</td>
                                    <td>{{$mark_distribution->physical or '--'}}</td>
                                    <td>{{$mark_distribution->edu_training or '--'}}</td>
                                    <td>{{$mark_distribution->written or '--'}}</td>
                                    <td>{{$mark_distribution->viva or '--'}}</td>
                                    <td>
                                        <a class="btn btn-primary btn-xs" href="{{URL::route('recruitment.mark_distribution.edit',['id'=>$mark_distribution->id])}}">
                                            <i class="fa fa-edit"></i>&nbsp;Edit
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="warning" colspan="7">No mark distribution available</td>
                                </tr>
                            @endforelse
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
@stop