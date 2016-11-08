@extends('template/master')
@section('title','Entry Form')
{{--@section('small_title','Add new ansar')--}}
@section('breadcrumb')
    {!! Breadcrumbs::render('entryform') !!}
    @endsection
@section('content')



    <script>
        GlobalApp.directive('datePickerDir', function ($timeout) {
            return {
                restrict: "AC",
                link: function (scope, element, attrs) {

                    //alert('asaddad')
                    $timeout(function () {

                    })
                    if(attrs.datePickerDir)$(element).datePicker({
                        defaultValue:attrs.value
                    });
                }

            }

        })
        ;


        GlobalApp.controller('fullEntryFormController', function ($scope, getNameService, getBloodService, getDiseaseSkillService, $sce,$http) {
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
            $scope.error = $sce.trustAsHtml("");
            $scope.ppp = {};
            $scope.rank = [];
            $scope.sessions=[];
            $scope.loadingSession = false;
            $scope.selectedSession="";
            $http({
                url:'{{URL::to('HRM/ansar_rank')}}',
                method:'get'
            }).then(function (response) {
                $scope.rank=response.data;
            })
            $scope.calling = function () {
                alert($scope.profile_pic);
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
//            alert($scope.SelectedDistrict);
                getNameService.getThana($scope.SelectedDistrict).then(function (response) {
                    $scope.thana = response.data;
                })
            };

            $scope.addEduinput = function () {
                $scope.eduRows.push([
                    {text: "শিক্ষা প্রতিষ্ঠানের নাম", value: '', name: 'institute_name[]'},
                    {text: "পাশ করার সাল", value: '', name: 'passing_year[]'},
                    {text: "বিভাগ / শ্রেণী", value: '', name: 'gade_divission[]'},
                ]);
                $scope.eduEngRows.push([
                    {text: "Institute Name", value: '', name: 'institute_name_eng[]'},
                    {text: "Passing year", value: '', name: 'passing_year_eng[]'},
                    {text: "Class", value: '', name: 'gade_divission_eng[]'},
                ]);
            };

            $scope.eduDeleteRows = function (index) {
                $scope.eduRows.splice(index, 1);
                $scope.eduEngRows.splice(index, 1);
            }

            $scope.addTraininput = function () {

                $scope.trainingRows.push([
                    {text: "পদবী", value: '', name: 'training_designation[]', type: 'dropdown', class_name: ''},
                    {text: "প্রতিষ্ঠান", value: '', name: 'institute[]', type: 'text', class_name: ''},
                    {
                        text: "প্রশিক্ষন শুরুর তারিখ",
                        value: '',
                        name: 'training_start[]',
                        type: '',
                        class_name: 'date-picker-dir'
                    },
                    {text: "প্রশিক্ষন শেষের তারিখ", value: '', name: 'training_end[]', type: '', class_name: 'date-picker-dir'},
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
                        text: "Training starting date",
                        value: '',
                        name: 'training_start_eng[]',
                        type: '',
                        class_name: 'date-picker-dir'
                    },
                    {
                        text: "Training ending date",
                        value: '',
                        name: 'training_end_eng[]',
                        type: '',
                        class_name: 'date-picker-dir'
                    },
                    {text: "আপ/আম নম্বর", value: '', name: 'training_sanad_eng[]', type: 'text', class_name: ''}
                ]);
            };


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
                    {text: "Mobile No", value: '', name: 'nominee_mobile_eng[]'}
                ]);
            };
            $scope.addNomineeinput();
            $scope.addTraininput();
            $scope.addEduinput();
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
            $scope.loadSession = function () {
                $scope.loadingSession = true;
                $http({
                    method: 'get',
                    url: '{{URL::to('HRM/session_name')}}',
                    //params: {id: id}
                }).then(function (response) {
                    $scope.sessions = response.data;
                    //alert($scope.sessions)
                    $scope.loadingSession = false;
                })
            }
            $scope.loadSession()
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
                        $(".overlay").css('display','block');
                        var b = $(this).val()
                        $("#pppp").ajaxSubmit({
                            success: function (responseText, statusText, xhr, $form) {
                                scope.formSubmitResult= responseText;
                                console.log(scope.formSubmitResult)
                                $(".overlay").css('display','none');
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
                                    window.location = scope.formSubmitResult.url;
                                    console.log(scope.formSubmitResult.data);
                                    $("#pppp").resetForm();
                                }
                                if (scope.formSubmitResult.status == 'save') {
                                    $("#pppp").resetForm();
                                }
                                if (scope.formSubmitResult.status == 'numeric') {
                                }
                                scope.$digest();
                            },
                            error: function (responseText, statusText, xhr, $form) {
                                $(".overlay").css('display','none');
                                scope.error = $sce.trustAsHtml(xhr.responseText);

                                console.log(responseText);
                            },
                            beforeSubmit: function (arr, $form, options) {
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
            
            <section class="content">

                <div>

                    <div>

                        <div>
                            <div class="alert alert-success" ng-show="formSubmitResult.status==true">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                <span class="glyphicon glyphicon-ok"></span>Ansar added successfully
                            </div>
                        </div>
                        <div>
                            <div class="alert alert-success" ng-show="formSubmitResult.status=='save' ">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                <span class="glyphicon glyphicon-ok"></span>Draft added successfully
                            </div>
                        </div>

                        <div id="entryform">
                            <form id="pppp" class="form-horizontal" enctype="multipart/form-data" id="myForm"
                                  method="post" action="{{URL::to('HRM\handleregistration')}}">
                                {!! csrf_field() !!}
                                <fieldset>

                                    <div class="level-title-session-entry">
                                        <h5 style="text-align: center;">বাক্তিগত ও পারিবারিক তথ্য </h5>
                                    </div>

                                    <div class="box box-info">
                                        <div class="box-body">
                                            <div class="form-horizontal col-md-12" ng-show="isAdmin==55">

                                                <label class="control-label col-sm-2" for="session_id">Session ID:</label>

                                                <div class="col-sm-10 ">
                                                    <select name="session_id" ng-model="selectedSession"
                                                            class="form-control" id="session_id" value="{{Request::old('selectedSession')}}">
                                                        <option value="">--Select Session--</option>
                                                        <option ng-repeat="s in sessions" value="[[s.id]]">
                                                            [[s.session_name]]
                                                        </option>
                                                    </select>
                                                </div>

                                            </div>
                                            {{--Start Ansar Name (English) Field --}}
                                            <div class="form group col-md-12"
                                                 ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.ansar_name_eng[0]}">

                                                <label class="control-label col-sm-2" for="email"><sup style="color: #ff0709;font-size: 1em">*</sup>Name:</label>

                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control " name="ansar_name_eng"
                                                           ng-model="ansar_name_eng" placeholder="Enter your name"
                                                           value="{{Request::old('ansar_name_eng')}}"/>
                                                    <span style="color:red"
                                                          ng-show="formSubmitResult.error.ansar_name_eng[0]">[[ formSubmitResult.error.ansar_name_eng[0] ]]</span>
                                                </div>

                                            </div>
                                            {{--End Ansar Name (English) Field --}}
                                            {{--Start Ansar Name (Bangla) Field --}}
                                            <div class="form-horizontal col-md-12 "
                                                 ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.ansar_name_bng[0]}">
                                                <label class="control-label col-sm-2" for="email"><sup style="color: #ff0709;font-size: 1em">*</sup>নাম:</label>

                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="ansar_name_bng"
                                                           name="ansar_name_bng" ng-model="ansar_name_bng"
                                                           placeholder="আপনার নাম লিখুন"
                                                           value="{{Request::old('ansar_name_bng')}}"/>
                                                    <span style="color:red"
                                                          ng-show="formSubmitResult.error.ansar_name_bng[0]">[[ formSubmitResult.error.ansar_name_bng[0] ]]</span>
                                                </div>
                                            </div>
                                            {{--End Ansar Name (Bangla) Field --}}
                                            {{--Start Ansar Rank Field --}}
                                            <div class="form-horizontal col-md-12 "
                                                 ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.recent_status[0]}">

                                                <label class="control-label col-sm-2" for="email"><sup style="color: #ff0709;font-size: 1em">*</sup>বর্তমান পদবী :</label>

                                                <div class="col-sm-10 ">
                                                    <select name="recent_status" ng-model="recent_status"
                                                            class="form-control" id="sell">
                                                        <option value="">--পদবী নির্বাচন করুন--</option>
                                                        <option ng-repeat="r in rank" value="[[r.id]]">
                                                            [[r.name_bng]]
                                                        </option>
                                                    </select>
                                                    <span style="color:red"
                                                          ng-show="formSubmitResult.error.recent_status[0]">[[ formSubmitResult.error.recent_status[0] ]]</span>
                                                </div>

                                            </div>
                                            {{--End Ansar Rank Field --}}
                                            {{--Start Ansar Father Name (English) Field --}}
                                            <div class="form group col-md-12 "
                                                 ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.father_name_eng[0]}">
                                                <label class="control-label col-sm-2" for="email"><sup style="color: #ff0709;font-size: 1em">*</sup>Father's name:</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control" id="father_name_eng"
                                                           name="father_name_eng" ng-model="father_name_eng" type="text"
                                                           placeholder="Father's name"
                                                           value="{{Request::old('father_name_eng')}}">
                                                    <span style="color:red"
                                                          ng-show="formSubmitResult.error.father_name_eng[0]">[[ formSubmitResult.error.father_name_eng[0] ]]</span>
                                                </div>
                                            </div>
                                            {{--End Ansar Father Name (English) Field --}}
                                            {{--Start Ansar Father Name (Bangla) Field --}}
                                            <div class="form group col-md-12 "
                                                 ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.father_name_bng[0]}">
                                                <label class="control-label col-sm-2" for="email"><sup style="color: #ff0709;font-size: 1em">*</sup>পিতার নাম</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control " id="father_name_bng"
                                                           name="father_name_bng" ng-model="father_name_bng" type="text"
                                                           placeholder="পিতার নাম"
                                                           value="{{Request::old('father_name_bng')}}">
                                                    <span style="color:red"
                                                          ng-show="formSubmitResult.error.father_name_bng[0]">[[ formSubmitResult.error.father_name_bng[0] ]]</span>
                                                </div>
                                            </div>
                                            {{--End Ansar Father Name (Bangla) Field --}}
                                            {{--Start Ansar Mother Name (English) Field --}}
                                            <div class="form group col-md-12 "
                                                 ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.mother_name_eng[0]}">
                                                <label class="control-label col-sm-2" for="email"><sup style="color: #ff0709;font-size: 1em">*</sup>Mother's Name</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control  " id="mother_name_eng"
                                                           name="mother_name_eng" ng-model="mother_name_eng" type="text"
                                                           placeholder=" Mother's Name"
                                                           value="{{Request::old('mother_name_eng')}}">
                                                    <span style="color:red"
                                                          ng-show="formSubmitResult.error.mother_name_eng[0]">[[ formSubmitResult.error.mother_name_eng[0] ]]</span>
                                                </div>
                                            </div>
                                            {{--End Ansar Mother Name (English) Field --}}
                                            {{--Start Ansar Mother Name (Bangla) Field --}}
                                            <div class="form group col-md-12 "
                                                 ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.mother_name_bng[0]}">
                                                <label class="control-label col-sm-2" for="email"><sup style="color: #ff0709;font-size: 1em">*</sup>মাতার নাম</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control  " id="mother_name_bng"
                                                           name="mother_name_bng" ng-model="mother_name_bng" type="text"
                                                           placeholder="মাতার নাম"
                                                           value="{{Request::old('mother_name_bng')}}">
                                                    <span style="color:red"
                                                          ng-show="formSubmitResult.error.mother_name_bng[0]">[[ formSubmitResult.error.mother_name_bng[0] ]]</span>
                                                </div>

                                            </div>
                                            {{--End Ansar Mother Name (Bangla) Field --}}
                                            {{--Start Ansar Date of Birth Field --}}
                                            <div class="form group col-md-12 "
                                              $scope.loadPagination();   ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.data_of_birth[0]}">
                                                <label class="control-label col-sm-2" for="email"><sup style="color: #ff0709;font-size: 1em">*</sup>Date of birth</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control picker " id="data_of_birth" name="data_of_birth" ng-model="data_of_birth" date-picker-dir="datePickerDir" placeholder="Date of birth" value="{{Request::old('data_of_birth')}}">
                                                    <span style="color:red" ng-show="formSubmitResult.error.data_of_birth[0]">[[ formSubmitResult.error.data_of_birth[0] ]]</span>
                                                </div>
                                            </div>
                                            {{--End Ansar Date of Birth Field --}}
                                            {{--Start Ansar Married Status Field --}}
                                            <div class="form-horizontal col-md-12 "
                                                 ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.marital_status[0]}">

                                                <label class="control-label col-sm-2" for="email"><sup style="color: #ff0709;font-size: 1em">*</sup>Marital
                                                    status</label>

                                                <div class="col-sm-10 ">
                                                    <select name="marital_status" ng-model="marital_status"
                                                            class="form-control" id="sell">
                                                        <option value="">--Select your marital condition--</option>
                                                        <option value="Married"
                                                                @if(Request::old('marital_status')=='Married') selected @endif>
                                                            Married
                                                        </option>
                                                        <option value="Unmarried"
                                                                @if(Request::old('marital_status')=='Unmarried') selected @endif>
                                                            Unmarried
                                                        </option>
                                                        <option value="Divorced">Divorced</option>
                                                    </select>
                                                    <span style="color:red"
                                                          ng-show="formSubmitResult.error.marital_status[0]">[[ formSubmitResult.error.marital_status[0] ]]</span>
                                                </div>

                                            </div>
                                            {{--End Ansar Married Status Field --}}

                                            <div class="form group col-md-12 ">
                                                <label class="control-label col-sm-2" for="email">Spouse Name:</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control  " id="spouse_name_eng"
                                                           name="spouse_name_eng" ng-model="spouse_name_eng" type="text"
                                                           placeholder="Spouse Name"
                                                           value="{{Request::old('spouse_name_eng')}}">

                                                </div>

                                            </div>
                                            <div class="form group col-md-12 ">
                                                <label class="control-label col-sm-2" for="email">স্ত্রী/স্বামীর নাম</label>
                                                <div class="col-sm-10">
                                                    <input class="form-control  " id="spouse_name_bng" name="spouse_name_bng" ng-model="spouse_name_bng" type="text" placeholder=" স্ত্রী/স্বামীর নাম " value="{{Request::old('spouse_name_bng')}}">
                                                </div>

                                            </div>
                                            {{--Start Ansar National Id Field --}}
                                            <div class="form group col-md-12 "
                                                 ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.national_id_no[0]}">
                                                <label class="control-label col-sm-2" for="email"><sup style="color: #ff0709;font-size: 1em">*</sup>National Id no</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control  " id="national_id_no"
                                                           name="national_id_no" ng-model="national_id_no" type="text"
                                                           placeholder="National Id no(Numeric 17 digit for 13 digit add birth year before id no.)"
                                                           value="{{Request::old('national_id_no')}}">
                                                    <span style="color:red"
                                                          ng-show="formSubmitResult.error.national_id_no[0]">[[ formSubmitResult.error.national_id_no[0] ]]</span>
                                                </div>

                                            </div>
                                            {{--End Ansar National Id Field --}}
                                            <div class="form group col-md-12 ">
                                                <label class="control-label col-sm-2" for="email">Birth Certificate no</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control  " id="birth_certificate_no" name="birth_certificate_no" ng-model="birth_certificate_no" type="text" placeholder="Birth Certificate no" value="{{Request::old('birth_certificate_no')}}">
                                                </div>
                                            </div>

                                            <div class="form-horizontal col-md-12 ">
                                                <label class="control-label col-sm-2" for="email">দীর্ঘ মেয়াদি অসুখ</label>

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
                                                           ng-model="my_disease" type="text"
                                                           placeholder="আপনার অসুখের নাম লিখুন">
                                                </div>
                                            </div>

                                            <div class="form-horizontal col-md-12 ">
                                                <label class="control-label col-sm-2" for="email">নির্দিষ্ট দক্ষতা</label>

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
                                                    <input class="form-control" id="own_particular_skill"
                                                           name="own_particular_skill" ng-model="my_skill" type="text"
                                                           required placeholder="আপনার দক্ষতা লিখুন">
                                                </div>
                                            </div>

                                            <div class="form group col-md-12">
                                                <label class="control-label col-sm-2" for="criminal">Criminal
                                                    Case:</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control"  name="criminal_case" ng-model="criminal_case" type="text" placeholder="Criminal case" value="{{Request::old('criminal_case')}}">
                                                </div>
                                            </div>
                                            <div class="form group col-md-12">
                                                <label class="control-label col-sm-2" for="criminal">ফৌজদারি মামলা:</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control" name="criminal_case_bng" type="text" placeholder="ফৌজদারি মামলা" value="{{Request::old('criminal_case')}}">
                                                </div>
                                            </div>
                                            {{--<div class="form group col-md-12 hiddden">--}}
                                                {{--<label class="control-label col-sm-2" for="criminal">সর্বশেষ প্রশিক্ষন সনদ নং:</label>--}}
                                                {{--<div class="col-sm-10">--}}
                                                    {{--<input class="form-control  " id="certificate_no" name="certificate_no" type="text" placeholder="সর্বশেষ প্রশিক্ষন সনদ নং" value="{{Request::old('certificate_no')}}">--}}
                                                {{--</div>--}}
                                            {{--</div>--}}
                                        </div>
                                    </div>

                                </fieldset>
                                <fieldset>
                                    <div class="level-title-session-entry">
                                        <h5 style="text-align: center;">স্থায়ী ঠিকানা</h5>
                                    </div>
                                    <div class="box box-info">
                                        <div class="box-body">
                                            <div class="form-horizontal col-md-12">
                                                <label class="control-label col-sm-2" for="email">Village/House No:</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control  " id="village_name" name="village_name" ng-model="village_name" type="text" placeholder=" Village" value="{{Request::old('village_name')}}">
                                                </div>
                                            </div>

                                            <div class="form-horizontal col-md-12">
                                                <label class="control-label col-sm-2" for="email">গ্রাম/বাড়ি নং:</label>
                                                <div class="col-sm-10">
                                                    <input class="form-control  " id="village_name_bng" name="village_name_bng" ng-model="village_name_bng" type="text" placeholder=" গ্রাম" value="{{Request::old('village_name_bng')}}">
                                                </div>

                                            </div>
                                            <div class="form-horizontal col-md-12">
                                                <label class="control-label col-sm-2" for="email">Road No:</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control  " id="road_no" name="road_no" ng-model="road_no" type="text" placeholder=" Road no" value="{{Request::old('road_no')}}">
                                                </div>
                                            </div>
                                            <div class="form-horizontal col-md-12">
                                                <label class="control-label col-sm-2" for="email">Post office:</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control  " id="post_office_name" name="post_office_name" ng-model="post_office_name" type="text" placeholder=" Post Office Name " value="{{Request::old('post_office_name')}}">
                                                </div>

                                            </div>
                                            <div class="form-horizontal col-md-12">
                                                <label class="control-label col-sm-2" for="email">ডাকঘর:</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control  " id="post_office_name_bng" name="post_office_name_bng" ng-model="post_office_name_bng" type="text" placeholder=" ডাকঘর " value="{{Request::old('post_office_name_bng')}}">
                                                </div>

                                            </div>
                                            <div class="form group col-md-12">
                                                <label class="control-label col-sm-2" for="email">Union/Word:</label>
                                                <div class="col-sm-10">
                                                    <input class="form-control  " id="union_name_eng" name="union_name_eng" ng-model="union_name_eng" type="text" placeholder=" Union Name" value="{{Request::old('union_name_eng')}}">
                                                </div>
                                            </div>
                                            <div class="form group col-md-12">
                                                <label class="control-label col-sm-2" for="email">ইউনিয়ন নাম/ওয়ার্ড:</label>
                                                <div class="col-sm-10">
                                                    <input class="form-control" id="union_name_bng" name="union_name_bng" ng-model="union_name_bng" type="text" placeholder="ইউনিয়ন নাম" value="{{Request::old('union_name_bng')}}">
                                                </div>
                                            </div>
                                            <div class="form-horizontal col-md-12 " ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.division_name_eng[0]}">
                                                <label class="control-label col-sm-2" for="email"><sup style="color: #ff0709;font-size: 1em">*</sup>বিভাগ</label>
                                                <div class="col-sm-10 ">
                                                    <select name="division_name_eng" class="form-control" id="sell" ng-model="SelectedDivision" ng-change="SelectedItemChanged()">
                                                        <option value="">--বিভাগ নির্বাচন করুন--</option>
                                                        <option ng-repeat="x in division" ng-selected="x.id=='{{Request::old('division_name_eng')}}'" value="[[x.id]]">[[x.division_name_bng]]</option>
                                                    </select>
                                                    <span style="color:red" ng-show="formSubmitResult.error.division_name_eng[0]">[[ formSubmitResult.error.division_name_eng[0] ]]</span>
                                                </div>

                                            </div>
                                            <div class="form-horizontal col-md-12 " ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.unit_name_eng[0]}">
                                                <label class="control-label col-sm-2" for="email"><sup style="color: #ff0709;font-size: 1em">*</sup>জেলা</label>
                                                <div class="col-sm-10 ">
                                                    <select name="unit_name_eng" class="form-control" id="sell" ng-model="SelectedDistrict" ng-change="SelectedDistrictChanged()">
                                                        <option value="">--জেলা নির্বাচন করুন--</option>
                                                        <option ng-repeat="x in district" ng-selected="x.id=='{{Request::old('unit_name_eng')}}'" value="[[x.id]]">[[ x.unit_name_bng ]]
                                                        </option>
                                                    </select>
                                                    <span style="color:red" ng-show="formSubmitResult.error.unit_name_eng[0]">[[ formSubmitResult.error.unit_name_eng[0] ]]</span>
                                                </div>

                                            </div>
                                            <div class="form-horizontal col-md-12 " ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.thana_name_eng[0]}">
                                                <label class="control-label col-sm-2" for="email"><sup style="color: #ff0709;font-size: 1em">*</sup>থানা</label>
                                                <div class="col-sm-10 ">
                                                    <select name="thana_name_eng" class="form-control" id="sell" ng-model="ThanaModel" ng-change="SelectedThanaChanged()">
                                                        <option value="">--থানা নির্বাচন করুন--</option>
                                                        <option ng-repeat="x in thana" ng-selected="x.id=='{{Request::old('thana_name_eng')}}'" value="[[x.id]]">[[ x.thana_name_bng ]]</option>
                                                    </select>
                                                    <span style="color:red" ng-show="formSubmitResult.error.thana_name_eng[0]">[[ formSubmitResult.error.thana_name_eng[0] ]]</span>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </fieldset>
                                <fieldset>


                                    <div class="level-title-session-entry">
                                        <h5 style="text-align: center;">শারীরিক যোগ্যতার তথ্য</h5>
                                    </div>
                                    <div class="box box-info">
                                        <div class="box-body">
                                            <div class="form-horizontal col-md-12 " ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.hight_feet[0]}">
                                                <label class="control-label col-sm-2" for="email"><sup style="color: #ff0709;font-size: 1em">*</sup>Height</label>
                                                <div class="col-sm-5">
                                                    <input class="form-control  " id="hight_feet" name="hight_feet" ng-model="hight_feet" type="text" placeholder=" FEET" value="{{Request::old('hight_feet')}}">
                                                    <span style="color:red" ng-show="formSubmitResult.error.hight_feet[0]">[[ formSubmitResult.error.hight_feet[0] ]]</span>
                                                </div>
                                                <div class="col-sm-5">
                                                    <input class="form-control  " id="hight_inch" name="hight_inch" ng-model="hight_inch" type="text" placeholder=" INCHES" value="{{Request::old('hight_inch')}}">
                                                </div>
                                            </div>
                                            <div class="form-horizontal col-md-12 " ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.ansar_name_eng[0]}">
                                                <label class="control-label col-sm-2" for="email"><sup style="color: #ff0709;font-size: 1em">*</sup>রক্তের গ্রুপ</label>
                                                <div class="col-sm-10 ">
                                                    <select name="blood_group_name_bng" class="form-control" id="sell" ng-model="BloodModel">
                                                        <option value="">--রক্তের গ্রুপ নির্বাচন করুন--</option>
                                                        <option ng-repeat="x in blood" ng-selected="x.id=='{{Request::old('blood_group_name_bng')}}'" value="[[x.id]]">[[ x.blood_group_name_bng ]]</option>
                                                    </select>
                                                    <span style="color:red" ng-show="formSubmitResult.error.blood_group_name_bng[0]">[[ formSubmitResult.error.blood_group_name_bng[0] ]]</span>
                                                </div>

                                            </div>


                                            <div class="form-horizontal col-md-12">
                                                <label class="control-label col-sm-2" for="email">Eye color:</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control  " id="eye_color" name="eye_color" ng-model="eye_color" type="text" placeholder="Eye color" value="{{Request::old('eye_color')}}">
                                                </div>
                                            </div>
                                            <div class="form-horizontal col-md-12">
                                                <label class="control-label col-sm-2" for="email">চোখের রং:</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control  " id="eye_color" name="eye_color_bng" ng-model="eye_color_bng" type="text" placeholder="চোখের রং" value="{{Request::old('eye_color_bng')}}">
                                                </div>
                                            </div>
                                            <div class="form group col-md-12">
                                                <label class="control-label col-sm-2" for="email">Skin color:</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control  " id="skin_color" name="skin_color" ng-model="skin_color" type="text" placeholder="Skin color" value="{{Request::old('skin_color')}}">
                                                </div>
                                            </div>
                                            <div class="form group col-md-12">
                                                <label class="control-label col-sm-2" for="skin_color_bng">গায়ের রং:</label>
                                                <div class="col-sm-10">
                                                    <input class="form-control  " id="skin_color_bng" name="skin_color_bng" ng-model="skin_color_bng" type="text" placeholder="গায়ের রং" value="{{Request::old('skin_color_bng')}}">
                                                </div>
                                            </div>

                                            <div class="form-horizontal col-md-12 " ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.sex[0]}">
                                                <label class="control-label col-sm-2" for="email"><sup style="color: #ff0709;font-size: 1em">*</sup>Gender</label>
                                                <div class="col-sm-10 ">
                                                    <select name="sex" ng-model="sex" class="form-control" id="sell">
                                                        <option value="">--Select an option--</option>
                                                        <option value="Male"
                                                                @if(Request::old('sex')=='Male') selected @endif>Male
                                                        </option>
                                                        <option value="Female"
                                                                @if(Request::old('sex')=='Female') selected @endif>Female
                                                        </option>
                                                        <option value="Other"
                                                                @if(Request::old('sex')=='Other') selected @endif>Other
                                                        </option>
                                                    </select>
                                                    <span style="color:red" ng-show="formSubmitResult.error.sex[0]">[[ formSubmitResult.error.sex[0] ]]</span>
                                                </div>

                                            </div>

                                            <div class="form group col-md-12">
                                                <label class="control-label col-sm-2" for="email">Identification mark:</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control  " id="identification_mark" name="identification_mark" ng-model="identification_mark" type="text" placeholder="Identification mark" value="{{Request::old('identification_mark')}}">
                                                </div>
                                            </div>
                                            <div class="form group col-md-12">
                                                <label class="control-label col-sm-2" for="email">সনাক্তকরন চিহ্ন:</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control  " id="identification_mark_bng" name="identification_mark_bng" ng-model="identification_mark_bng" type="text" placeholder="সনাক্তকরন চিহ্ন" value="{{Request::old('identification_mark')}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                <input type="hidden" name="ansar_id">

                                <fieldset>
                                    <div class="level-title-session-entry">
                                        <h5 style="text-align: center;">Educational Information</h5>
                                    </div>
                                    <div class="box box-info">
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
                                                        <td>
                                                            <select ng-model="ppp.educationIdBng[$index]"
                                                                    ng-change="eduEngChange($index)">
                                                                <option value="">--Select an option--</option>
                                                                <option ng-repeat="r in ppp.educationName"
                                                                        value="[[r.id]]">[[r.education_deg_eng]]
                                                                </option>
                                                            </select>
                                                        </td>
                                                        <td ng-repeat="r in row">
                                                            <input type="text" name="[[r.name]]" placeholder="[[ r.text ]]" value="[[r.value]]">
                                                        </td>
                                                        <td>
                                                            <a href="" ng-click="(eduRows.length > 1)?eduDeleteRows($index):''"><i class="glyphicon glyphicon-trash"></i></a>
                                                        </td>
                                                    </tr>
                                                    <tr >
                                                        <td style=" border-top:0px;background: #ffffff !important;">
                                                            <a href="">
                                                                <p ng-click="addEduinput()" style="cursor: hand;padding: .2em .5em;background-color: #5cb85c;display: inline-block;color:#ffffff">Add more</p>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                </table>


                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                <fieldset>
                                    <div class="level-title-session-entry">
                                        <h5 style="text-align: center;">শিক্ষাগত যোগ্যতার তথ্য</h5>
                                    </div>
                                    <div class="box box-info">
                                        <div class="box-body">
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <tr>
                                                        <th>শিক্ষাগত যোগ্যতা</th>
                                                        <th>শিক্ষা প্রতিষ্ঠানের নাম</th>
                                                        <th>পাশ করার সাল</th>
                                                        <th>বিভাগ / শ্রেণী</th>
                                                        <th>Action</th>
                                                    </tr>
                                                    <tr ng-repeat="row in eduRows">
                                                        <td>
                                                            <select name="educationIdBng[]"
                                                                    ng-model="ppp.educationIdBng[$index]">
                                                                <option value="">--অপশন নির্বাচন করুন--</option>
                                                                <option ng-repeat="r in ppp.educationName"
                                                                        value="[[r.id]]">[[r.education_deg_bng]]
                                                                </option>
                                                            </select>
                                                        </td>
                                                        <td ng-repeat="r in row">
                                                            <input type="text" name="[[r.name]]"
                                                                   placeholder="[[r.text]]"
                                                                   value="[[r.value]]">
                                                        </td>

                                                        <td>
                                                            <a href=""
                                                               ng-click="(eduRows.length > 1)?eduDeleteRows($index):''"><i
                                                                        class="glyphicon glyphicon-trash"></i></a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style=" border-top:0px;background: #ffffff !important;">
                                                            <a href=""><p ng-click="addEduinput()"
                                                                          style="cursor: hand;padding: .2em .5em;background-color: #5cb85c;display: inline-block;color:#ffffff">
                                                                    Add more</p></a>
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
                                    <div class="box box-info">
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
                                                                <option ng-repeat="r in rank" value="[[r.name_eng]]">
                                                                    [[r.code]]
                                                                </option>
                                                            </select>
                                                            <input  ng-if="r.type!='dropdown'"  style="line-height: 18px;" type="[[r.type]]" name="[[r.name]]" date-picker-dir="[[r.class_name]]" placeholder="[[ r.text ]]" value="[[r.value]]"></td>
                                                        <td><a href=""
                                                               ng-click="(trainingEngRows.length > 1)?deleteTrainingRows($index):''"><i
                                                                        class="glyphicon glyphicon-trash"></i></a></td>
                                                    </tr>
                                                    <tr>
                                                        <td style=" border-top:0px;background: #ffffff !important;">
                                                            <a href=""><p ng-click="addTraininput()"
                                                                          style="cursor: hand;padding: .2em .5em;background-color: #5cb85c;display: inline-block;color:#ffffff">
                                                                    Add more</p></a>
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
                                    <div class="box box-info">
                                        <div class="box-body">
                                            <div class=" table-responsive">
                                                <table class="table">
                                                    <tr>
                                                        <th>পদবী</th>
                                                        <th>প্রতিষ্ঠান</th>
                                                        <th>প্রশিক্ষন শুরুর তারিখ</th>
                                                        <th>প্রশিক্ষন শেষের তারিখ</th>
                                                        <th>সনদ নং</th>
                                                        <th>Action</th>
                                                    </tr>


                                                    <tr ng-repeat="row in trainingRows">
                                                        <td ng-repeat="r in row">
                                                            <select ng-if="r.type=='dropdown'" name="[[r.name]]">
                                                                <option value="">--পদবী নির্বাচন করুন--</option>
                                                                <option ng-repeat="r in rank" value="[[r.name_bng]]">
                                                                    [[r.name_bng]]
                                                                </option>
                                                            </select>
                                                            <input ng-if="r.type!='dropdown'" style="line-height: 18px;"
                                                                   type="[[r.type]]"
                                                                   name="[[r.name]]"
                                                                   placeholder="[[r.text]]"
                                                                   date-picker-dir="[[r.class_name]]"
                                                                   value="[[r.value]]"></td>
                                                        <td><a href=""
                                                               ng-click="(trainingRows.length > 1)?deleteTrainingRows($index):''"><i
                                                                        class="glyphicon glyphicon-trash"></i></a></td>
                                                    </tr>
                                                    <tr>
                                                        <td style=" border-top:0px;background: #ffffff !important;">
                                                            <a href=""><p ng-click="addTraininput()"
                                                                          style="cursor: hand;padding: .2em .5em;background-color: #5cb85c;display: inline-block;color:#ffffff">
                                                                    Add more</p></a>
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
                                    <div class="box box-info">
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
                                                        <td style=" border-top:0px;background:#ffffff !important;">
                                                            <a href=""><p ng-click="addNomineeinput()"
                                                                          style="cursor: hand;padding: .2em .5em;background-color: #5cb85c;display: inline-block;color:#ffffff">
                                                                    Add more</p></a>
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
                                    <div class="box box-info">
                                        <div class="box-body">
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <tr>
                                                        <th>নাম</th>
                                                        <th>সম্পর্ক</th>
                                                        <th>শতকরা(%)</th>
                                                        <th>মোবাইল নং</th>
                                                        <th>Action</th>
                                                    </tr>
                                                    <tr ng-repeat="row in nomineeRows">
                                                        <td ng-repeat="r in row"><input type="text" name="[[r.name]]"
                                                                                        placeholder="[[r.text]]"
                                                                                        value="[[r.value]]"></td>
                                                        <td><a href=""
                                                               ng-click="(nomineeRows.length > 1)?deleteNomineeRows($index):''"><i
                                                                        class="glyphicon glyphicon-trash"></i></a></td>
                                                    </tr>
                                                    <tr>
                                                        <td style=" border-top:0px;background: #ffffff !important;">
                                                            <a href=""><p ng-click="addNomineeinput()"
                                                                          style="cursor: hand;padding: .2em .5em;background-color: #5cb85c;display: inline-block;color:#ffffff">
                                                                    Add more</p></a>
                                                        </td>
                                                    </tr>
                                                </table>


                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                                {{--Other Information Field--}}
                                <fieldset>
                                    <div class="level-title-session-entry">
                                        <h5 style="text-align: center;">অন্যান্য তথ্য</h5>
                                    </div>
                                    <div class="box box-info">
                                        <div class="box-body">
                                            <div class="form-horizontal col-md-12 "
                                                 ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.mobile_no_self[0]}">
                                                <label class="control-label col-sm-2" for="email"><sup style="color: #ff0709;font-size: 1em">*</sup>Mobile
                                                    no(Self)</label>

                                                <div class="col-sm-10">
                                                    <div class="input-group">
                                                        <span class="input-group-addon">+88</span>
                                                        <input class="form-control  " id="mobile_no_self" name="mobile_no_self" ng-model="mobile_no_self" ng-change="mobile_no_self=mobile_no_self.length>11?mobile_no_self.substring(0,11):mobile_no_self" type="text" placeholder="Mobile no(Self)" value="{{Request::old('mobile_no_self')}}">
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
                                                           name="mobile_no_request" ng-model="mobile_no_request"
                                                           type="text" placeholder="Mobile no(Request)"
                                                           value="{{Request::old('mobile_no_request')}}">
                                                </div>

                                            </div>
                                            <div class="form-horizontal col-md-12">
                                                <label class="control-label col-sm-2" for="email">Land phone
                                                    no(self):</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control  " id="land_phone_self"
                                                           name="land_phone_self" ng-model="land_phone_self" type="text"
                                                           placeholder="Land phone no(self)"
                                                           value="{{Request::old('land_phone_self')}}">
                                                </div>
                                            </div>

                                            <div class="form group col-md-12">
                                                <label class="control-label col-sm-2" for="email">Land phone
                                                    no(request):</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control  " id="land_phone_request" name="land_phone_request" ng-model="land_phone_request" type="text" placeholder="Land phone no(request)" value="{{Request::old('land_phone_request')}}">
                                                </div>
                                            </div>

                                            <div class="form-horizontal col-md-12 ">
                                                <label class="control-label col-sm-2" for="email">Email(Self)</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control  " id="email_self" name="email_self" ng-model="email_self" type="email" placeholder="Email(Self)" value="{{Request::old('email_self')}}">
                                                </div>
                                            </div>
                                            <div class="form-horizontal col-md-12">
                                                <label class="control-label col-sm-2" for="email">Email(Request):</label>
                                                <div class="col-sm-10">
                                                    <input class="form-control  " id="email_request" name="email_request" ng-model="email_request" type="email" placeholder="Email(Request)" value="{{Request::old('email_request')}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                                {{--All Photos Field--}}
                                <fieldset>
                                    <div class="level-title-session-entry">
                                        <h5 style="text-align: center;">ছবি সমূহ</h5>
                                    </div>
                                    <div class="box box-info">
                                        <div class="box-body">
                                            <div class=" table-responsive">
                                                <table class="table">
                                                    <tr>
                                                        <th>Profile picture</th>
                                                        <th>Sign image</th>
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
                                {{--Draft Save Button--}}
                                <div class="row" style="margin: 0 !important;">
                                    <div class="form-horizontal pull-left">
                                        <button form-submit id="submit" type="submit" name="submit" class="btn btn-primary"
                                               value="1">Submit</button>
                                    </div>
                                    {{--Form Submit Button--}}
                                    <div class="form-horizontal pull-right">
                                        <button form-submit id="submit1" type="submit" name="Save" class="btn btn-primary"
                                               value="0">Save as draft</button>
                                        <!--<button name="save" type="save">Save</button>-->
                                    </div>
                                </div>

                            </form>


                        </div>

                    </div>
                </div>
                {{--<div ng-bind-html="error"></div>--}}
            </section>
        </div>
    </div>
@stop