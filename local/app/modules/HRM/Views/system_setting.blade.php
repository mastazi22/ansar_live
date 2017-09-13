@extends('template.master')
@section('title','System Setting')
@section('breadcrumb')
    {!! Breadcrumbs::render('system_setting') !!}
@endsection
@section('content')

    <div>
        <section class="content">
            @if(Session::has('success'))
                <div class="alert alert-success">{{Session::get('success')}}</div>
            @elseif(Session::has('error'))
                <div class="alert alert-error">{{Session::get('error')}}</div>
            @endif
            <div class="box box-solid">
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Setting Name</th>
                                        <th>Setting Value</th>
                                        <th>Setting Description</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                    @if(count($data))
                                        @foreach($data as $d)
                                            <tr>
                                                <td>{{$d->setting_name}}</td>
                                                <td>{!! $d->getValueAsString() !!}</td>
                                                <td>{{$d->description}}</td>
                                                <td>{{$d->active?"active":"inactive"}}</td>
                                                <td>
                                                    <a href="{{URL::route('system_setting_edit',['id'=>$d->id])}}"
                                                       class="btn btn-primary btn-xs">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td class="bg bg-warning" colspan="5">No data available</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection