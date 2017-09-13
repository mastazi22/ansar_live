@extends('template.master')
@section('title','Edit System Setting')
@section('breadcrumb')
    {!! Breadcrumbs::render('system_setting_edit') !!}
@endsection
@section('content')

    <div>
        <section class="content">
            <notify></notify>
            <div class="box box-solid">
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-6 col-centered">
                            @include('HRM::Partial_view.form_transfer_policy',['data'=>$data,'units'=>$units])
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    @endsection