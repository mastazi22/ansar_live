<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="{{asset('dist/css/user_css.css')}}">
    <link href="{{asset('bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('dist/css/AdminLTE.min.css')}}" rel="stylesheet" type="text/css"/>
</head>
<body>

@include('HRM::Letter.'.$view)
<script src="{{asset('plugins/jQuery/jQuery-2.1.4.min.js')}}" type="text/javascript"></script>
<script>

    $(function () {
        $(document).on('click','#print-report', function (e) {
            e.preventDefault();
            $('body').append('<div id="print-area" class="letter">'+$(".letter").html()+'</div>')
            window.print();
            $("#print-area").remove()
        })
    })
</script>
</body>
</html>