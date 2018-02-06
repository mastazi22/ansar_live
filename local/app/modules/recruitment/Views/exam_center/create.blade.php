@extends('template.master')
@section('title','Create Exam Center')
@section('breadcrumb')
    {!! Breadcrumbs::render('create_job_circular') !!}
@endsection
@section('content')

    <section class="content" >
        <div class="box box-solid">
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-6 col-centered">
                        @include('recruitment::exam_center.form')
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endsection