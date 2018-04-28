@extends('template.master')
@section('title','User Permission')
@section('breadcrumb')
    {!! Breadcrumbs::render('user_permission',$id) !!}
@endsection
@section('content')
    <script>
        $(document).ready(function (e) {
            var pLength = $("input[type='checkbox']").length - 1;

            $('body').on('click', '.toggle-view', function (e) {
                e.preventDefault();
                $(this).children('img').toggleClass('rotate-img-up rotate-img-down')
                $($(this).parents('div')[0]).siblings('.p_continer').slideToggle(300)
            })
            checkLength();
            $("input[type='checkbox']:not(#all)").on('change', function () {
                checkLength();
            })
            $("#all").on('change', function () {
                if(this.checked) {
                    $("input[type='checkbox']:not(#all)").each(function () {
                        $(this).prop('checked', true);
                    })
                }
                else{
                    $("input[type='checkbox']:not(#all)").each(function () {
                        $(this).prop('checked', false);
                    })
                }
            })
            function checkLength(){
                var checked = $("input[type='checkbox']:not(#all):checked").length;
                if(checked==pLength) $("#all").prop('checked',true);
                else $("#all").prop('checked',false);
            }
        })
    </script>
    <div>
        <form action="{{action('UserController@updatePermission',['id'=>$id])}}" method="post">
            {{csrf_field()}}
            <section class="content">
                <div class="box box-solid">
                    <div class="box-header">
                        <p>Edit permission of : <strong>{{$user->user_name}}({{$user->usertype->type_name}})</strong></p>
                        <label class="control-label">
                            Grant All Permission &nbsp;
                            <div class="styled-checkbox">
                                {!! Form::checkBox('permit_all','permit_all',null,['id'=>'all']) !!}
                                <label for="all"></label>
                            </div>

                        </label>
                        <button type="submit" class="btn btn-primary pull-right">
                            <i class="fa fa-save"></i> Save Permission
                        </button>
                    </div>
                    <div class="box-body">
                        <div class="row" style="">
                            @for($j=0;$j<3;$j++)
                            <div class="col-lg-4">
                                @for($i=$j;$i<count($routes);$i+=3)
                                    <div style="margin-top: 5px">
                                        <div class="legend">
                                            {{$routes[$i]->root}}
                                            <button class="btn btn-default btn-xs pull-right toggle-view">
                                                <img src="{{asset('dist/img/down_icon.png')}}" class="rotate-img-down"
                                                     style="width: 18px;height: 20px;">
                                            </button>
                                        </div>
                                        <div class="box-body p_continer" style="background-color: #FFFFFF;display: none">
                                            <ul class="permission-list">
                                                @include('User.permission_partial',['data'=>$routes[$i]->children])
                                            </ul>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                            @endfor

                        </div>
                    </div>
                </div>
            </section>
        </form>
    </div>
    <script>
        $(document).ready(function () {
            $('body').on('click', ".tree-view", function (e) {
                e.preventDefault();
                var i = $(this).attr('data-open');
                if (parseInt(i)) {
                    $(this).children('i').addClass('fa-minus').removeClass('fa-plus')
                    $(this).parents('span').siblings('ul').slideToggle(200);
                    $(this).attr('data-open', 0)
                }
                else {
                    $(this).children('i').addClass('fa-plus').removeClass('fa-minus')
                    $(this).parents('span').siblings('ul').slideToggle(200);
                    $(this).attr('data-open', 1)
                }
            })
        })
    </script>
@stop