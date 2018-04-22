@extends('template.master')
@section('title','User Request List')
@section('breadcrumb')
    {!! Breadcrumbs::render('entry.list') !!}
@endsection
@section('content')
    <section class="content">
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
                <div class="table-responsive">
                    <table class="table table-condensed table-bordered">
                        <caption>
                            <span style="font-size: 1.5em;font-weight: bold;color: #000;">
                                Total: {{count($user_create_requests)}}
                            </span>
                            <a href="{{URL::route('user_create_request.create')}}" class="btn btn-primary pull-right">
                                <i class="fa fa-user"></i>&nbsp;Create user request
                            </a>
                        </caption>
                        <tr>
                            <th>SL. No</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Mobile No.</th>
                            <th>User Type</th>
                            <th>Status</th>
                        </tr>
                        <?php $i=1;?>
                        @forelse($user_create_requests as $user_create_request)
                            <tr>
                                <td>{{$i++}}</td>
                                <td>{{$user_create_request->first_name}}</td>
                                <td>{{$user_create_request->last_name}}</td>
                                <td>{{$user_create_request->email}}</td>
                                <td>{{$user_create_request->mobile_no}}</td>
                                <td>{{ucfirst($user_create_request->user_type)}}</td>
                                <td>
                                    @if($user_create_request->status=='pending')
                                        <span class="label label-warning">Pending</span>
                                    @elseif($user_create_request->status=='canceled')
                                        <span class="label label-danger">Canceled</span>
                                    @else
                                        <span class="label label-success">Approved</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="bg-warning">No request available</td>
                            </tr>
                        @endforelse
                    </table>
                </div>
            </div>
        </div>
    </section>

@endsection