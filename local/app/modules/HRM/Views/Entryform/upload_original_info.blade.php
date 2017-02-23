@extends('template.master')
@section('title','Upload Original Info')
@section('breadcrumb')
    {!! Breadcrumbs::render('upload_photo_original') !!}
@endsection
@section('content')
    <section class="content">
        <div class="box box-solid">
            <div class="box-header">
                <h3 class="box-title">Upload front side</h3>
            </div>
            <div class="box-body">
                <div id="upload_front">
                </div>
                <input type="file" name="original_front" multiple id="original_front" style="visibility: hidden;position: absolute">
            </div>
        </div>
        <div class="box box-solid">
            <div class="box-header">
                <h3 class="box-title">Upload back side</h3>
            </div>
            <div class="box-body">
                <div id="upload_back">
                </div>
                <input type="file" name="original_front" multiple id="original_back" style="visibility: hidden;position: absolute">
            </div>
        </div>
    </section>
    <script>
        $(document).ready(function () {

            $("#upload_front").on('click', function (e) {
                $("#original_front").trigger('click');
            })
            $("#upload_back").on('click', function (e) {
                $("#original_back").trigger('click');
            })
            var files = [];
            var processQueue = false;
            var index = 0;
            var sfiles = [];
            var sprocessQueue = false;
            var sindex = 0;
            $("#original_front").on('change', function () {
                if (files.length == 0) $("#upload_front").html('')
                for (var i = 0; i < this.files.length; i++) {
                    files.push(this.files[i])
                    var html = '<div class="card">' +
                            '<div class="image-title">' + this.files[i].name + '<span class="status">Queuing...</span></div>' +
                            '<div class="progress-container">' +
                            '<div class="progress"></div>' +
                            '</div>' +
                            '</div>'
                    $("#upload_front").append(html)
                }
                if (processQueue == false) {
                    processQueue = true;
                    index = 0;
                    uploadOriginalFront();

                }
            })
            $("#original_back").on('change', function () {
                if (sfiles.length == 0) $("#upload_back").html('')
                for (var i = 0; i < this.files.length; i++) {
                    sfiles.push(this.files[i])
                    var html = '<div class="card">' +
                            '<div class="image-title">' + this.files[i].name + '<span class="status">Queuing...</span></div>' +
                            '<div class="progress-container">' +
                            '<div class="progress"></div>' +
                            '</div>' +
                            '</div>'
                    $("#upload_back").append(html)
                }
                if (sprocessQueue == false) {
                    sprocessQueue = true;
                    sindex = 0;
                    uploadOriginalBack();

                }
            })

            function uploadOriginalFront() {
                var file = files.shift();
                if (file == undefined) {
                    processQueue = false;
                    return;
                }
                var fd = new FormData();
                fd.append("file", file);
//                console.log($("#original_front").find(".card").eq(index).html());
                $("#upload_front").animate({
                    scrollTop:$("#upload_front").scrollTop()+$("#upload_front .card").eq(index).offset().top-$("#upload_front").offset().top
                },100)
                $("#upload_front .card").eq(index).find(".status").text("Processing...").addClass('text text-warning')
                $.ajax({
                    url: '/HRM/upload/original_front/store',
                    data: fd,
                    type: 'post',
                    cache: false,
                    contentType: false,
                    processData: false,
                    xhr: function () {
                        var xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener('progress', function (evt) {
                            if (evt.lengthComputable) {
                                var percent = evt.loaded / evt.total;
                                $("#upload_front .card").eq(index).find(".progress").css({
                                    width: (percent * 100) + "%"
                                })
                            }
                        }, false)
                        return xhr;
                    },
                    success: function (response) {
                        $("#upload_front .card").eq(index).find(".status").text("Complete").removeClass('text text-warning').addClass('text text-success')
                        index++;
                        setTimeout(function () {
                            uploadOriginalFront()
                        }, 2000);
                    },
                    error: function (response) {
                        console.log(response)
                        $("#upload_front .card").eq(index).find(".status").text("Error").removeClass('text text-warning text-success').addClass('text text-danger')
                        index++;
                        setTimeout(function () {
                            uploadOriginalFront()
                        }, 2000);
                    }
                })
            }

            function uploadOriginalBack() {
                var file = sfiles.shift();
                if (file == undefined) {
                    sprocessQueue = false;
                    return;
                }
                var fd = new FormData();
                fd.append("file", file);
                $("#upload_back").animate({
                    scrollTop:$("#upload_back").scrollTop()+$("#upload_back .card").eq(sindex).offset().top-$("#upload_back").offset().top
                },100)
                console.log($("#upload_back").scrollTop()+$("#upload_back .card").eq(sindex).offset().top-$("#upload_back").offset().top)
                $("#upload_back .card").eq(sindex).find(".status").text("Processing...").addClass('text text-warning')
                $.ajax({
                    url: '/HRM/upload/original_back/store',
                    data: fd,
                    type: 'post',
                    cache: false,
                    contentType: false,
                    processData: false,
                    xhr: function () {
                        var xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener('progress', function (evt) {
                            if (evt.lengthComputable) {
                                var percent =  evt.loaded/ evt.total;
                                console.log(evt.loaded+" "+evt.total)
                                $("#upload_back .card").eq(sindex).find(".progress").css({
                                    width: (percent * 100) + "%"
                                })
                            }
                        }, false)
                        return xhr;
                    },
                    success: function (response) {
                        $("#upload_back .card").eq(sindex).find(".status").text("Complete").removeClass('text text-warning').addClass('text text-success')
                        sindex++;
                        setTimeout(function () {
                            uploadOriginalBack()
                        }, 2000);
                    },
                    error: function (response) {
                        console.log(response)
                        $("#upload_back .card").eq(sindex).find(".status").text("Error").removeClass('text text-warning text-success').addClass('text text-danger')
                        sindex++;
                        setTimeout(function () {
                            uploadOriginalBack()
                        }, 2000);
                    }
                })
            }
        })
    </script>
@stop