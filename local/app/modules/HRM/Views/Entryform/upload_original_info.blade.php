@extends('template.master')
@section('title','Upload Original Info')
@section('breadcrumb')
    {!! Breadcrumbs::render('upload_photo_original') !!}
@endsection
@section('content')
    <style>

    </style>
    <section class="content">
        <div class="box box-solid">
            <div class="box-header">
                <h3 class="box-title">Upload front side</h3>
            </div>
            <div class="box-body">
                <div id="upload_front" class="dropzone">
                </div>
            </div>
        </div>
        <div class="box box-solid">
            <div class="box-header">
                <h3 class="box-title">Upload back side</h3>
            </div>
            <div class="box-body">
                <div id="upload_back" class="dropzone">
                </div>
            </div>
        </div>
    </section>
    <script>
        $(document).ready(function(){
            $("#upload_front").dropzone({
                url:"{{URL::route('original_front')}}",
                uploadMultiple:false,
                acceptedFiles:'image/jpg,image/jpeg',
                headers: {
                    'X-CSRF-TOKEN': '{{csrf_token()}}'
                }
            })
            $("#upload_back").dropzone({
                url:"{{URL::route('original_back')}}",
                uploadMultiple:false,
                acceptedFiles:'image/jpg,image/jpeg',
                headers: {
                    'X-CSRF-TOKEN': '{{csrf_token()}}'
                }
            })
        })
    </script>
@stop