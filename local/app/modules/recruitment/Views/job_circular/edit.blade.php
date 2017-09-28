@extends('template.master')
@section('title','Edit Job Category')
@section('breadcrumb')
    {!! Breadcrumbs::render('edit_job_category') !!}
@endsection
@section('content')

    <section class="content" >
        <div class="box box-solid">
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-6 col-centered">
                        @include('recruitment::job_category.form',['data'=>$data])
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection