@extends('template.master')
@section('title','Edit Job Circular')
@section('breadcrumb')
    {!! Breadcrumbs::render('edit_job_circular') !!}
@endsection
@section('content')

    <section class="content" >
        <div class="box box-solid">
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-8 col-centered">
                        @include('recruitment::exam_center.form',['data'=>$data])
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection