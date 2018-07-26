@extends('template.master')
@section('title','Add New KPI')
@section('breadcrumb')
    {!! Breadcrumbs::render('entry.list.entry') !!}
@endsection
@section('content')
    <section class="content">
        <div class="box box-solid">
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-8 col-centered">
                        @include('AVURP::kpi.form',['url'=>URL::route('AVURP.kpi.store')])
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection