@extends('template.master')
@section('content')
    <script>
        $(document).ready(function () {
            var v = "{{$user->userProfile->bank_name}}";
            $("select[name='bank_name']").children('option').each(function () {
                if ($(this).attr('value') == v) {
                    $(this).attr('selected', true)
                }
            })
            @if(auth()->user()->type==11)
            $("#user-name-form").ajaxForm({
                beforeSubmit: function (data) {
                    $("#user-name-form .submit").slideUp(100)
                    $("#user-name-form .submitting").slideDown(100)
                },
                success: function (response) {
                    $("#user-name-form .submit").slideDown(100)
                    $("#user-name-form .submitting").slideUp(100)
                    console.log(response)
                    if (response.validation) {
                        $("#user-name-form p").css('display', 'block')
                    }
                    else if (response.submit) {
                        $('body').notifyDialog({
                            type: 'success',
                            message: 'User name changed successfully'
                        }).showDialog()
                        $("#user-name-form p").css('display', 'none')
                    }
                    else {
                        $('body').notifyDialog({
                            type: 'error',
                            message: 'An error occured.Please try again later'
                        }).showDialog()
                        $("#user-name-form p").css('display', 'none')
                    }
                },
                error: function (response, statusText) {
                    $("#user-name-form .submit").slideDown(100)
                    $("#user-name-form .submitting").slideUp(100)
                    $('body').notifyDialog({
                        type: 'error',
                        message: 'An server error occured.ERROR CODE:' + statusText
                    }).showDialog()
                }

            })
            @endif
            $("#user-password-form").ajaxForm({
                beforeSubmit: function (data) {
                    $("#user-password-form .submit").slideUp(100)
                    $("#user-password-form .submitting").slideDown(100)
                },
                success: function (response) {
                    $("#user-password-form .submit").slideDown(100)
                    $("#user-password-form .submitting").slideUp(100)
                    console.log(response)
                    if (response.validation) {
                        $("#user-password-form p").css('display', 'none')
                        if (response.error.old_password != undefined) {
                            $("#user-password-form p:eq(0) span").text(response.error.old_password)
                            $("#user-password-form p:eq(0)").css('display', 'block')
                        }
                        if (response.error.password != undefined) {
                            $("#user-password-form p:eq(1) span").text(response.error.password)
                            $("#user-password-form p:eq(1)").css('display', 'block')
                        }
                        if (response.error.c_password != undefined) {
                            $("#user-password-form p:eq(2) span").text(response.error.c_password)
                            $("#user-password-form p:eq(2)").css('display', 'block')
                        }
                    }
                    else if (response.submit) {
                        $('body').notifyDialog({
                            type: 'success',
                            message: 'User password changed successfully'
                        }).showDialog()
                        $("#user-password-form p").css('display', 'none')
                    }
                    else {
                        $('body').notifyDialog({
                            type: 'error',
                            message: 'An error occur. Please try again later'
                        }).showDialog()
                        $("#user-password-form p").css('display', 'none')
                    }
                },
                error: function (response, statusText) {
                    $("#user-password-form .submit").slideDown(100)
                    $("#user-password-form .submitting").slideUp(100)
                    $('body').notifyDialog({
                        type: 'error',
                        message: 'An server error occur. ERROR CODE:' + statusText
                    }).showDialog()
                }

            })
            $("#user_profile_form").ajaxForm({
                beforeSubmit: function (data) {
                    $("#user_profile_form .submit").slideUp(100)
                    $("#user_profile_form .submitting").slideDown(100)
                },
                success: function (response) {
                    $("#user_profile_form .submit").slideDown(100)
                    $("#user_profile_form .submitting").slideUp(100)
                    console.log(response)
                    if (response.submit) {
                        $('body').notifyDialog({type: 'success', message: 'Profile Updated'}).showDialog()
                        window.location.assign("{{\Illuminate\Support\Facades\URL::previous()}}")
                    }
                    else {
                        $('body').notifyDialog({
                            type: 'error',
                            message: 'An error occured. Please try again later'
                        }).showDialog()

                    }
                },
                error: function (response, statusText) {
                    $("#user_profile_form .submit").slideDown(100)
                    $("#user_profile_form .submitting").slideUp(100)
                    $('body').notifyDialog({
                        type: 'error',
                        message: 'An server error occured. ERROR CODE:' + statusText
                    }).showDialog()
                }

            })
            $("#save_image").on('click', function (e) {
                e.preventDefault();
                $("#profile_pic").ajaxSubmit({
                    beforeSubmit: function (data) {
                        $("#pppppp").css('display', 'block')
                        console.log(data)
                    },
                    success: function (r) {
                        if (r.status) {
                            noty({
                                type: 'success',
                                text: r.message,
                                layout: 'top',
                                maxVisible: 5,
                                timeout: 5000,
                                dismissQueue: true
                            })
                        }
                        else {
                            noty({
                                type: 'error',
                                text: r.message,
                                layout: 'top',
                                maxVisible: 5,
                                timeout: 5000,
                                dismissQueue: true
                            })
                        }
                        $("#pppppp").css('display', 'none')
                        $("#pppppp").css('width', 0 + '%').attr('aria-valuenow', 0);
//                        console.log(r)
                    },
                    uploadProgress: function (event, position, total, totalPercentage) {
                        $("#pppppp").text(totalPercentage + "%");
                        $("#pppppp").css('width', totalPercentage + '%').attr('aria-valuenow', totalPercentage);
                        console.log({position: totalPercentage})
                    }
                })
            })
            $("#change_picture").on('click', function (e) {

                e.preventDefault();
                $("#profile_pic_chose").trigger('click')
            })
            $("#profile_pic_chose").change(function (e) {
                var src = $(this).val();
                var i = src.lastIndexOf('\\');
                //alert(i)
                $("#img_src").text(src.substring(i + 1, src.length))
                var file = e.target.files[0]
                var f = new FileReader()
                f.onload = function () {
                    var content = f.result;
                    $("#p_image").attr('src', content)

                }
                f.readAsDataURL(file)
            })
        })
    </script>
    <div>
        <section class="content">
            <div class="box box-solid">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a data-toggle="tab" href="#personal-info">Personal Information</a>
                        </li>
                        <li>
                            <a data-toggle="tab" href="#user-name-password">Change user name/password</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div id="personal-info" class="tab-pane active">
                            <div class="row">
                                <div class="col-sm-6 col-sm-offset-1">
                                    <form id="user_profile_form" action="{{action('UserController@updateProfile')}}"
                                          method="post" class="form-horizontal">
                                        {{csrf_field()}}
                                        <div class="form-group has-feedback">
                                            <label for="first_name" class="col-sm-3 control-label"
                                                   style="text-align: left;padding-top:0">First Name</label>

                                            <div class="col-sm-9">
                                                <input type="text" name="first_name"
                                                       value="{{$user->userProfile->first_name}}"
                                                       class="form-control" placeholder="FIrst Name"/>
                                                <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                            </div>
                                        </div>
                                        <div class="form-group has-feedback">
                                            <label for="last_name" class="col-sm-3 control-label"
                                                   style="text-align: left;padding-top:0">Last Name</label>

                                            <div class="col-sm-9">
                                                <input type="text" name="last_name"
                                                       value="{{$user->userProfile->last_name}}"
                                                       class="form-control" placeholder="Last Name"/>
                                                <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                            </div>
                                        </div>
                                        <div class="form-group has-feedback">
                                            <label for="email" class="col-sm-3 control-label"
                                                   style="text-align: left;padding-top:0">Email</label>

                                            <div class="col-sm-9">
                                                <input type="text" name="email" value="{{$user->userProfile->email}}"
                                                       class="form-control" placeholder="Email"/>
                                                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                                            </div>
                                        </div>
                                        <div class="form-group has-feedback">
                                            <label for="office_phone_no" class="col-sm-3 control-label"
                                                   style="text-align: left;padding-top:0">Office Phone No.</label>

                                            <div class="col-sm-9">
                                                <input type="text" name="office_phone_no"
                                                       value="{{$user->userProfile->office_phone_no}}"
                                                       class="form-control" placeholder="Office Phone No"/>
                                                <span class="glyphicon glyphicon-phone form-control-feedback"></span>
                                            </div>
                                        </div>
                                        <div class="form-group has-feedback">
                                            <label for="mobile_no" class="col-sm-3 control-label"
                                                   style="text-align: left;padding-top:0">Mobile No.</label>

                                            <div class="col-sm-9">
                                                <input type="text" name="mobile_no"
                                                       value="{{$user->userProfile->mobile_no}}"
                                                       class="form-control" placeholder="Mobile No"/>
                                                <span class="glyphicon glyphicon-phone form-control-feedback"></span>
                                            </div>
                                        </div>
                                        <div class="form-group has-feedback">
                                            <label for="contact_address" class="col-sm-3 control-label"
                                                   style="text-align: left;padding-top:0">Contact Address</label>

                                            <div class="col-sm-9">
                                                <input type="text" name="contact_address"
                                                       value="{{$user->userProfile->contact_address}}"
                                                       class="form-control" placeholder="Contact Address"/>
                                                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                                            </div>
                                        </div>
                                        <div class="form-group has-feedback">
                                            <label for="rank" class="col-sm-3 control-label"
                                                   style="text-align: left;padding-top:0">Rank</label>

                                            <div class="col-sm-9">
                                                <input type="text" name="rank" value="{{$user->userProfile->rank}}"
                                                       class="form-control" placeholder="Rank"/>
                                                <span class="glyphicon glyphicon-star form-control-feedback"></span>
                                            </div>
                                        </div>
                                        <div class="form-group has-feedback">
                                            <label for="rank" class="col-sm-3 control-label"
                                                   style="text-align: left;padding-top:0">Rank</label>

                                            <div class="col-sm-9">
                                                <input type="text" name="rank" value="{{$user->userProfile->rank}}"
                                                       class="form-control" placeholder="Rank"/>
                                                <span class="glyphicon glyphicon-star form-control-feedback"></span>
                                            </div>
                                        </div>
                                        {{--<p class="text text-danger " style="font-size: 20px">You only edit your bank account one time</p>--}}
                                        @if(!$user->userProfile->bank_name)
                                        <div class="form-group has-feedback">
                                            <label for="rank" class="col-sm-3 control-label"
                                                   style="text-align: left;padding-top:0">Bank Name</label>

                                            <div class="col-sm-9">
                                                <select name="bank_name" value="{{$user->userProfile->bank_name}}"
                                                        class="form-control">
                                                    <option value="">Select a bank</option>
                                                    <option value="AB Bank Limited">AB Bank Limited</option>
                                                    <option value="Agrani Bank Limited">Agrani Bank Limited</option>
                                                    <option value="Al-Arafah Islami Bank Limited">Al-Arafah Islami Bank
                                                        Limited
                                                    </option>
                                                    <option value="Bangladesh Commerce Bank Limited">Bangladesh Commerce
                                                        Bank Limited
                                                    </option>
                                                    <option value="Bangladesh Development Bank Limited">Bangladesh
                                                        Development Bank Limited
                                                    </option>
                                                    <option value="Bangladesh Krishi Bank">Bangladesh Krishi Bank
                                                    </option>
                                                    <option value="Bank Al-Falah Limited">Bank Al-Falah Limited</option>
                                                    <option value="Bank Asia Limited">Bank Asia Limited</option>
                                                    <option value="BASIC Bank Limited">BASIC Bank Limited</option>
                                                    <option value="BRAC Bank Limited">BRAC Bank Limited</option>
                                                    <option value="Citibank N.A">Citibank N.A</option>
                                                    <option value="Commercial Bank of Ceylon Limited">Commercial Bank of
                                                        Ceylon Limited
                                                    </option>
                                                    <option value="Dhaka Bank Limited">Dhaka Bank Limited</option>
                                                    <option value="Dutch-Bangla Bank Limited">Dutch-Bangla Bank
                                                        Limited
                                                    </option>
                                                    <option value="Eastern Bank Limited">Eastern Bank Limited</option>
                                                    <option value="EXIM Bank Limited">EXIM Bank Limited</option>
                                                    <option value="First Security Islami Bank Limited">
                                                        First Security Islami Bank Limited
                                                    </option>
                                                    <option value="Habib Bank Ltd.">Habib Bank Ltd.</option>
                                                    <option value="ICB Islamic Bank Ltd.">ICB Islamic Bank Ltd.</option>
                                                    <option value="IFIC Bank Limited">IFIC Bank Limited</option>
                                                    <option value="Islami Bank Bangladesh Ltd">Islami Bank Bangladesh
                                                        Ltd
                                                    </option>
                                                    <option value="Jamuna Bank Ltd">Jamuna Bank Ltd</option>
                                                    <option value="Janata Bank Limited">Janata Bank Limited</option>
                                                    <option value="Meghna Bank Limited">Meghna Bank Limited</option>
                                                    <option value="Mercantile Bank Limited">Mercantile Bank Limited
                                                    </option>
                                                    <option value="Midland Bank Limited">Midland Bank Limited</option>
                                                    <option value="Mutual Trust Bank Limited">Mutual Trust Bank
                                                        Limited
                                                    </option>
                                                    <option value="National Bank Limited">National Bank Limited</option>
                                                    <option value="National Bank of Pakistan">National Bank of
                                                        Pakistan
                                                    </option>
                                                    <option value="National Credit & Commerce Bank Ltd">National Credit
                                                        & Commerce Bank Ltd
                                                    </option>
                                                    <option value="NRB Commercial Bank Limited">NRB Commercial Bank
                                                        Limited
                                                    </option>
                                                    <option value="One Bank Limited">One Bank Limited</option>
                                                    <option value="Premier Bank Limited">Premier Bank Limited</option>
                                                    <option value="Prime Bank Ltd">Prime Bank Ltd</option>
                                                    <option value="Pubali Bank Limited">Pubali Bank Limited</option>
                                                    <option value="Rajshahi Krishi Unnayan Bank">Rajshahi Krishi Unnayan
                                                        Bank
                                                    </option>
                                                    <option value="Rupali Bank Limited">Rupali Bank Limited</option>
                                                    <option value="Shahjalal Bank Limited">Shahjalal Bank Limited
                                                    </option>
                                                    <option value="Shimanto Bank Limited">Shimanto Bank Limited</option>
                                                    <option value="Social Islami Bank Ltd.">Social Islami Bank Ltd.
                                                    </option>
                                                    <option value="Sonali Bank Limited">Sonali Bank Limited</option>
                                                    <option value="South Bangla Agriculture & Commerce Bank Limited">
                                                        South Bangla Agriculture & Commerce Bank Limited
                                                    </option>
                                                    <option value="Southeast Bank Limited">Southeast Bank Limited
                                                    </option>
                                                    <option value="Standard Bank Limited">Standard Bank Limited</option>
                                                    <option value="Standard Chartered Bank">Standard Chartered Bank
                                                    </option>
                                                    <option value="State Bank of India">State Bank of India</option>
                                                    <option value="The City Bank Ltd.">The City Bank Ltd.</option>
                                                    <option value="The Hong Kong and Shanghai Banking Corporation. Ltd.">
                                                        The Hong Kong and Shanghai Banking Corporation. Ltd.
                                                    </option>
                                                    <option value="Trust Bank Limited">Trust Bank Limited</option>
                                                    <option value="Union Bank Limited">Union Bank Limited</option>
                                                    <option value="United Commercial Bank Limited">United Commercial
                                                        Bank Limited
                                                    </option>
                                                    <option value="Uttara Bank Limited">Uttara Bank Limited</option>
                                                    <option value="Woori Bank">Woori Bank</option>
                                                </select>
                                                <span class="glyphicon glyphicon-star form-control-feedback"></span>
                                            </div>
                                        </div>
                                        @else
                                            <div class="form-group">
                                                <label for="rank" class="col-sm-3 control-label"
                                                       style="text-align: left;padding-top:0">Bank Name</label>

                                                <div class="col-sm-9">
                                                    <div class="form-control">
                                                        {{$user->userProfile->bank_name}}
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        @if(!$user->userProfile->branch_name)
                                        <div class="form-group has-feedback">
                                            <label for="rank" class="col-sm-3 control-label"
                                                   style="text-align: left;padding-top:0">Branch Name</label>

                                            <div class="col-sm-9">
                                                <input type="text" name="branch_name"
                                                       value="{{$user->userProfile->branch_name}}"
                                                       class="form-control" placeholder="branch_name"/>
                                                <span class="glyphicon glyphicon-star form-control-feedback"></span>
                                            </div>
                                        </div>
                                        @else
                                            <div class="form-group">
                                                <label for="rank" class="col-sm-3 control-label"
                                                       style="text-align: left;padding-top:0">Branch Name</label>

                                                <div class="col-sm-9">
                                                    <div class="form-control">
                                                        {{$user->userProfile->branch_name}}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @if(!$user->userProfile->bank_account_no)
                                        <div class="form-group has-feedback">
                                            <label for="rank" class="col-sm-3 control-label"
                                                   style="text-align: left;padding-top:0">Bank account no</label>

                                            <div class="col-sm-9">
                                                <input type="text" name="bank_account_no"
                                                       value="{{$user->userProfile->bank_account_no}}"
                                                       class="form-control" placeholder="Bank account no"/>
                                                <span class="glyphicon glyphicon-star form-control-feedback"></span>
                                            </div>
                                        </div>
                                        @else
                                            <div class="form-group">
                                                <label for="rank" class="col-sm-3 control-label"
                                                       style="text-align: left;padding-top:0">Bank Account No</label>

                                                <div class="col-sm-9">
                                                    <div class="form-control">
                                                        {{$user->userProfile->bank_account_no}}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="row">
                                            <div class="col-sm-4 col-sm-offset-8">
                                                <button type="submit" class="btn btn-primary btn-block btn-flat">
                                                    <div class="submit">
                                                        Update Profile
                                                    </div>
                                                    <div class="submitting">
                                                        <i class="fa fa-spinner fa-spin"></i><span
                                                                class="blink-animation">Updating...</span>
                                                    </div>
                                                </button>
                                            </div>
                                            <!-- /.col -->
                                        </div>
                                    </form>
                                </div>
                                <div class="col-sm-4">
                                    <div style="position: relative">
                                        <div class="progress">
                                            <div id="pppppp" style="display: none;"
                                                 class="progress-bar progress-bar-success progress-bar-striped active"
                                                 role="progressbar" aria-valuemin="0" aria-valuemax="100"
                                                 aria-valuenow="10" style="width: 100%">
                                                40%
                                            </div>
                                        </div>
                                        <img id="p_image"
                                             style="display: block;width: 200px;height: 200px;margin: 0 auto"
                                             class="img-responsive img-circle img-thumbnail"
                                             src="{{action('UserController@getImage',['file'=>auth()->user()->userProfile->profile_image])}}">
                                    </div>
                                    <div style="position: relative;padding: 10px">
                                        <form id="profile_pic" enctype="multipart/form-data"
                                              action="{{action('UserController@changeUserImage')}}" method="post">
                                            <div style="position: relative;border: 1px solid #0000C2;overflow: hidden">
                                                <button id="change_picture" class="btn btn-primary"
                                                        style="float: left;border-radius:0"> Change Picture
                                                </button>
                                                <p id="img_src"
                                                   style="float: left;padding: 5px 10px;display: block;margin: 0;box-sizing: border-box;width: 59%;overflow:hidden;text-overflow: ellipsis;white-space: nowrap"></p>

                                                <div class="clearfix"></div>
                                            </div>
                                            <button style="display: block;margin: 10px 0;width:100%;" type="submit"
                                                    class="btn btn-primary" id="save_image" class="btn btn-success">Save
                                            </button>
                                            <input type="file" name="image_file"
                                                   style="visibility: hidden;z-index: -1;position: absolute" ;
                                                   id="profile_pic_chose">
                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="user-name-password" class="tab-pane">
                            <div class="row">
                                @if(auth()->user()->type==11)
                                    <div class="col-sm-4 col-sm-offset-4">
                                        <h4 style="border-bottom: 1px solid #ababab">Change user name</h4>

                                        <form id="user-name-form" action="{{action('UserController@changeUserName')}}"
                                              method="post">
                                            <input type="hidden" name="user_id" value="{{$user->id}}">

                                            <div class="form-group has-feedback">
                                                <input type="text" name="user_name" value="" class="form-control"
                                                       placeholder="user name"/>
                                                <span class="glyphicon glyphicon-user form-control-feedback"></span>

                                                <p style="display: none" class="alert-danger-custom"><i
                                                            class="fa fa-warning"></i><span>This user name already exists</span>
                                                </p>
                                            </div>
                                            <div>
                                                <button type="submit" class="btn btn-primary">
                                                    <div class="submit">
                                                        Change
                                                    </div>
                                                    <div class="submitting">
                                                        <i class="fa fa-spinner fa-spin"></i><span
                                                                class="blink-animation">Changing...</span>
                                                    </div>
                                                </button>
                                                <div class="clearfix"></div>
                                            </div>
                                        </form>

                                    </div>
                                @endif
                            </div>
                            <div class="row">
                                <div class="col-sm-4 col-sm-offset-4" style="margin-bottom: 20px">
                                    <h4 style="border-bottom: 1px solid #ababab">Change user password</h4>

                                    <form id="user-password-form"
                                          action="{{action('UserController@changeUserPassword')}}" method="post">
                                        <input type="hidden" name="user_id" value="{{$user->id}}">

                                        <div class="form-group has-feedback">
                                            <input type="password" name="old_password" value="" class="form-control"
                                                   placeholder="Enter old password"/>
                                            <span class="glyphicon glyphicon-lock form-control-feedback"></span>

                                            <p style="display: none" class="alert-danger-custom"><i
                                                        class="fa fa-warning"></i><span>Password mis-match</span></p>
                                        </div>
                                        <div class="form-group has-feedback">
                                            <input type="password" name="password" value="" class="form-control"
                                                   placeholder="Enter new password"/>
                                            <span class="glyphicon glyphicon-lock form-control-feedback"></span>

                                            <p style="display: none" class="alert-danger-custom"><i
                                                        class="fa fa-warning"></i><span>Password mis-match</span></p>
                                        </div>
                                        <div class="form-group has-feedback">
                                            <input type="password" name="c_password" value="" class="form-control"
                                                   placeholder="Confirm new password"/>
                                            <span class="glyphicon glyphicon-lock form-control-feedback"></span>

                                            <p style="display: none" class="alert-danger-custom"><i
                                                        class="fa fa-warning"></i><span>Password mis-match</span></p>
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
    </div>

@stop