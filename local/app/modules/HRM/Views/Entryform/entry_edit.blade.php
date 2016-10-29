@extends('template.master')
@section('title','Edit Ansar Information')
@section('breadcrumb')
    {!! Breadcrumbs::render('entry_edit',$ansarAllDetails->ansar_id) !!}
    @endsection
@section('content')


    <script>
        $(document).ready(function () {
            $('#data_of_birth').datePicker(false);
            //$("#to_date").datePicker(true);

        })
        GlobalApp.controller('fullEntryFormController', function ($scope, getNameService, getBloodService, getDiseaseSkillService, $sce, $http) {
            $scope.isAdmin = parseInt('{{Auth::user()->type}}')
            $scope.SelectedDistrict = ""
            $scope.eduRows = [];
            $scope.eduEngRows = [];
            $scope.trainingRows = [];
            $scope.trainingEngRows = [];
            $scope.nomineeRows = [];
            $scope.nomineeEngRows = [];
            $scope.profile_pic = " ";
            $scope.formSubmitResult = {};
            $scope.ppp = {educationIdBng: []};
            $scope.rank = [];

            $http({
                url: '{{URL::to('HRM/ansar_rank')}}',
                method: 'get'
            }).then(function (response) {
                $scope.rank = response.data;
            })

            $scope.error = $sce.trustAsHtml("");
            $scope.formatDate = function (dValue) {
                return moment(dValue).format("D-MMM-YYYY");
            }
            $scope.recent_status = "{{$ansarAllDetails->designation_id}}";
            $scope.marital_status = "{{$ansarAllDetails->marital_status}}";
            $scope.particular_skill = "{{$ansarAllDetails->skill_id}}";
            if ($scope.particular_skill == 1) {
                $scope.own_particular_skill = true;
            }
            $scope.long_term_disease = "{{$ansarAllDetails->disease_id}}";
            if ($scope.long_term_disease == 1) {
                $scope.own_disease = true;
            }
            $scope.selectedSession = "{{$ansarAllDetails->session_id}}";
            if ($scope.selectedSession) {
                getNameService.getSession($scope.selectedSession).then(function (response) {
                    $scope.sessions = response.data;
                })
            }

            $scope.SelectedDivision = "{{$ansarAllDetails->division_id}}";
            if ($scope.SelectedDivision) {
                getNameService.getDistric($scope.SelectedDivision).then(function (response) {
                    $scope.district = response.data;
                })
            }
            $scope.SelectedDistrict = "{{$ansarAllDetails->unit_id}}";
            if ($scope.SelectedDistrict) {
                getNameService.getThana($scope.SelectedDistrict).then(function (response) {
                    $scope.thana = response.data;
                })
            }

            $scope.ThanaModel = "{{$ansarAllDetails->thana_id}}";

            if ($scope.ThanaModel) {
                getNameService.getThana($scope.SelectedDistrict).then(function (response) {
                    $scope.thana = response.data;
                })
            }

            $scope.BloodModel = "{{$ansarAllDetails->blood_group_id}}";
            $scope.sex = "{{$ansarAllDetails->sex}}";

            $scope.calling = function () {

            }
            getNameService.getDivision().then(function (response) {
                $scope.division = response.data;
            });
            $scope.SelectedItemChanged = function () {
                getNameService.getDistric($scope.SelectedDivision).then(function (response) {
                    $scope.district = response.data;
                })
            };
            $scope.SelectedDistrictChanged = function () {
                getNameService.getThana($scope.SelectedDistrict).then(function (response) {
                    $scope.thana = response.data;
                })
            };
            $scope.loadSession = function () {
                getNameService.getSession($scope.selectedSession).then(function (response) {
                    $scope.sessions = response.data;
                })
            };
            $scope.loadSession()
            $scope.addEduinput = function () {

                $scope.eduRows.push([
                    {text: "শিক্ষা প্রতিষ্ঠানের নাম", value: '', name: 'institute_name[]'},
                    {text: "Passing year", value: '', name: 'passing_year[]'},
                    {text: "বিভাগ / শ্রেণী", value: '', name: 'gade_divission[]'},
                ]);
                $scope.eduEngRows.push([
                    {text: "Institute Name", value: '', name: 'institute_name_eng[]'},
                    {text: "Passing year", value: '', name: 'passing_year_eng[]'},
                    {text: "Class", value: '', name: 'gade_divission_eng[]'},
                ]);
            };

            @forelse($ansarAllDetails->education as $edu)
                @if($edu->educationName) $scope.ppp.educationIdBng.push('{{$edu->educationName->id}}');
            @endif

                   $scope.eduRows.push([

                        {text: "শিক্ষা প্রতিষ্ঠানের নাম", value: '{{$edu->institute_name}}', name: 'institute_name[]'},
                        {text: "Passing year", value: '{{$edu->passing_year}}', name: 'passing_year[]'},
                        {text: "বিভাগ / শ্রেণী", value: '{{$edu->gade_divission}}', name: 'gade_divission[]'},
                    ]);
            $scope.eduEngRows.push([
                {text: "Institute Name", value: '{{$edu->institute_name_eng}}', name: 'institute_name_eng[]'},
                {text: "Passing year", value: '{{$edu->passing_year_eng}}', name: 'passing_year_eng[]'},
                {text: "Class", value: '{{$edu->gade_divission_eng}}', name: 'gade_divission_eng[]'},
            ]);
            @empty
            $scope.addEduinput();
            @endforelse;


            $scope.eduDeleteRows = function (index) {
                $scope.eduRows.splice(index, 1);
                $scope.eduEngRows.splice(index, 1);
            }

            $scope.addTraininput = function () {

                $scope.trainingRows.push([
                    {text: "পদবী", value: '', name: 'training_designation[]', type: 'dropdown', class_name: ''},
                    {text: "প্রতিষ্ঠান", value: '', name: 'institute[]', type: 'text', class_name: ''},
                    {
                        text: "Training start date",
                        value: '',
                        name: 'training_start[]',
                        type: '',
                        class_name: 'date-picker-dir'
                    },
                    {
                        text: "Training end date",
                        value: '',
                        name: 'training_end[]',
                        type: '',
                        class_name: 'date-picker-dir'
                    },
                    {text: "আপ/আম নম্বর", value: '', name: 'training_sanad[]', type: 'text', class_name: ''}

                ]);
                $scope.trainingEngRows.push([
                    {
                        text: "Designation",
                        value: '',
                        name: 'training_designation_eng[]',
                        type: 'dropdown',
                        class_name: ''
                    },
                    {text: "Institute", value: '', name: 'institute_eng[]', type: 'text'},
                    {
                        text: "Training start date",
                        value: '',
                        name: 'training_start_eng[]',
                        type: '',
                        class_name: 'date-picker-dir'
                    },
                    {
                        text: "Training end date",
                        value: '',
                        name: 'training_end_eng[]',
                        type: '',
                        class_name: 'date-picker-dir'
                    },
                    {text: "আপ/আম নম্বর", value: '', name: 'training_sanad_eng[]', type: 'text', class_name: ''}
                ]);
            };

            @forelse($ansarAllDetails->training as $training)
                $scope.trainingRows.push([
                        {
                            text: "পদবী",
                            value: '{{$training->training_designation}}',
                            name: 'training_designation[]',
                            type: 'dropdown',
                            class_name: ''
                        },
                        {
                            text: "প্রতিষ্ঠান",
                            value: '{{$training->training_institute_name}}',
                            name: 'institute[]',
                            type: 'text',
                            class_name: ''
                        },
                        {
                            text: "Training start date",
                            value: moment('{{$training->training_start_date_eng}}').format("D-MMM-YYYY"),
                            name: 'training_start[]',
                            type: '',
                            class_name: 'date-picker-dir'
                        },
                        {
                            text: "Training end date",
                            value: moment('{{$training->training_end_date_eng}}').format("D-MMM-YYYY"),
                            name: 'training_end[]',
                            type: '',
                            class_name: 'date-picker-dir'
                        },
                        {
                            text: "আপ/আম নম্বর",
                            value: '{{$training->trining_certificate_no}}',
                            name: 'training_sanad[]',
                            type: 'text',
                            class_name: ''
                        }

                    ]);
            $scope.trainingEngRows.push([
                {
                    text: "Designation",
                    value: '{{$training->training_designation_eng}}',
                    name: 'training_designation_eng[]',
                    type: 'dropdown',
                    class_name: ''
                },
                {
                    text: "Institute",
                    value: '{{$training->training_institute_name_eng}}',
                    name: 'institute_eng[]',
                    type: 'text'
                },
                {
                    text: "Training start date",
                    value: moment('{{$training->training_start_date_eng}}').format("D-MMM-YYYY"),
                    name: 'training_start_eng[]',
                    type: '',
                    class_name: 'date-picker-dir'
                },
                {
                    text: "Training end date",
                    value: moment('{{$training->training_end_date_eng}}').format("D-MMM-YYYY"),
                    name: 'training_end_eng[]',
                    type: '',
                    class_name: 'date-picker-dir'
                },
                {
                    text: "আপ/আম নম্বর",
                    value: '{{$training->trining_certificate_no_eng}}',
                    name: 'training_sanad_eng[]',
                    type: 'text',
                    class_name: ''
                }
            ]);
            @empty
            $scope.addTraininput();
            @endforelse;


            $scope.deleteTrainingRows = function (index) {
                $scope.trainingRows.splice(index, 1);
                $scope.trainingEngRows.splice(index, 1)
            }
            $scope.addNomineeinput = function () {

                $scope.nomineeRows.push([
                    {text: "নাম", value: '', name: 'nominee_name[]'},
                    {text: "সম্পর্ক", value: '', name: 'relation[]'},
                    {text: "শতকরা", value: '', name: 'percentage[]'},
                    {text: "মোবাইল নং", value: '', name: 'nominee_mobile[]'}
                ]);
                $scope.nomineeEngRows.push([
                    {text: "Name", value: '', name: 'nominee_name_eng[]'},
                    {text: "Relation", value: '', name: 'relation_eng[]'},
                    {text: "Percentage", value: '', name: 'percentage_eng[]'},
                    {text: "Mobile no", value: '', name: 'nominee_mobile_eng[]'}
                ]);
            };
            @forelse($ansarAllDetails->nominee as $nominee)
            $scope.nomineeRows.push([
                        {text: "নাম", value: '{{$nominee->name_of_nominee}}', name: 'nominee_name[]'},
                        {text: "সম্পর্ক", value: '{{$nominee->relation_with_nominee}}', name: 'relation[]'},
                        {text: "Percentage", value: '{{$nominee->nominee_parcentage}}', name: 'percentage[]'},
                        {text: "মোবাইল নং", value: '{{$nominee->nominee_contact_no}}', name: 'nominee_mobile[]'}
                    ]);
            $scope.nomineeEngRows.push([
                {text: "Name", value: '{{$nominee->name_of_nominee_eng}}', name: 'nominee_name_eng[]'},
                {text: "Relation", value: '{{$nominee->relation_with_nominee_eng}}', name: 'relation_eng[]'},
                {text: "Percentage", value: '{{$nominee->nominee_parcentage_eng}}', name: 'percentage_eng[]'},
                {text: "Mobile no", value: '{{$nominee->nominee_contact_no_eng}}', name: 'nominee_mobile_eng[]'}
            ]);
            @empty
            $scope.addNomineeinput()
            @endforelse



    //        $scope.addTraininput();
//        $scope.addNomineeinput();

            $scope.deleteNomineeRows = function (index) {
                $scope.nomineeRows.splice(index, 1);
                $scope.nomineeEngRows.splice(index, 1);
            }
            getBloodService.getAllBloodName().then(function (response) {
                $scope.blood = response.data;
            })

            getDiseaseSkillService.getAllDisease().then(function (response) {
                $scope.Disease = response.data;
            });
            getDiseaseSkillService.getAllSkill().then(function (response) {
                $scope.skillName = response.data;
            });
            getDiseaseSkillService.getAllEducationName().then(function (response) {
                $scope.ppp.educationName = response.data;
            });

            $scope.diseaseChange = function () {

                if ($scope.long_term_disease == 1)
                    $scope.own_disease = true;
                else {
                    $scope.my_disease = "";
                    $scope.own_disease = false;
                }
            }
            $scope.skillChange = function () {

                if ($scope.particular_skill == 1)
                    $scope.own_particular_skill = true;
                else {
                    $scope.my_skill = "";
                    $scope.own_particular_skill = false;
                }
            }

        });

        GlobalApp.factory('getNameService', function ($http) {
            return {
                getDivision: function () {
                    return $http.get("{{URL::to('HRM/DivisionName')}}");
                },
                getDistric: function (data) {

                    return $http.get("{{URL::to('HRM/DistrictName')}}", {params: {id: data}});
                },
                getThana: function (data) {
                    return $http.get("{{URL::to('HRM/ThanaName')}}", {params: {id: data}});
                },
                getSession: function () {
                    return $http.get("{{URL::to('HRM/SessionName')}}");
                }
            }

        });

        GlobalApp.factory('getBloodService', function ($http) {
            return {
                getAllBloodName: function () {
                    return $http.get("{{URL::to('HRM/getBloodName')}}")
                }
            }
        });

        GlobalApp.factory('getDiseaseSkillService', function ($http) {
            return {
                getAllDisease: function () {
                    return $http.get("{{URL::to('HRM/getDiseaseName')}}")
                },
                getAllSkill: function () {
                    return $http.get("{{URL::to('HRM/getallskill')}}")
                },
                getAllEducationName: function () {
                    return $http.get("{{URL::to('HRM/getalleducation')}}")
                }
            }

        });
        GlobalApp.directive('formSubmit', function ($sce) {
            return {
                restrict: 'A',
                link: function (scope, element, attribute) {
                    $(element).click(function (e) {
                        var b = $(this).val()
                        $(".overlay").css('display','block');
                        $("#pppp").ajaxSubmit({
                            success: function (responseText, statusText, xhr, $form) {
                                scope.formSubmitResult = responseText;
                                $(".overlay").css('display','none');
                                console.log(scope.formSubmitResult)
                                if(scope.formSubmitResult.status==false){
                                    var keys = Object.keys(scope.formSubmitResult.error);
                                    var min = 10000000;
                                    keys.forEach(function (value){
                                        console.log({top:$("input[name="+value+"],select[name="+value+"]").offset().top,value:value})
                                        min = min>$("input[name="+value+"],select[name="+value+"]").offset().top?$("input[name="+value+"],select[name="+value+"]").offset().top:min;
                                    })
//                                    alert(min)
                                    $('body,html').animate({
                                        scrollTop:min-$("#entryform").offset().top + $("#entryform").scrollTop()
                                    },1000)
                                }
                                if (scope.formSubmitResult.status == true) {
                                    window.location = "{{URL::to('HRM/entrylist')}}";
                                }
                                if (scope.formSubmitResult.status == 'numeric') {
                                }
                                scope.$digest();
                            }, error: function (responseText, statusText, xhr, $form) {
                                $(".overlay").css('display','none');
                                scope.error = $sce.trustAsHtml(xhr.responseText);

                                console.log(responseText);
                                console.log(responseText);
                            }, beforeSubmit: function (arr, $form, options) {
                                arr.push({name: 'action', type: 'text', value: b})
                                console.log(arr)
                            }
                        });
                        return false;
                    })
                }
            }
        })
        $(document).ready(function (e) {
            $(".overlay").height($(window).height());
            $(window).resize(function () {
                $(".overlay").height($(window).height());
            })
            $(window).scroll(function () {
//                alert("scroll")
                $(".overlay").css('top',($(window).scrollTop()-$("#entryform").offset().top)+"px")
            })
        })
    </script>
    <div id="entryform" ng-controller="fullEntryFormController" d-picker>
        <div class="overlay">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
        </div>
        <div>
            {{--<div class="breadcrumbplace">--}}
                {{--{!! Breadcrumbs::render('editentryform') !!}--}}
            {{--</div>--}}

            <section class="content">

                <div class="row">

                    <div style="padding: 10px 20px">


                        <div id="entryform">
                            <form id="pppp" class="form-horizontal" enctype="multipart/form-data" id="myForm"
                                  method="post" action="{{URL::route('submiteditentry')}}">
                                {!! csrf_field() !!}
                                <fieldset>
                                    <div class="level-title-session-entry">
                                        <h5 style="text-align: center;">পারিবারিক তথ্য </h5>
                                    </div>
                                    <div class="box-info">
                                        <div class="box-body">
                                            <div class="form group col-md-12">

                                                <label class="control-label col-sm-2" for="email">Ansar ID:</label>

                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control "
                                                           value="{{ $ansarAllDetails->ansar_id}}" disabled/>
                                                </div>

                                            </div>
                                            <div class="form-horizontal col-md-12" ng-show="isAdmin==55">

                                                <label class="control-label col-sm-2" for="session_id">Session
                                                    ID:</label>

                                                <div class="col-sm-10 ">
                                                    <select name="session_id" ng-model="selectedSession"
                                                            class="form-control" id="session_id"
                                                            value="{{Request::old('selectedSession')}}">
                                                        <option value="">--Select Session--</option>
                                                        <option ng-repeat="s in sessions" value="[[s.id]]">
                                                            [[s.session_name]]
                                                        </option>
                                                    </select>
                                                </div>

                                            </div>
                                            <div class="form group col-md-12"
                                                 ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.session_id[0]}">

                                                <label class="control-label col-sm-2" for="email"><sup
                                                            style="color: #ff0709;font-size: 1em">*</sup>Name:</label>

                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control " name="ansar_name_eng"
                                                           value="{{ $ansarAllDetails->ansar_name_eng}}"
                                                           placeholder="Enter your name"/>
                                                    <span style="color:red"
                                                          ng-show="formSubmitResult.error.ansar_name_eng[0]">[[ formSubmitResult.error.ansar_name_eng[0] ]]</span>
                                                </div>

                                            </div>


                                            <div class="form-horizontal col-md-12 "
                                                 ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.ansar_name_bng[0]}">
                                                <label class="control-label col-sm-2" for="email"><sup
                                                            style="color: #ff0709;font-size: 1em">*</sup>নাম:</label>

                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="ansar_name_bng"
                                                           name="ansar_name_bng"
                                                           value="{{ $ansarAllDetails->ansar_name_bng}}"
                                                           placeholder="আপনার নাম লিখুন"/>
                                                    <span style="color:red"
                                                          ng-show="formSubmitResult.error.ansar_name_bng[0]">[[ formSubmitResult.error.ansar_name_bng[0] ]]</span>
                                                </div>
                                            </div>


                                            <div class="form-horizontal col-md-12 "
                                                 ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.recent_status[0]}">

                                                <label class="control-label col-sm-2" for="email"><sup
                                                            style="color: #ff0709;font-size: 1em">*</sup>বর্তমান পদবী :</label>

                                                <div class="col-sm-10 ">
                                                    <select name="recent_status" ng-model="recent_status"
                                                            class="form-control" id="sell">
                                                        <option value="">--পদবী নির্বাচন করুন--</option>
                                                        <option ng-repeat="r in rank" value="[[r.id]]">[[r.name_bng]]
                                                        </option>
                                                    </select>
                                                    <span style="color:red"
                                                          ng-show="formSubmitResult.error.recent_status[0]">[[ formSubmitResult.error.recent_status[0] ]]</span>
                                                </div>

                                            </div>


                                            <div class="form group col-md-12 "
                                                 ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.father_name_eng[0]}">
                                                <label class="control-label col-sm-2" for="email"><sup
                                                            style="color: #ff0709;font-size: 1em">*</sup>Father's name:</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control" id="father_name_eng"
                                                           name="father_name_eng"
                                                           value="{{ $ansarAllDetails->father_name_eng}}" type="text"
                                                           placeholder="Father's name">
                                                    <span style="color:red"
                                                          ng-show="formSubmitResult.error.father_name_eng[0]">[[ formSubmitResult.error.father_name_eng[0] ]]</span>
                                                </div>
                                            </div>
                                            <div class="form group col-md-12 "
                                                 ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.father_name_bng[0]}">
                                                <label class="control-label col-sm-2" for="email"><sup
                                                            style="color: #ff0709;font-size: 1em">*</sup>পিতার
                                                    নাম:</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control " id="father_name_bng"
                                                           name="father_name_bng"
                                                           value="{{ $ansarAllDetails->father_name_bng}}" type="text"
                                                           placeholder="পিতার নাম">
                                                    <span style="color:red"
                                                          ng-show="formSubmitResult.error.father_name_bng[0]">[[ formSubmitResult.error.father_name_bng[0] ]]</span>
                                                </div>
                                            </div>
                                            <div class="form group col-md-12 "
                                                 ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.mother_name_eng[0]}">
                                                <label class="control-label col-sm-2" for="email"><sup
                                                            style="color: #ff0709;font-size: 1em">*</sup>Mother's Name:</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control  " id="mother_name_eng"
                                                           name="mother_name_eng" type="text"
                                                           placeholder=" Mother's Name"
                                                           value="{{ $ansarAllDetails->mother_name_eng}}">
                                                    <span style="color:red"
                                                          ng-show="formSubmitResult.error.mother_name_eng[0]">[[ formSubmitResult.error.mother_name_eng[0] ]]</span>
                                                </div>
                                            </div>
                                            <div class="form group col-md-12 "
                                                 ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.mother_name_bng[0]}">
                                                <label class="control-label col-sm-2" for="email"><sup
                                                            style="color: #ff0709;font-size: 1em">*</sup>মাতার
                                                    নাম:</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control  " id="mother_name_bng"
                                                           name="mother_name_bng" type="text" placeholder="মাতার নাম"
                                                           value="{{ $ansarAllDetails->mother_name_bng}}">
                                                    <span style="color:red"
                                                          ng-show="formSubmitResult.error.mother_name_bng[0]">[[ formSubmitResult.error.mother_name_bng[0] ]]</span>
                                                </div>

                                            </div>
                                            <div class="form group col-md-12 "
                                                 ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.data_of_birth[0]}">
                                                <label class="control-label col-sm-2" for="email"><sup
                                                            style="color: #ff0709;font-size: 1em">*</sup>Date of birth:</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control picker " id="data_of_birth"
                                                           name="data_of_birth" date-picker-dir="datePickerDir"
                                                           placeholder="Date of birth"
                                                           value="[[formatDate('{{ $ansarAllDetails->data_of_birth}}')]]">
                                                    <span style="color:red"
                                                          ng-show="formSubmitResult.error.data_of_birth[0]">[[ formSubmitResult.error.data_of_birth[0] ]]</span>
                                                </div>
                                            </div>

                                            <div class="form-horizontal col-md-12 "
                                                 ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.marital_status[0]}">

                                                <label class="control-label col-sm-2" for="email"><sup
                                                            style="color: #ff0709;font-size: 1em">*</sup>Marital status:</label>

                                                <div class="col-sm-10 ">
                                                    <select name="marital_status" ng-model="marital_status"
                                                            class="form-control" id="sell">
                                                        <option value="">--Select your marital condition--</option>
                                                        <option value="Married">Married</option>
                                                        <option value="Unmarried">Unmarried</option>
                                                        <option value="Divorced">Divorced</option>
                                                    </select>
                                                    <span style="color:red"
                                                          ng-show="formSubmitResult.error.marital_status[0]">[[ formSubmitResult.error.marital_status[0] ]]</span>
                                                </div>

                                            </div>


                                            <div class="form group col-md-12 ">
                                                <label class="control-label col-sm-2" for="email">Spouse Name:</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control  " id="spouse_name_eng"
                                                           name="spouse_name_eng" type="text" placeholder="Spouse Name"
                                                           value="{{ $ansarAllDetails->spouse_name_eng}}">

                                                </div>

                                            </div>
                                            <div class="form group col-md-12 ">
                                                <label class="control-label col-sm-2" for="email">স্ত্রী/স্বামীর
                                                    নাম:</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control  " id="spouse_name_bng"
                                                           name="spouse_name_bng" type="text"
                                                           placeholder=" স্ত্রী/স্বামীর নাম "
                                                           value="{{ $ansarAllDetails->spouse_name_bng}}">
                                                </div>
                                            </div>
                                            <div class="form group col-md-12 "
                                                 ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.national_id_no[0]}">
                                                <label class="control-label col-sm-2" for="email"><sup
                                                            style="color: #ff0709;font-size: 1em">*</sup>National Id no:</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control  " id="national_id_no"
                                                           name="national_id_no" type="text"
                                                           placeholder="National Id no(Numeric 17 digit for 13 digit add birth year before id no.)"
                                                           value="{{ $ansarAllDetails->national_id_no}}">
                                                    <span style="color:red"
                                                          ng-show="formSubmitResult.error.national_id_no[0]">[[ formSubmitResult.error.national_id_no[0] ]]</span>
                                                </div>

                                            </div>

                                            <div class="form group col-md-12 ">
                                                <label class="control-label col-sm-2" for="email">Birth Certificate
                                                    no:</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control  " id="birth_certificate_no"
                                                           name="birth_certificate_no" type="text"
                                                           placeholder="Birth Certificate no"
                                                           value="{{ $ansarAllDetails->birth_certificate_no}}">
                                                </div>
                                            </div>

                                            <div class="form-horizontal col-md-12 ">
                                                <label class="control-label col-sm-2" for="email">দীর্ঘ মেয়াদি
                                                    অসুখ:</label>

                                                <div class="col-sm-10">
                                                    <select name="long_term_disease" ng-model="long_term_disease"
                                                            class="form-control" id="sell" ng-change="diseaseChange()">
                                                        <option value="">--অসুখ নির্বাচন করুন--</option>
                                                        <option ng-repeat="x in Disease" value="[[x.id]]">
                                                            [[x.disease_name_bng]]
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-horizontal col-md-12 " ng-show="own_disease">
                                                <label class="control-label col-sm-2" for="email">অসুখ:</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control" id="own_disease" name="own_disease"
                                                           type="text" ng-model="my_disease"
                                                           value="{{$ansarAllDetails->own_disease}}"
                                                           placeholder="আপনার অসুখের নাম লিখুন">
                                                </div>
                                            </div>

                                            <div class="form-horizontal col-md-12 ">
                                                <label class="control-label col-sm-2" for="email">নির্দিষ্ট
                                                    দক্ষতা:</label>

                                                <div class="col-sm-10 ">
                                                    <select name="particular_skill" ng-model="particular_skill"
                                                            ng-change="skillChange()" class="form-control" id="sell">
                                                        <option value="">--দক্ষতা নির্বাচন করুন--</option>
                                                        <option ng-repeat="x in skillName" value="[[x.id]]">
                                                            [[x.skill_name_bng]]
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-horizontal col-md-12 " ng-show="own_particular_skill">
                                                <label class="control-label col-sm-2" for="email">দক্ষতা:</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control"
                                                           value="{{$ansarAllDetails->own_particular_skill}}"
                                                           id="own_particular_skill" name="own_particular_skill"
                                                           type="text" ng-model="my_skill" required
                                                           placeholder="আপনার দক্ষতা লিখুন">
                                                </div>
                                            </div>

                                            <div class="form group col-md-12">
                                                <label class="control-label col-sm-2" for="criminal">Criminal
                                                    Case:</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control  " name="criminal_case" type="text"
                                                           placeholder="Criminal case"
                                                           value="{{ $ansarAllDetails->criminal_case}}">

                                                </div>
                                            </div>
                                            <div class="form group col-md-12">
                                                <label class="control-label col-sm-2" for="criminal">ফৌজদারি
                                                    মামলা:</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control  " name="criminal_case_bng" type="text"
                                                           placeholder="ফৌজদারি মামলা"
                                                           value="{{ $ansarAllDetails->criminal_case_bng}}">

                                                </div>
                                            </div>
                                            {{--<div class="form group col-md-12">--}}
                                                {{--<label class="control-label col-sm-2" for="criminal">সর্বশেষ প্রশিক্ষন--}}
                                                    {{--সনদ নং:</label>--}}

                                                {{--<div class="col-sm-10">--}}
                                                    {{--<input class="form-control  " id="certificate_no"--}}
                                                           {{--name="certificate_no" type="text"--}}
                                                           {{--placeholder="সর্বশেষ প্রশিক্ষন সনদ নং"--}}
                                                           {{--value="{{ $ansarAllDetails->certificate_no}}">--}}
                                                {{--</div>--}}
                                            {{--</div>--}}
                                        </div>
                                    </div>

                                </fieldset>
                                <fieldset>
                                    <div class="level-title-session-entry">
                                        <h5 style="text-align: center;">স্থায়ী ঠিকানা</h5>
                                    </div>
                                    <div class="box-info">
                                        <div class="box-body">
                                            <div class="form-horizontal col-md-12">
                                                <label class="control-label col-sm-2" for="email">Village:</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control  " id="village_name" name="village_name"
                                                           type="text" placeholder=" Village"
                                                           value="{{ $ansarAllDetails->village_name}}">

                                                </div>

                                            </div>
                                            <div class="form-horizontal col-md-12">
                                                <label class="control-label col-sm-2" for="email">গ্রাম:</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control  " id="village_name_bng"
                                                           name="village_name_bng" type="text" placeholder=" গ্রাম"
                                                           value="{{ $ansarAllDetails->village_name_bng}}">

                                                </div>

                                            </div>
                                            <div class="form-horizontal col-md-12">
                                                <label class="control-label col-sm-2" for="email">Post office:</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control  " id="post_office_name"
                                                           name="post_office_name" type="text"
                                                           placeholder=" Post Office Name "
                                                           value="{{ $ansarAllDetails->post_office_name}}">

                                                </div>

                                            </div>
                                            <div class="form-horizontal col-md-12">
                                                <label class="control-label col-sm-2" for="email">ডাকঘর:</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control  " id="post_office_name_bng"
                                                           name="post_office_name_bng" type="text" placeholder=" ডাকঘর "
                                                           value="{{ $ansarAllDetails->post_office_name_bng}}">

                                                </div>

                                            </div>


                                            <div class="form group col-md-12">
                                                <label class="control-label col-sm-2" for="email">Union:</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control  " id="union_name_eng"
                                                           name="union_name_eng" type="text" placeholder=" Union Name"
                                                           value="{{ $ansarAllDetails->union_name_eng}}">
                                                </div>
                                            </div>
                                            <div class="form group col-md-12">
                                                <label class="control-label col-sm-2" for="email">ইউনিয়ন নাম:</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control" id="union_name_bng"
                                                           name="union_name_bng" type="text" placeholder="ইউনিয়ন নাম"
                                                           value="{{ $ansarAllDetails->union_name_bng}}">
                                                </div>
                                            </div>

                                            <div class="form-horizontal col-md-12 "
                                                 ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.division_name_eng[0]}">

                                                <label class="control-label col-sm-2" for="email"><sup
                                                            style="color: #ff0709;font-size: 1em">*</sup>বিভাগ:</label>


                                                <div class="col-sm-10 ">
                                                    <select name="division_name_eng" class="form-control" id="sell"
                                                            ng-model="SelectedDivision"
                                                            ng-change="SelectedItemChanged()">
                                                        <option value="">--বিভাগ নির্বাচন করুন--</option>
                                                        <option ng-repeat="x in division" value="[[x.id]]">
                                                            [[x.division_name_bng]]
                                                        </option>
                                                    </select>
                                                    <span style="color:red"
                                                          ng-show="formSubmitResult.error.division_name_eng[0]">[[ formSubmitResult.error.division_name_eng[0] ]]</span>
                                                </div>

                                            </div>

                                            <div class="form-horizontal col-md-12 "
                                                 ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.unit_name_eng[0]}">

                                                <label class="control-label col-sm-2" for="email"><sup
                                                            style="color: #ff0709;font-size: 1em">*</sup>জেলা:</label>

                                                <div class="col-sm-10 ">
                                                    <select name="unit_name_eng" class="form-control" id="sell"
                                                            ng-model="SelectedDistrict"
                                                            ng-change="SelectedDistrictChanged()">
                                                        <option value="">--জেলা নির্বাচন করুন--</option>
                                                        <option ng-repeat="x in district" value="[[x.id]]">[[
                                                            x.unit_name_bng ]]
                                                        </option>
                                                    </select>
                                                    <span style="color:red"
                                                          ng-show="formSubmitResult.error.unit_name_eng[0]">[[ formSubmitResult.error.unit_name_eng[0] ]]</span>
                                                </div>

                                            </div>

                                            <div class="form-horizontal col-md-12 "
                                                 ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.thana_name_eng[0]}">

                                                <label class="control-label col-sm-2" for="email"><sup
                                                            style="color: #ff0709;font-size: 1em">*</sup>থানা:</label>

                                                <div class="col-sm-10 ">
                                                    <select name="thana_name_eng" class="form-control" id="sell"
                                                            ng-model="ThanaModel" ng-change="SelectedThanaChanged()">
                                                        <option value="">--থানা নির্বাচন করুন--</option>
                                                        <option ng-repeat="x in thana"
                                                                ng-selected="x.id=='{{Request::old('thana_name_eng')}}'"
                                                                value="[[x.id]]">[[ x.thana_name_bng ]]
                                                        </option>
                                                    </select>
                                                    <span style="color:red"
                                                          ng-show="formSubmitResult.error.thana_name_eng[0]">[[ formSubmitResult.error.thana_name_eng[0] ]]</span>
                                                </div>

                                            </div>

                                        </div>
                                    </div>
                                </fieldset>
                                <fieldset>


                                    <div class="level-title-session-entry">
                                        <h5 style="text-align: center;">শারীরিক যোগ্যতার তথ্য</h5>
                                    </div>
                                    <div class="box-info">
                                        <div class="box-body">
                                            <div class="form-horizontal col-md-12 "
                                                 ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.hight_feet[0]}">
                                                <label class="control-label col-sm-2" for="email"><sup
                                                            style="color: #ff0709;font-size: 1em">*</sup>Height:</label>

                                                <div class="col-sm-5">
                                                    <input class="form-control  " id="hight_feet" name="hight_feet"
                                                           type="text" placeholder=" FEET"
                                                           value="{{ $ansarAllDetails->hight_feet}}">
                                                    <span style="color:red"
                                                          ng-show="formSubmitResult.error.hight_feet[0]">[[ formSubmitResult.error.hight_feet[0] ]]</span>
                                                </div>
                                                <div class="col-sm-5">
                                                    <input class="form-control  " id="hight_inch" name="hight_inch"
                                                           type="text" placeholder=" INCHES"
                                                           value="{{ $ansarAllDetails->hight_inch}}">

                                                </div>
                                            </div>
                                            <div class="form-horizontal col-md-12 "
                                                 ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.blood_group_name_bng[0]}">

                                                <label class="control-label col-sm-2" for="email"><sup
                                                            style="color: #ff0709;font-size: 1em">*</sup>রক্তের
                                                    গ্রুপ:</label>

                                                <div class="col-sm-10 ">
                                                    <select name="blood_group_name_bng" class="form-control" id="sell"
                                                            ng-model="BloodModel">
                                                        <option value="">--রক্তের গ্রুপ নির্বাচন করুন--</option>
                                                        <option ng-repeat="x in blood"
                                                                ng-selected="x.id=='{{Request::old('blood_group_name_bng')}}'"
                                                                value="[[x.id]]">[[ x.blood_group_name_bng ]]
                                                        </option>
                                                    </select>
                                                    <span style="color:red"
                                                          ng-show="formSubmitResult.error.blood_group_name_bng[0]">[[ formSubmitResult.error.blood_group_name_bng[0] ]]</span>
                                                </div>

                                            </div>


                                            <div class="form-horizontal col-md-12">
                                                <label class="control-label col-sm-2" for="email">Eye color:</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control  " id="eye_color" name="eye_color"
                                                           type="text" placeholder="Eye color"
                                                           value="{{ $ansarAllDetails->eye_color}}">
                                                </div>
                                            </div>
                                            <div class="form-horizontal col-md-12">
                                                <label class="control-label col-sm-2" for="email">চোখের রং:</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control  " id="eye_color" name="eye_color_bng"
                                                           type="text" placeholder="চোখের রং"
                                                           value="{{ $ansarAllDetails->eye_color_bng}}">
                                                </div>
                                            </div>
                                            <div class="form group col-md-12">
                                                <label class="control-label col-sm-2" for="email">Skin color:</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control  " id="skin_color" name="skin_color"
                                                           type="text" placeholder="Skin color"
                                                           value="{{ $ansarAllDetails->skin_color}}">
                                                </div>
                                            </div>
                                            <div class="form group col-md-12">
                                                <label class="control-label col-sm-2" for="skin_color_bng">গায়ের
                                                    রং:</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control  " id="skin_color_bng"
                                                           name="skin_color_bng" type="text" placeholder="গায়ের রং"
                                                           value="{{ $ansarAllDetails->skin_color_bng}}">
                                                </div>
                                            </div>

                                            <div class="form-horizontal col-md-12 "
                                                 ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.sex[0]}">

                                                <label class="control-label col-sm-2" for="email"><sup
                                                            style="color: #ff0709;font-size: 1em">*</sup>Gender:</label>

                                                <div class="col-sm-10 ">
                                                    <select name="sex" ng-model="sex" class="form-control" id="sell">
                                                        <option value="">--Select an option--</option>
                                                        <option value="Male">Male</option>
                                                        <option value="Female">Female</option>
                                                        <option value="Other">Other</option>
                                                    </select>
                                                    <span style="color:red" ng-show="formSubmitResult.error.sex[0]">[[ formSubmitResult.error.sex[0] ]]</span>
                                                </div>

                                            </div>

                                            <div class="form group col-md-12">
                                                <label class="control-label col-sm-2" for="email">Identification
                                                    mark:</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control  " id="identification_mark"
                                                           name="identification_mark" type="text"
                                                           placeholder="Identification mark"
                                                           value="{{ $ansarAllDetails->identification_mark}}">
                                                </div>
                                            </div>
                                            <div class="form group col-md-12">
                                                <label class="control-label col-sm-2" for="email">সনাক্তকরন
                                                    চিহ্ন:</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control  " id="identification_mark_bng"
                                                           name="identification_mark_bng" type="text"
                                                           placeholder="সনাক্তকরন চিহ্ন"
                                                           value="{{ $ansarAllDetails->identification_mark_bng}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                <input type="hidden" name="ansar_id" value="{{$ansarAllDetails->ansar_id}}">

                                <fieldset>
                                    <div class="level-title-session-entry">
                                        <h5 style="text-align: center;">শিক্ষাগত যোগ্যতার তথ্য</h5>
                                    </div>
                                    <div class="box-info">
                                        <div class="box-body">
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <tr>
                                                        <th>শিক্ষাগত যোগ্যতা</th>
                                                        <th>শিক্ষা প্রতিষ্ঠানের নাম</th>
                                                        <th>Passing year</th>
                                                        <th>বিভাগ / শ্রেণী</th>
                                                        <th>Action</th>
                                                    </tr>
                                                    <tr ng-repeat="row in eduRows">
                                                        <td ng-init="i=$index">
                                                            <select name="educationIdBng[]"
                                                                    ng-model="ppp.educationIdBng[i]">
                                                                <option value="">--অপশন নির্বাচন করুন--</option>
                                                                <option ng-repeat="r in ppp.educationName"
                                                                        value="[[r.id]]">[[r.education_deg_bng]]
                                                                </option>
                                                            </select>
                                                        </td>
                                                        <td ng-repeat="r in row"><input type="text" name="[[r.name]]"
                                                                                        placeholder="[[ r.text ]]"
                                                                                        value="[[r.value]]"></td>
                                                        <td><a href=""
                                                               ng-click="(eduRows.length > 1)?eduDeleteRows($index):''"><i
                                                                        class="glyphicon glyphicon-trash"></i></a></td>
                                                    </tr>
                                                    <tr>
                                                        <td style=" border-top:0px;background: #ffffff;">
                                                                <button class="btn btn-info" ng-click="addEduinput()" >Add more</button>
                                                        </td>
                                                    </tr>
                                                </table>


                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                <fieldset>
                                    <div class="level-title-session-entry">
                                        <h5 style="text-align: center;">Educational Information</h5>
                                    </div>
                                    <div class="box-info">
                                        <div class="box-body">
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <tr>
                                                        <th>Name Of Degree</th>
                                                        <th>Institute Name</th>
                                                        <th>Passing year</th>
                                                        <th>Class</th>
                                                        <th>Action</th>
                                                    </tr>
                                                    <tr ng-repeat="row in eduEngRows">
                                                        <td ng-init="i=$index">
                                                            <select ng-model="ppp.educationIdBng[i]"
                                                                    ng-change="eduEngChange($index)">
                                                                <option value="">--Select an option--</option>
                                                                <option ng-repeat="r in ppp.educationName"
                                                                        value="[[r.id]]">[[r.education_deg_eng]]
                                                                </option>
                                                            </select>
                                                        </td>
                                                        <td ng-repeat="r in row"><input type="text" name="[[r.name]]"
                                                                                        placeholder="[[ r.text ]]"
                                                                                        value="[[r.value]]"></td>
                                                        <td><a href=""
                                                               ng-click="(eduRows.length > 1)?eduDeleteRows($index):''"><i
                                                                        class="glyphicon glyphicon-trash"></i></a></td>
                                                    </tr>
                                                    <tr>
                                                        <td style=" border-top:0px;background: #ffffff;">
                                                            <button class="btn btn-info" ng-click="addEduinput()" >Add more</button>
                                                        </td>
                                                    </tr>
                                                </table>


                                            </div>
                                        </div>
                                    </div>
                                </fieldset>


                                <fieldset>

                                    <div class="level-title-session-entry">
                                        <h5 style="text-align: center;">প্রশিক্ষন সংক্রান্ত তথ্য্</h5>
                                    </div>
                                    <div class="box-info">
                                        <div class="box-body">
                                            <div class=" table-responsive">
                                                <table class="table">
                                                    <tr>
                                                        <th>পদবী</th>
                                                        <th>প্রতিষ্ঠান</th>
                                                        <th>Training start date</th>
                                                        <th>Training end date</th>
                                                        <th>সনদ নং</th>
                                                        <th>Action</th>
                                                    </tr>


                                                    <tr ng-repeat="row in trainingRows">

                                                        <td ng-repeat="r in row">
                                                            <select ng-if="r.type=='dropdown'" name="[[r.name]]">
                                                                <option value="">--পদবী নির্বাচন করুন--</option>
                                                                <option ng-repeat="ra in rank" value="[[ra.name_bng]]"
                                                                        ng-selected="ra.name_bng==r.value">
                                                                    [[ra.name_bng]]
                                                                </option>
                                                            </select>
                                                            <input ng-if="r.type!='dropdown'" style="line-height: 18px;"
                                                                   type="[[r.type]]" date-picker-dir="[[r.class_name]]"
                                                                   name="[[r.name]]" placeholder="[[ r.text ]]"
                                                                   value="[[r.value]]"></td>
                                                        <td><a href=""
                                                               ng-click="(trainingRows.length > 1)?deleteTrainingRows($index):''"><i
                                                                        class="glyphicon glyphicon-trash"></i></a></td>
                                                    </tr>
                                                    <tr>
                                                        <td style=" border-top:0px;background: #ffffff;">
                                                            <button class="btn btn-info" ng-click="addEduinput()" >Add more</button>
                                                        </td>
                                                    </tr>
                                                </table>


                                            </div>
                                        </div>
                                    </div>
                                </fieldset>


                                <fieldset>

                                    <div class="level-title-session-entry">
                                        <h5 style="text-align: center;">Training Information</h5>
                                    </div>
                                    <div class="box-info">
                                        <div class="box-body">
                                            <div class=" table-responsive">
                                                <table class="table">
                                                    <tr>
                                                        <th>Designation</th>
                                                        <th>Institute</th>
                                                        <th>Training start date</th>
                                                        <th>Training end date</th>
                                                        <th>Certificate No.</th>
                                                        <th>Action</th>
                                                    </tr>


                                                    <tr ng-repeat="row in trainingEngRows">
                                                        <td ng-repeat="r in row">
                                                            <select ng-if="r.type=='dropdown'" name="[[r.name]]">
                                                                <option value="">--Select a rank--</option>
                                                                <option ng-repeat="ra in rank" value="[[ra.name_bng]]"
                                                                        ng-selected="ra.name_eng==r.value">
                                                                    [[ra.name_eng]]
                                                                </option>
                                                            </select>
                                                            <input ng-if="r.type!='dropdown'" style="line-height: 18px;"
                                                                   type="[[r.type]]" name="[[r.name]]"
                                                                   placeholder="[[ r.text ]]" value="[[r.value]]"
                                                                   date-picker-dir="[[r.class_name]]"></td>
                                                        <td><a href=""
                                                               ng-click="(trainingEngRows.length > 1)?deleteTrainingRows($index):''"><i
                                                                        class="glyphicon glyphicon-trash"></i></a></td>
                                                    </tr>
                                                    <tr>
                                                        <td style=" border-top:0px;background: #ffffff;">
                                                            <button class="btn btn-info" ng-click="addEduinput()" >Add more</button>
                                                        </td>
                                                    </tr>
                                                </table>


                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                <fieldset>

                                    <div class="level-title-session-entry">
                                        <h5 style="text-align: center;">উত্তরাধিকারীর তথ্য</h5>
                                    </div>
                                    <div class="box-info">
                                        <div class="box-body">
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <tr>
                                                        <th>নাম</th>
                                                        <th>সম্পর্ক</th>
                                                        <th>Percentage(%)</th>
                                                        <th>মোবাইল নং</th>
                                                        <th>Action</th>
                                                    </tr>

                                                    <tr ng-repeat="row in nomineeRows">
                                                        <td ng-repeat="r in row"><input type="text" name="[[r.name]]"
                                                                                        placeholder="[[ r.text ]]"
                                                                                        value="[[r.value]]"></td>
                                                        <td><a href=""
                                                               ng-click="(nomineeRows.length > 1)?deleteNomineeRows($index):''"><i
                                                                        class="glyphicon glyphicon-trash"></i></a></td>
                                                    </tr>
                                                    <tr>
                                                        <td style=" border-top:0px;background: #ffffff;">
                                                            <button class="btn btn-info" ng-click="addEduinput()" >Add more</button>
                                                        </td>
                                                    </tr>
                                                </table>


                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                <fieldset>

                                    <div class="level-title-session-entry">
                                        <h5 style="text-align: center;">Nominee Information</h5>
                                    </div>
                                    <div class="box-info">
                                        <div class="box-body">
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Relation</th>
                                                        <th>Percentage(%)</th>
                                                        <th>Mobile No.</th>
                                                        <th>Action</th>
                                                    </tr>

                                                    <tr ng-repeat="row in nomineeEngRows">
                                                        <td ng-repeat="r in row"><input type="text" name="[[r.name]]"
                                                                                        placeholder="[[ r.text ]]"
                                                                                        value="[[r.value]]"></td>
                                                        <td><a href=""
                                                               ng-click="(nomineeRows.length > 1)?deleteNomineeRows($index):''"><i
                                                                        class="glyphicon glyphicon-trash"></i></a></td>
                                                    </tr>
                                                    <tr>
                                                        <td style=" border-top:0px;background: #ffffff;">
                                                            <button class="btn btn-info" ng-click="addEduinput()" >Add more</button>
                                                        </td>
                                                    </tr>
                                                </table>


                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                <fieldset>
                                    <div class="level-title-session-entry">
                                        <h5 style="text-align: center;">অন্যান্য তথ্য</h5>
                                    </div>
                                    <div class="box-info">
                                        <div class="box-body">
                                            <div class="form-horizontal col-md-12 "
                                                 ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.mobile_no_self[0]}">
                                                <label class="control-label col-sm-2" for="email"><sup
                                                            style="color: #ff0709;font-size: 1em">*</sup>Mobile
                                                    no(Self):</label>

                                                <div class="col-sm-10">
                                                    <div class="input-group" ng-init="mobile_no_self='{{ $ansarAllDetails->mobile_no_self}}'">
                                                        <span class="input-group-addon">+88</span>
                                                        <input class="form-control  " id="mobile_no_self"
                                                               name="mobile_no_self" type="text"
                                                               placeholder="Mobile no(Self)"
                                                               ng-model="mobile_no_self"
                                                               ng-change="mobile_no_self=mobile_no_self.length>11?mobile_no_self.substring(0,11):mobile_no_self"
                                                               value="{{ $ansarAllDetails->mobile_no_self}}">
                                                    </div>
                                                    <span style="color:red"
                                                          ng-show="formSubmitResult.error.mobile_no_self[0]">[[ formSubmitResult.error.mobile_no_self[0] ]]</span>
                                                    <span style="color:red" ng-if="formSubmitResult.status=='numeric' ">Numeric value needed</span>
                                                    <span style="color:red" ng-if="formSubmitResult.status=='eight' ">Remove first '88' digits</span>


                                                </div>
                                            </div>
                                            <div class="form-horizontal col-md-12">
                                                <label class="control-label col-sm-2" for="email">Mobile
                                                    no(Request):</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control  " id="mobile_no_request"
                                                           name="mobile_no_request" type="text"
                                                           placeholder="Mobile no(Request)"
                                                           value="{{ $ansarAllDetails->mobile_no_request}}">
                                                </div>

                                            </div>
                                            <div class="form-horizontal col-md-12">
                                                <label class="control-label col-sm-2" for="email">Land phone
                                                    no(self):</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control  " id="land_phone_self"
                                                           name="land_phone_self" type="text"
                                                           placeholder="Land phone no(self)"
                                                           value="{{ $ansarAllDetails->land_phone_self}}">
                                                </div>
                                            </div>
                                            <div class="form group col-md-12">
                                                <label class="control-label col-sm-2" for="email">Land phone
                                                    no(request):</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control  " id="land_phone_request"
                                                           name="land_phone_request" type="text"
                                                           placeholder="Land phone no(request)"
                                                           value="{{ $ansarAllDetails->land_phone_request}}">
                                                </div>
                                            </div>
                                            <div class="form-horizontal col-md-12 ">
                                                <label class="control-label col-sm-2" for="email">Email(Self):</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control  " id="email_self" name="email_self"
                                                           type="email" placeholder="Email(Self)"
                                                           value="{{ $ansarAllDetails->email_self}}">
                                                </div>


                                            </div>
                                            <div class="form-horizontal col-md-12">
                                                <label class="control-label col-sm-2"
                                                       for="email">Email(Request):</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control  " id="email_request"
                                                           name="email_request" type="email"
                                                           placeholder="Email(Request)"
                                                           value="{{ $ansarAllDetails->email_request}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                <fieldset>
                                    <div class="level-title-session-entry">
                                        <h5 style="text-align: center;">ছবি সমূহ</h5>
                                    </div>
                                    <div class="box-info">
                                        <div class="box-body">
                                            <div class=" table-responsive">
                                                <table class="table">
                                                    <tr>
                                                        <th>Profile picture</th>
                                                        <th>Signature  image</th>
                                                        <th>Thumb image</th>
                                                    </tr>
                                                    <tr>
                                                        <td><input type="file" ng-change="calling()" name="profile_pic"
                                                                   ng-model="profile_pic"><span style="color:red"
                                                                                                ng-show="formSubmitResult.error.profile_pic[0]">[[ formSubmitResult.error.profile_pic[0] ]]</span>
                                                        </td>
                                                        <td><input type="file" name="sign_pic" ng-model="sign_pic"><span
                                                                    style="color:red"
                                                                    ng-show="formSubmitResult.error.sign_pic[0]">[[ formSubmitResult.error.sign_pic[0] ]]</span>
                                                        </td>
                                                        <td><input type="file" name="thumb_pic"
                                                                   ng-model="thumb_pic"><span style="color:red"
                                                                                              ng-show="formSubmitResult.error.thumb_pic[0]">[[ formSubmitResult.error.thumb_pic[0] ]]</span>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                                <input type="hidden" name="ID" value="{{ $ansarAllDetails->ansar_id}}"/>

                                <div class="form-horizontal pull-left">
                                    <input form-submit id="submit" type="submit" name="submit" class="btn btn-primary"
                                           value="Update">
                                </div>
                            </form>


                        </div>

                    </div>
                </div>
                <div ng-bind-html="error"></div>
            </section>
        </div>
    </div>
@stop