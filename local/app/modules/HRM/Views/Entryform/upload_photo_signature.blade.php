@extends('template.master')
@section('title','Upload Photo & Signature')
@section('breadcrumb')
    {!! Breadcrumbs::render('upload_photo_signature') !!}
@endsection
@section('content')
    <style>

    </style>
    <section class="content">
        <div class="box box-solid">
            <div class="box-header">
                <h3 class="box-title">Upload photo</h3>
            </div>
            <div class="box-body">
                <div id="upload_photo" class="dropzone">
                </div>
            </div>
        </div>
        <div class="box box-solid">
            <div class="box-header">
                <h3 class="box-title">Upload signature</h3>
            </div>
            <div class="box-body">
                <div id="upload_signature" class="dropzone">
                </div>
            </div>
        </div>
    </section>
    <script>
        $(document).ready(function(){
            $("#upload_photo").dropzone({
                url:"{{URL::route('photo_store')}}",
                uploadMultiple:false,
                parallelUploads:1,

                acceptedFiles:'image/jpg,image/jpeg',
                headers: {
                    'X-CSRF-TOKEN': '{{csrf_token()}}'
                }
            })
            $("#upload_signature").dropzone({
                url:"{{URL::route('signature_store')}}",
                uploadMultiple:false,
                parallelUploads:1,

                acceptedFiles:'image/jpg,image/jpeg',
                headers: {
                    'X-CSRF-TOKEN': '{{csrf_token()}}'
                }
            })
        })
    </script>
@stop