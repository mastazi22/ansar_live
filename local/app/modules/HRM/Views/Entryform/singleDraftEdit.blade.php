@extends('template.master')
@section('title','Edit Draft')
@section('breadcrumb')
    {!! Breadcrumbs::render('draft_edit',$data) !!}
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
                    scope.$watch('draft', function (n, o) {
                        if (attrs.datepicker({                dateFormat:'dd-M-yy'            })Dir && Object.keys(n).length > 0)$(element).datepicker({                dateFormat:'dd-M-yy'            })({
                            defaultValue:attrs.value
                        });
                    })

                }

            }

        })
        GlobalApp.controller('singleDraftController', function ($scope, getDraftService, getDiseaseSkillService, getBloodService, getNameService, $sce, $http) {
            $scope.recent = '{{$data}}';
            $scope.BloodModel = "";
            $scope.SelectedDistrict = "";
            $scope.ThanaModel = "";
            $scope.eduRows = [];
            $scope.eduEngRows = [];
            $scope.training = [];
            $scope.trainingRows = [];
            $scope.trainingEngRows = [];
            $scope.nomineeRows = [];
            $scope.nomineeEngRows = [];
            $scope.formSubmitResult = {};
            $scope.error = $sce.trustAsHtml("");
            $scope.ppp = {educationIdBng: []};
            $scope.draft = {};
            $scope.rank = [];
            $scope.isAdmin = parseInt('{{Auth::user()->type}}');
            $http({
                url: '{{URL::route('ansar_rank')}}',
                method: 'get'
            }).then(function (response) {
                $scope.rank = response.data;
            })


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

            getDraftService.getSingleDraftValues($scope.recent).then(function (response) {
                $scope.draft = response.data;
                $scope.sex = $scope.draft.sex;
                $scope.recent_status = $scope.draft.recent_status;
                $scope.marital_status = $scope.draft.marital_status;
                $scope.BloodModel = $scope.draft.blood_group_name_bng;
                $scope.long_term_disease = $scope.draft.long_term_disease;
//            alert($scope.long_term_disease);

                if ($scope.long_term_disease == 1)
                    $scope.own_disease = true;

                $scope.particular_skill = $scope.draft.particular_skill;

                if ($scope.particular_skill == 1)
                    $scope.own_particular_skill = true;

                $scope.SelectedDivision = $scope.draft.division_name_eng;
                $scope.SelectedItemChanged();
                $scope.SelectedDistrict = $scope.draft.unit_name_eng;
                $scope.SelectedDistrictChanged();
                $scope.ThanaModel = $scope.draft.thana_name_eng;
//            alert(JSON.stringify($scope.draft));
                $scope.eduLength = $scope.draft.educationIdBng.length;
                $scope.trainLength = $scope.draft.training_designation.length;
                $scope.nomineeLength = $scope.draft.nominee_name.length;
                $scope.addNomineeinput();
                $scope.addTraininput();
                $scope.addEduinput();
//            alert($scope.nomineeLength);
                console.log($scope.draft);

            });

            getDiseaseSkillService.getAllDisease().then(function (response) {
                $scope.Disease = response.data;
            });
            getDiseaseSkillService.getAllSkill().then(function (response) {
                $scope.skillName = response.data;
            });
            getDiseaseSkillService.getAllEducationName().then(function (response) {
                $scope.ppp.educationName = response.data;
            });
            getBloodService.getAllBloodName().then(function (response) {
                $scope.blood = response.data;
            })
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

                if ($scope.eduLength > 0) {

                    $scope.draft.educationIdBng.forEach(function (c, index) {
                        $scope.ppp.educationIdBng.push($scope.draft.educationIdBng[index]);

                        $scope.eduRows.push([
                            {
                                text: "শিক্ষা প্রতিষ্ঠানের নাম",
                                value: $scope.draft.institute_name[index],
                                name: 'institute_name[]'
                            },
                            {text: "Passing year", value: $scope.draft.passing_year[index], name: 'passing_year[]'},
                            {
                                text: "বিভাগ / শ্রেণী",
                                value: $scope.draft.gade_divission[index],
                                name: 'gade_divission[]'
                            },
                        ]);

                        $scope.eduEngRows.push([
                            {
                                text: "Institute Name",
                                value: $scope.draft.institute_name_eng[index],
                                name: 'institute_name_eng[]'
                            },
                            {
                                text: "Passing year",
                                value: $scope.draft.passing_year_eng[index],
                                name: 'passing_year_eng[]'
                            },
                            {
                                text: "Class",
                                value: $scope.draft.gade_divission_eng[index],
                                name: 'gade_divission_eng[]'
                            },
                        ]);
                    })

                }

                else {
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
                }
                $scope.eduLength = 0;
            };


            $scope.eduDeleteRows = function (index) {
                $scope.eduRows.splice(index, 1);
                $scope.eduEngRows.splice(index, 1);
            }
            $scope.addTraininput = function () {

                if ($scope.trainLength > 0) {
                    $scope.draft.training_designation.forEach(function (c, index) {
                        $scope.trainingRows.push([
                            {
                                text: "পদবী",
                                value: $scope.draft.training_designation[index],
                                name: 'training_designation[]',
                                type: 'dropdown',
                                class_name: ""
                            },
                            {
                                text: "প্রতিষ্ঠান",
                                value: $scope.draft.institute[index],
                                name: 'institute[]',
                                type: 'text',
                                class_name: ""
                            },
                            {
                                text: "Training start date",
                                value: $scope.draft.training_start[index],
                                name: 'training_start[]',
                                type: '',
                                class_name: "datePickerDir"
                            },
                            {
                                text: "Training end date",
                                value: $scope.draft.training_end[index],
                                name: 'training_end[]',
                                type: '',
                                class_name: "datePickerDir"
                            },
                            {
                                text: "আপ/আম নম্বর",
                                value: $scope.draft.training_sanad[index],
                                name: 'training_sanad[]',
                                type: 'text',
                                class_name: ""
                            }

                        ]);
                        $scope.trainingEngRows.push([
                            {
                                text: "পদবী",
                                value: $scope.draft.training_designation_eng[index],
                                name: 'training_designation_eng[]',
                                type: 'dropdown',
                                class_name: ""
                            },
                            {
                                text: "প্রতিষ্ঠান",
                                value: $scope.draft.institute_eng[index],
                                name: 'institute_eng[]',
                                type: 'text',
                                class_name: ""
                            },
                            {
                                text: "Training start date",
                                value: $scope.draft.training_start_eng[index],
                                name: 'training_start_eng[]',
                                type: '',
                                class_name: "datePickerDir"
                            },
                            {
                                text: "Training end date",
                                value: $scope.draft.training_end_eng[index],
                                name: 'training_end_eng[]',
                                type: '',
                                class_name: "datePickerDir"
                            },
                            {
                                text: "আপ/আম নম্বর",
                                value: $scope.draft.training_sanad_eng[index],
                                name: 'training_sanad_eng[]',
                                type: 'text',
                                class_name: ""
                            }
                        ]);

                    })
                }
                else {
                    $scope.trainingRows.push([
                        {text: "পদবী", value: '', name: 'training_designation[]', type: 'dropdown', class_name: ""},
                        {text: "প্রতিষ্ঠান", value: '', name: 'institute[]', type: 'text', class_name: ""},
                        {
                            text: "Training start date",
                            value: '',
                            name: 'training_start[]',
                            type: '',
                            class_name: "datePickerDir"
                        },
                        {
                            text: "Training end date",
                            value: '',
                            name: 'training_end[]',
                            type: '',
                            class_name: "datePickerDir"
                        },
                        {text: "আপ/আম নম্বর", value: '', name: 'training_sanad[]', type: 'text', class_name: ""}

                    ]);
                    $scope.trainingEngRows.push([
                        {
                            text: "পদবী",
                            value: '',
                            name: 'training_designation_eng[]',
                            type: 'dropdown',
                            class_name: "datePickerDir"
                        },
                        {
                            text: "প্রতিষ্ঠান",
                            value: '',
                            name: 'institute_eng[]',
                            type: 'text',
                            class_name: "datePickerDir"
                        },
                        {
                            text: "Training start date",
                            value: '',
                            name: 'training_start_eng[]',
                            type: '',
                            class_name: "datePickerDir"
                        },
                        {
                            text: "Training end date",
                            value: '',
                            name: 'training_end_eng[]',
                            type: '',
                            class_name: "datePickerDir"
                        },
                        {
                            text: "আপ/আম নম্বর",
                            value: '',
                            name: 'training_sanad_eng[]',
                            type: 'text',
                            class_name: "datePickerDir"
                        }
                    ]);
                }
                $scope.trainLength = 0;
            };


            $scope.deleteTrainingRows = function (index) {
                $scope.trainingRows.splice(index, 1);
                $scope.trainingEngRows.splice(index, 1)
            }
            $scope.addNomineeinput = function () {

                if ($scope.nomineeLength > 0) {
                    $scope.draft.nominee_name.forEach(function (c, index) {
                        $scope.nomineeRows.push([
                            {text: "নাম", value: $scope.draft.nominee_name[index], name: 'nominee_name[]'},
                            {text: "সম্পর্ক", value: $scope.draft.relation[index], name: 'relation[]'},
                            {text: "শতকরা", value: $scope.draft.percentage[index], name: 'percentage[]'},
                            {text: "মোবাইল নং", value: $scope.draft.nominee_mobile[index], name: 'nominee_mobile[]'}
                        ]);
                        $scope.nomineeEngRows.push([
                            {text: "Name", value: $scope.draft.nominee_name_eng[index], name: 'nominee_name_eng[]'},
                            {text: "Relation", value: $scope.draft.relation_eng[index], name: 'relation_eng[]'},
                            {text: "Percentage", value: $scope.draft.percentage_eng[index], name: 'percentage_eng[]'},
                            {
                                text: "Mobile No.",
                                value: $scope.draft.nominee_mobile_eng[index],
                                name: 'nominee_mobile_eng[]'
                            }
                        ]);
                    })
                }

                else {
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
                        {text: "Mobile No.", value: '', name: 'nominee_mobile_eng[]'}
                    ]);
                }
                $scope.nomineeLength = 0;
            };

            $scope.deleteNomineeRows = function (index) {
                $scope.nomineeRows.splice(index, 1);
                $scope.nomineeEngRows.splice(index, 1);
            }
        });
        GlobalApp.factory('getDraftService', function ($http) {
            return {
                getSingleDraftValues: function (recent) {
//                alert(recent);
                    return $http.get("{{URL::to('HRM/entrysingledraft')}}/" + recent);
                }
            }
        });
        GlobalApp.factory('getDiseaseSkillService', function ($http) {
            return {
                getAllDisease: function () {
                    return $http.get("{{URL::route('get_disease_list')}}")
                },
                getAllSkill: function () {
                    return $http.get("{{URL::route('get_skill_list')}}")
                },
                getAllEducationName: function () {
                    return $http.get("{{URL::route('getalleducation')}}")
                }
            }

        });
        GlobalApp.factory('getBloodService', function ($http) {
            return {
                getAllBloodName: function () {
                    return $http.get("{{URL::route('blood_name')}}")
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
                                console.log(scope.formSubmitResult);
                                $(".overlay").css('display','none');
                                if (scope.formSubmitResult.status == "update") {
                                    $(element).resetForm();
                                    window.location = "{{URL::route('entry_draft')}}";
                                }
                                if (scope.formSubmitResult.status == 'saved') {
                                    $(element).resetForm();
                                    window.location = "{{URL::route('entry_draft')}}";
                                }
                                scope.$digest();
                            }, error: function (responseText, statusText, xhr, $form) {

                                $(".overlay").css('display','none');
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

    <div id="entryform" ng-controller="singleDraftController">
        <div class="overlay">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
        </div>
        <div>
            <section class="content">
                <div >

                    <div id="entryform">
                        <form id="pppp" class="form-horizontal" enctype="multipart/form-data" id="myForm"
                              method="post" action="{{URL::route('editdraft',['id'=>$data])}}">
                            {!! csrf_field() !!}
                            <fieldset>

                                <div class="level-title-session-entry">
                                    <h5 style="text-align: center;">পারিবারিক তথ্য </h5>
                                </div>

                                <div class="box-info">
                                    <div class="box-body">

                                        <div class="form group col-md-12"
                                             ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.ansar_name_eng[0]}">

                                            <label class="control-label col-sm-2" for="email"><sup
                                                        style="color: #ff0709;font-size: 1em">*</sup>Name:</label>

                                            <div class="col-sm-10">
                                                <input type="text" class="form-control " name="ansar_name_eng"
                                                       ng-model="ansar_name_eng" placeholder="Enter your name"
                                                       ng-value="draft.ansar_name_eng "/>
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
                                                       name="ansar_name_bng" ng-model="ansar_name_bng"
                                                       placeholder="আপনার নাম লিখুন"
                                                       ng-value="draft.ansar_name_bng "/>
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
                                                       name="father_name_eng" ng-model="father_name_eng" type="text"
                                                       placeholder="Father's name"
                                                       ng-value="draft.father_name_eng ">
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
                                                       name="father_name_bng" ng-model="father_name_bng" type="text"
                                                       placeholder="পিতার নাম" ng-value="draft.father_name_bng ">
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
                                                       name="mother_name_eng" ng-model="mother_name_eng" type="text"
                                                       placeholder=" Mother's Name"
                                                       ng-value="draft.mother_name_eng ">
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
                                                       name="mother_name_bng" ng-model="mother_name_bng" type="text"
                                                       placeholder="মাতার নাম" ng-value="draft.mother_name_bng ">
                                                    <span style="color:red"
                                                          ng-show="formSubmitResult.error.mother_name_bng[0]">[[ formSubmitResult.error.mother_name_bng[0] ]]</span>
                                            </div>

                                        </div>
                                        <div class="form group col-md-12 "
                                             ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.data_of_birth[0]}">
                                            <label class="control-label col-sm-2" for="email"><sup
                                                        style="color: #ff0709;font-size: 1em">*</sup>Date of birth:</label>

                                            <div class="col-sm-10">
                                                <input class="form-control  " id="data_of_birth"
                                                       name="data_of_birth" date-picker-dir="datePickerDir"
                                                       type="text" placeholder="Date of birth"
                                                       value="[[draft.data_of_birth]]">
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
                                                    <option value="">--Select an option--</option>
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
                                                       name="spouse_name_eng" ng-model="spouse_name_eng" type="text"
                                                       placeholder="Spouse Name" ng-value="draft.spouse_name_eng ">

                                            </div>

                                        </div>
                                        <div class="form group col-md-12 ">
                                            <label class="control-label col-sm-2" for="email">স্ত্রী/স্বামীর
                                                নাম:</label>

                                            <div class="col-sm-10">
                                                <input class="form-control  " id="spouse_name_bng"
                                                       name="spouse_name_bng" ng-model="spouse_name_bng" type="text"
                                                       placeholder=" স্ত্রী/স্বামীর নাম "
                                                       ng-value="draft.spouse_name_bng ">
                                            </div>

                                        </div>
                                        <div class="form group col-md-12 "
                                             ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.national_id_no[0]}">
                                            <label class="control-label col-sm-2" for="email"><sup
                                                        style="color: #ff0709;font-size: 1em">*</sup>National Id no:</label>

                                            <div class="col-sm-10">
                                                <input class="form-control  " id="national_id_no"
                                                       name="national_id_no" ng-model="national_id_no" type="text"
                                                       placeholder="National Id no(Numeric 17 digit for 13 digit add birth year before id no.)"
                                                       ng-value="draft.national_id_no ">
                                                    <span style="color:red"
                                                          ng-show="formSubmitResult.error.national_id_no[0]">[[ formSubmitResult.error.national_id_no[0] ]]</span>
                                            </div>

                                        </div>

                                        <div class="form group col-md-12 ">
                                            <label class="control-label col-sm-2" for="email">Birth Certificate
                                                no:</label>

                                            <div class="col-sm-10">
                                                <input class="form-control  " id="birth_certificate_no"
                                                       name="birth_certificate_no" ng-model="birth_certificate_no"
                                                       type="text" placeholder="Birth Certificate no"
                                                       ng-value="draft.birth_certificate_no ">
                                            </div>
                                        </div>
                                        <div>
                                            <div class="form-horizontal col-md-12 ">

                                                <label class="control-label col-sm-2" for="email">দীর্ঘ মেয়াদি
                                                    অসুখ:</label>

                                                <div class="col-sm-10">
                                                    <select name="long_term_disease" ng-model="long_term_disease"
                                                            class="form-control" id="sell"
                                                            ng-change="diseaseChange()">
                                                        <option value="">--Select an option--</option>
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
                                                           ng-value="draft.own_disease">
                                                </div>
                                            </div>

                                            <div class="form-horizontal col-md-12 ">

                                                <label class="control-label col-sm-2" for="email">নির্দিষ্ট
                                                    দক্ষতা:</label>

                                                <div class="col-sm-10 ">
                                                    <select name="particular_skill" ng-model="particular_skill"
                                                            class="form-control" id="sell"
                                                            ng-change="skillChange()">
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
                                                           name="own_particular_skill" ng-model="my_skill"
                                                           type="text" ng-value="draft.own_particular_skill">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form group col-md-12">
                                            <label class="control-label col-sm-2" for="criminal">Criminal
                                                Case:</label>

                                            <div class="col-sm-10">
                                                <input class="form-control  "
                                                       name="criminal_case" ng-model="criminal_case" type="text"
                                                       placeholder="Criminal case" ng-value="draft.criminal_case ">

                                            </div>
                                        </div>
                                        <div class="form group col-md-12">
                                            <label class="control-label col-sm-2" for="criminal">ফৌজদারি
                                                মামলা:</label>

                                            <div class="col-sm-10">
                                                <input class="form-control  "
                                                       name="criminal_case_bng" ng-model="criminal_case_bng"
                                                       type="text" placeholder="ফৌজদারি মামলা"
                                                       ng-value="draft.criminal_case ">

                                            </div>
                                        </div>
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
                                                       ng-model="village_name" type="text" placeholder=" Village"
                                                       ng-value="draft.village_name ">

                                            </div>

                                        </div>
                                        <div class="form-horizontal col-md-12">
                                            <label class="control-label col-sm-2" for="email">গ্রাম:</label>

                                            <div class="col-sm-10">
                                                <input class="form-control  " id="village_name_bng"
                                                       name="village_name_bng" ng-model="village_name_bng"
                                                       type="text" placeholder=" গ্রাম"
                                                       ng-value="draft.village_name_bng ">

                                            </div>

                                        </div>
                                        <div class="form-horizontal col-md-12">
                                            <label class="control-label col-sm-2" for="email">Post office:</label>

                                            <div class="col-sm-10">
                                                <input class="form-control  " id="post_office_name"
                                                       name="post_office_name" ng-model="post_office_name"
                                                       type="text" placeholder=" Post Office Name "
                                                       ng-value="draft.post_office_name ">

                                            </div>

                                        </div>
                                        <div class="form-horizontal col-md-12">
                                            <label class="control-label col-sm-2" for="email">ডাকঘর:</label>

                                            <div class="col-sm-10">
                                                <input class="form-control  " id="post_office_name_bng"
                                                       name="post_office_name_bng" ng-model="post_office_name_bng"
                                                       type="text" placeholder=" ডাকঘর "
                                                       ng-value="draft.post_office_name_bng ">

                                            </div>

                                        </div>

                                        <div class="form group col-md-12">
                                            <label class="control-label col-sm-2" for="email">Union:</label>

                                            <div class="col-sm-10">
                                                <input class="form-control  " id="union_name_eng"
                                                       name="union_name_eng" ng-model="union_name_eng" type="text"
                                                       placeholder=" Union Name" ng-value="draft.union_name_eng ">
                                            </div>
                                        </div>
                                        <div class="form group col-md-12">
                                            <label class="control-label col-sm-2" for="email">ইউনিয়ন নাম:</label>

                                            <div class="col-sm-10">
                                                <input class="form-control" id="union_name_bng"
                                                       name="union_name_bng" ng-model="union_name_bng" type="text"
                                                       placeholder="ইউনিয়ন নাম" ng-value="draft.union_name_bng ">
                                            </div>
                                        </div>

                                        <div class="form-horizontal col-md-12 "
                                             ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.division_name_eng[0]}" ng-hide="isAdmin==22">

                                            <label class="control-label col-sm-2" for="email"><sup
                                                        style="color: #ff0709;font-size: 1em">*</sup>বিভাগ:</label>


                                            <div class="col-sm-10">
                                                <select name="division_name_eng" class="form-control" id="sell"
                                                        ng-model="SelectedDivision"
                                                        ng-change="SelectedItemChanged()">
                                                    <option value="">--Select a division--</option>
                                                    <option ng-repeat="x in division" value="[[x.id]]">
                                                        [[x.division_name_bng]]
                                                    </option>
                                                </select>
                                                    <span style="color:red"
                                                          ng-show="formSubmitResult.error.division_name_eng[0]">[[ formSubmitResult.error.division_name_eng[0] ]]</span>
                                            </div>

                                        </div>
                                        {{--<div class="form-horizontal col-md-12"ng-show="isAdmin==22">--}}

                                            {{--<label class="control-label col-sm-2" for="email">বিভাগ:</label>--}}
                                            {{--<div class="col-sm-10">--}}
                                                {{--<input type="text" class="form-control" value="{{Auth::user()->district->division->division_name_bng}}" disabled>--}}
                                            {{--</div>--}}

                                        {{--</div>--}}
                                        <div class="form-horizontal col-md-12 "
                                             ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.unit_name_eng[0]}">

                                            <label class="control-label col-sm-2" for="email"><sup
                                                        style="color: #ff0709;font-size: 1em">*</sup>জেলা:</label>

                                            <div class="col-sm-10 ">
                                                <select name="unit_name_eng" class="form-control" id="sell"
                                                        ng-model="SelectedDistrict"
                                                        ng-change="SelectedDistrictChanged()">
                                                    <option value="">--Select a district--</option>
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
                                                        ng-model="ThanaModel">
                                                    <option value="">--Select a thana--</option>
                                                    <option ng-repeat="x in thana" value="[[x.id]]">[[
                                                        x.thana_name_bng ]]
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
                                                       ng-model="hight_feet" type="text" placeholder=" FEET"
                                                       ng-value="draft.hight_feet ">
                                                    <span style="color:red"
                                                          ng-show="formSubmitResult.error.hight_feet[0]">[[ formSubmitResult.error.hight_feet[0] ]]</span>
                                            </div>
                                            <div class="col-sm-5">
                                                <input class="form-control  " id="hight_inch" name="hight_inch"
                                                       ng-model="hight_inch" type="text" placeholder=" INCH" ng-change="hight_inch=hight_inch>=12?11:hight_inch"
                                                       ng-value="draft.hight_inch ">

                                            </div>
                                        </div>
                                        <div class="form-horizontal col-md-12 "
                                             ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.ansar_name_eng[0]}">

                                            <label class="control-label col-sm-2" for="email"><sup
                                                        style="color: #ff0709;font-size: 1em">*</sup>রক্তের
                                                গ্রুপ:</label>

                                            <div class="col-sm-10 ">
                                                <select name="blood_group_name_bng" class="form-control" id="sell"
                                                        ng-model="BloodModel">
                                                    <option value="">--Select a blood group--</option>
                                                    <option ng-repeat="x in blood" value="[[x.id]]">[[
                                                        x.blood_group_name_bng ]]
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
                                                       ng-model="eye_color" type="text" placeholder="Eye color"
                                                       ng-value="draft.eye_color ">
                                            </div>
                                        </div>
                                        <div class="form-horizontal col-md-12">
                                            <label class="control-label col-sm-2" for="email">চোখের রং:</label>

                                            <div class="col-sm-10">
                                                <input class="form-control  " id="eye_color" name="eye_color_bng"
                                                       ng-model="eye_color_bng" type="text" placeholder="চোখের রং"
                                                       ng-value="draft.eye_color_bng ">
                                            </div>
                                        </div>
                                        <div class="form group col-md-12">
                                            <label class="control-label col-sm-2" for="email">Skin color:</label>

                                            <div class="col-sm-10">
                                                <input class="form-control  " id="skin_color" name="skin_color"
                                                       ng-model="skin_color" type="text" placeholder="Skin color"
                                                       ng-value="draft.skin_color ">
                                            </div>
                                        </div>
                                        <div class="form group col-md-12">
                                            <label class="control-label col-sm-2" for="skin_color_bng">গায়ের
                                                রং:</label>

                                            <div class="col-sm-10">
                                                <input class="form-control  " id="skin_color_bng"
                                                       name="skin_color_bng" ng-model="skin_color_bng" type="text"
                                                       placeholder="গায়ের রং" ng-value="draft.skin_color_bng ">
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
                                                       name="identification_mark" ng-model="identification_mark"
                                                       type="text" placeholder="Identification mark"
                                                       ng-value="draft.identification_mark ">
                                            </div>
                                        </div>
                                        <div class="form group col-md-12">
                                            <label class="control-label col-sm-2" for="email">সনাক্তকরন
                                                চিহ্ন:</label>

                                            <div class="col-sm-10">
                                                <input class="form-control  " id="identification_mark_bng"
                                                       name="identification_mark_bng"
                                                       ng-model="identification_mark_bng" type="text"
                                                       placeholder="সনাক্তকরন চিহ্ন"
                                                       ng-value="draft.identification_mark ">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>

                            <input type="hidden" name="ansar_id">

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
                                                                ng-model="ppp.educationIdBng[$index]">
                                                            <option value="">--অপশন নির্বাচন করুন--</option>
                                                            <option ng-repeat="r in ppp.educationName"
                                                                    value="[[r.id]]">[[ ppp.educationIdBng[$index]
                                                                ]] [[r.education_deg_bng]]
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
                                                    <td style=" border-top:0px;background: #ffffff">
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
                                                    <td style=" border-top:0px;;background: #ffffff">
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
                                                        <select ng-if="r.type=='dropdown'" name="[[r.name]]" ng-model="training[$parent.$parent.$index]">
                                                            <option value="">--পদবী নির্বাচন করুন--</option>
                                                            <option ng-repeat="ra in rank" value="[[ra.id]]"
                                                                    ng-selected="ra.name_bng==r.value">
                                                                [[ra.name_bng]]
                                                            </option>
                                                        </select>
                                                        <input ng-if="r.type!='dropdown'" style="line-height: 18px;"
                                                               type="[[r.type]]" date-picker-dir="[[r.class_name]]"
                                                               name="[[r.name]]" placeholder="[[ r.text ]]"
                                                               value="[[r.value]]">
                                                    </td>
                                                    <td><a href=""
                                                           ng-click="(trainingRows.length > 1)?deleteTrainingRows($index):''"><i
                                                                    class="glyphicon glyphicon-trash"></i></a></td>
                                                </tr>
                                                <tr>
                                                    <td style=" border-top:0px;;background: #ffffff">
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
                                                        <select ng-if="r.type=='dropdown'" name="[[r.name]]" ng-model="training[$parent.$parent.$index]">
                                                            <option value="">--Select a rank--</option>
                                                            <option ng-repeat="ra in rank" value="[[ra.id]]"
                                                                    ng-selected="ra.name_eng==r.value">
                                                                [[ra.name_eng]]
                                                            </option>
                                                        </select>
                                                        <input ng-if="r.type!='dropdown'" style="line-height: 18px;"
                                                               type="[[r.type]]" name="[[r.name]]"
                                                               placeholder="[[ r.text ]]" value="[[r.value]]"
                                                               date-picker-dir="[[r.class_name]]">
                                                    </td>
                                                    <td><a href=""
                                                           ng-click="(trainingEngRows.length > 1)?deleteTrainingRows($index):''"><i
                                                                    class="glyphicon glyphicon-trash"></i></a></td>
                                                </tr>
                                                <tr>
                                                    <td style=" border-top:0px;;background: #ffffff">
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
                                                    <td style=" border-top:0px;;background: #ffffff">
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
                                                    <td style=" border-top:0px;;background: #ffffff">
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
                                                <div class="input-group">
                                                    <span class="input-group-addon">+88</span>
                                                    <input class="form-control  " id="mobile_no_self"
                                                           name="mobile_no_self" ng-model="mobile_no_self" ng-change="mobile_no_self=mobile_no_self.length>11?mobile_no_self.substring(0,11):mobile_no_self" type="text"
                                                           placeholder="Mobile no(Self)"
                                                           ng-value="draft.mobile_no_self">
                                                </div>
                                                    <span style="color:red"
                                                          ng-show="formSubmitResult.error.mobile_no_self[0]">[[ formSubmitResult.error.mobile_no_self[0] ]]</span>
                                            </div>
                                        </div>
                                        <div class="form-horizontal col-md-12">
                                            <label class="control-label col-sm-2" for="email">Mobile
                                                no(Request):</label>

                                            <div class="col-sm-10">
                                                <input class="form-control  " id="mobile_no_request"
                                                       name="mobile_no_request" ng-model="mobile_no_request"
                                                       type="text" placeholder="Mobile no(Request)"
                                                       ng-value="draft.mobile_no_request ">
                                            </div>

                                        </div>
                                        <div class="form-horizontal col-md-12">
                                            <label class="control-label col-sm-2" for="email">Land phone
                                                no(self):</label>

                                            <div class="col-sm-10">
                                                <input class="form-control  " id="land_phone_self"
                                                       name="land_phone_self" ng-model="land_phone_self" type="text"
                                                       placeholder="Land phone no(self)"
                                                       ng-value="draft.land_phone_self ">
                                            </div>
                                        </div>

                                        <div class="form group col-md-12">
                                            <label class="control-label col-sm-2" for="email">Land phone
                                                no(request):</label>

                                            <div class="col-sm-10">
                                                <input class="form-control  " id="land_phone_request"
                                                       name="land_phone_request" ng-model="land_phone_request"
                                                       type="text" placeholder="Land phone no(request)"
                                                       ng-value="draft.land_phone_request ">
                                            </div>
                                        </div>
                                        <div class="form-horizontal col-md-12 "
                                             ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.email_self[0]}">
                                            <label class="control-label col-sm-2" for="email">Email(Self):</label>

                                            <div class="col-sm-10">
                                                <input class="form-control  " id="email_self" name="email_self"
                                                       ng-model="email_self" type="email" placeholder="Email(Self)"
                                                       ng-value="draft.email_self ">
                                                    <span style="color:red"
                                                          ng-show="formSubmitResult.error.email_self[0]">[[ formSubmitResult.error.email_self[0] ]]</span>
                                            </div>


                                        </div>
                                        <div class="form-horizontal col-md-12">
                                            <label class="control-label col-sm-2"
                                                   for="email">Email(Request):</label>

                                            <div class="col-sm-10">
                                                <input class="form-control  " id="email_request"
                                                       name="email_request" ng-model="email_request" type="email"
                                                       placeholder="Email(Request)" ng-value="draft.email_request ">
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
                                                    <th>Signature image</th>
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

                            <div>
                                <div class="form-horizontal pull-left">
                                    <input form-submit id="submit" type="submit" name="submit" class="btn btn-primary"
                                           value="Submit">
                                </div>
                                <div class="form-horizontal pull-right">
                                    <input form-submit id="submit1" type="submit" name="Save" class="btn btn-primary"
                                           value="Update draft">
                                    <!--<button name="save" type="save">Save</button>-->
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </form>


                    </div>

                </div>
            </section>
        </div>
    </div>
@stop
