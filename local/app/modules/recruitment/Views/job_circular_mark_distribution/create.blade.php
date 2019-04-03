@extends('template.master')
@section('title','Create Mark Distribution')
@section('breadcrumb')
    {!! Breadcrumbs::render('recruitment') !!}
@endsection
@section('content')

    <section class="content" >
        <div class="box box-solid">
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-6 col-centered">
                        @include('recruitment::job_circular_mark_distribution.form',['circulars'=>$circulars])
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endsection