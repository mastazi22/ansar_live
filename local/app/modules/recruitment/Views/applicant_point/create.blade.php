@extends('template.master')
@section('title','New Applicant Mark Rules')
@section('breadcrumb')
    {!! Breadcrumbs::render('recruitment.point.index') !!}
@endsection
@section('content')

    <section class="content" >
        <div class="box box-solid">
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-6 col-centered">
                        @include('recruitment::applicant_point.form',compact('circulars','rules_name','rules_for'))
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endsection