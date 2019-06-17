{{--User: Shreya--}}
{{--Date: 12/14/2015--}}
{{--Time: 11:28 AM--}}

@extends('template.master')
@section('title','Upload Share Id')
{{--@section('small_title','DG')--}}
@section('breadcrumb')
    {!! Breadcrumbs::render('direct_black') !!}
@endsection
@section('content')

    <div>
        @if(Session::has('success_message'))
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-ok"></span> {{Session::get('success_message')}}
                </div>
            </div>
        @endif
        <section class="content" style="position: relative;" >
            <notify></notify>
            <div class="box box-solid">
                <form action="/HRM/test" method="post" enctype="multipart/form-data">
                    {!! csrf_field() !!}
                <div class="box-body">
                    <div class="form-group">
                        <label class="control-label">Upload File</label>
                        <input type="file" name="shareIdFile" class="form-control">
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary">Upload</button>
                    </div>
                </div>
                </form>
            </div>
        </section>
    </div>
@endsection