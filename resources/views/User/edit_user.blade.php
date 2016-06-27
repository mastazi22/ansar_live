@extends('template.master')
@section('content')
    <script>
        $(document).ready(function () {
            $("#user-name-form").ajaxForm({
                beforeSubmit: function (data) {
                    $("#user-name-form .submit").slideUp(100)
                    $("#user-name-form .submitting").slideDown(100)
                },
                success:function(response){
                    $("#user-name-form .submit").slideDown(100)
                    $("#user-name-form .submitting").slideUp(100)
                    console.log(response)
                    if(response.validation){
                        $("#user-name-form p").css('display','block')
                    }
                    else if(response.submit){
                        $('body').notifyDialog({type:'success',message:'User name change successfully'}).showDialog()
                        $("#user-name-form p").css('display','none')
                    }
                    else{
                        $('body').notifyDialog({type:'error',message:'An error occur.Please try again later'}).showDialog()
                        $("#user-name-form p").css('display','none')
                    }
                },
                error:function(response,statusText){
                    $("#user-name-form .submit").slideDown(100)
                    $("#user-name-form .submitting").slideUp(100)
                    $('body').notifyDialog({type:'error',message:'An server error occur.ERROR CODE:'+statusText}).showDialog()
                }

            })
            $("#user-password-form").ajaxForm({
                beforeSubmit: function (data) {
                    $("#user-password-form .submit").slideUp(100)
                    $("#user-password-form .submitting").slideDown(100)
                },
                success:function(response){
                    $("#user-password-form .submit").slideDown(100)
                    $("#user-password-form .submitting").slideUp(100)
                    console.log(response)
                    if(response.validation){
                        $("#user-password-form p").css('display','none')
                        if(response.error.password!=undefined){
                            $("#user-password-form p:eq(0) span").text(response.error.password)
                            $("#user-password-form p:eq(0)").css('display','block')
                        }
                        if(response.error.c_password!=undefined){
                            $("#user-password-form p:eq(1) span").text(response.error.c_password)
                            $("#user-password-form p:eq(1)").css('display','block')
                        }
                    }
                    else if(response.submit){
                        $('body').notifyDialog({type:'success',message:'User password change successfully'}).showDialog()
                        $("#user-password-form p").css('display','none')
                    }
                    else{
                        $('body').notifyDialog({type:'error',message:'An error occur.Please try again later'}).showDialog()
                        $("#user-password-form p").css('display','none')
                    }
                },
                error:function(response,statusText){
                    $("#user-password-form .submit").slideDown(100)
                    $("#user-password-form .submitting").slideUp(100)
                    $('body').notifyDialog({type:'error',message:'An server error occur.ERROR CODE:'+statusText}).showDialog()
                }

            })
        })
    </script>
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a>Edit User</a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active">
                    <div class="box box-solid">
                        <div class="row">
                            <div class="col-sm-4 col-sm-offset-4">
                                <h4 style="border-bottom: 1px solid #ababab">Change user name</h4>
                                <form id="user-name-form" action="{{action('UserController@changeUserName')}}" method="post">
                                    <input type="hidden" name="user_id"  value="{{$id}}">
                                    <div class="form-group has-feedback">
                                        <input type="text" name="user_name" value="" class="form-control" placeholder="user name"/>
                                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                        <p style="display: none" class="alert-danger-custom"><i class="fa fa-warning"></i><span>This user name already exists</span></p>
                                    </div>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <div class="submit">
                                                Change
                                            </div>
                                            <div class="submitting">
                                                <i class="fa fa-spinner fa-spin"></i><span class="blink-animation">Changing...</span>
                                            </div>
                                        </button>
                                        <div class="clearfix"></div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="row" >
                            <div class="col-sm-4 col-sm-offset-4" style="margin-bottom: 20px">
                                <h4 style="border-bottom: 1px solid #ababab">Change user password</h4>
                                <form id="user-password-form" action="{{action('UserController@changeUserPassword')}}" method="post">
                                    <input type="hidden" name="user_id" value="{{$id}}">
                                    <div class="form-group has-feedback">
                                        <input type="password" name="password" value="" class="form-control" placeholder="Enter password"/>
                                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                                        <p style="display: none" class="alert-danger-custom"><i class="fa fa-warning"></i><span>Password mis-match</span></p>
                                    </div>
                                    <div class="form-group has-feedback">
                                        <input type="password" name="c_password" value="" class="form-control" placeholder="Type password again"/>
                                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                                        <p style="display: none" class="alert-danger-custom"><i class="fa fa-warning"></i><span>Password mis-match</span></p>
                                    </div>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <div class="submit">
                                                Change
                                            </div>
                                            <div class="submitting">
                                                <i class="fa fa-spinner fa-spin"></i><span class="blink-animation">Changing...</span>
                                            </div>
                                        </button>
                                        <div class="clearfix"></div>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop