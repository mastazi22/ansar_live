@extends('template.master')
@section('title','Upload Bank Info')
@section('breadcrumb')
    {{--    {!! Breadcrumbs::render('upload_photo_original') !!}--}}
@endsection
@section('content')
    <section class="content">
        <div class="box box-solid">
            <div class="box-header">
                <h3 class="box-title">Upload File</h3>
            </div>
            <div class="box-body">
                @if(Session::has("success"))
                    <div class="alert alert-success" role="alert">
                        {{Session::get("success")}}
                    </div>
                @endif
                <p style="text-align: right">
                    <a class="btn btn-primary"
                       href="{{asset('sample_data_file/default_format.xls')}}">Download default format</a>
                </p>
                <form id="bulk_bank_account_info_form" method="post" class="form"
                      action="{{ URL::to("HRM/bulk-upload-bank-info") }}" enctype="multipart/form-data">
                    {!! csrf_field() !!}
                    <input type="submit" name="Upload" class="btn btn-primary" style="margin-bottom: 1%;">
                    <input type="file" name="bulk_bank_account_info" id="bulk_bank_account_info">
                    @if(isset($errors)&&$errors->first('bulk_bank_account_info'))
                        <p class="text text-danger">{{$errors->first('bulk_bank_account_info')}}</p>
                    @endif
                </form>
            </div>
        </div>
    </section>
@stop