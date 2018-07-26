@extends('template.master')
@section('title','Edit KPI Info')
@section('breadcrumb')
    {!! Breadcrumbs::render('entry.list.edit') !!}
@endsection
@section('content')
    <section class="content">
        <div class="box box-solid">
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-10 col-sm-offset-1">
                        @include('AVURP::kpi.form',['url'=>URL::route('AVURP.kpi.update',['kpi'=>$id]),'id'=>$id])
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection