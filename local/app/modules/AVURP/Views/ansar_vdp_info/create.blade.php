@extends('template.master')
@section('title','Add New Entry')
@section('breadcrumb')
    {!! Breadcrumbs::render('recruitment') !!}
@endsection
@section('content')
    <section class="content">
        <div class="box box-solid">
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-10 col-sm-offset-1">
                        @include('AVURP::ansar_vdp_info.form',['url'=>URL::route('AVURP.info.store')])
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection