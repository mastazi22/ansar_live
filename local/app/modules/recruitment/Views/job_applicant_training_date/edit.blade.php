@extends('template.master')
@section('title','Edit Training date')
@section('breadcrumb')
    {!! Breadcrumbs::render('edit_job_circular') !!}
@endsection
@section('content')

    <section class="content" >
        <div class="box box-solid">
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-6 col-centered">
                        @include('recruitment::job_applicant_training_date.form',['data'=>$data,'circulars'=>$circulars])
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection