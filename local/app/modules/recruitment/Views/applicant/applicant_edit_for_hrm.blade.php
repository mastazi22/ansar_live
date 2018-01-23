<script>


   /* GlobalApp.controller('fullEntryFormController', function ($scope, $q, $http, httpService, notificationService) {
        $scope.isAdmin = parseInt('{{Auth::user()->type}}')
        $scope.formData = {};
        $scope.fields = [];
        $scope.eduRows = [];
        $scope.eduEngRows = [];
        $scope.allLoading = true;
        $scope.relations = {
            '': '--সম্পর্ক নির্বাচন করুন--',
            'father': 'Father',
            'mother': 'Mother',
            'brother': 'Brother',
            'sister': 'Sister',
            'cousin': 'Cousin',
            'uncle': 'Uncle',
            'aunt': 'Aunt',
            'neighbour': 'Neighbour'
        };

        $scope.profile_pic = " ";
        $scope.formSubmitResult = {};
        $scope.ppp = [];
        $scope.disableDDT = false;
        $scope.calling = function () {
            alert($scope.profile_pic);
        }
        $scope.disableDDT = true;
        $q.all([
            $http({method: 'get', url: '{{URL::to('recruitment/applicant/detail')}}'}),
            httpService.range(),
            httpService.education(),
            $http({method: 'get', url: '{{URL::route('recruitment.applicant.getfieldstore')}}'})
        ]).then(function (response) {
            console.log(response)
            $scope.allLoading = false;
            $scope.formData = response[0].data.data;
            $scope.district = response[0].data.units;
            $scope.thana = response[0].data.thanas;
            $scope.division = response[1];
            $scope.ppp = response[2];
            $scope.fields = response[3].data['field_value'].split(',');
            $scope.disableDDT = false;
            $scope.formData.division_id += '';
            $scope.formData.unit_id += '';
            $scope.formData.thana_id += '';
            $scope.formData.appliciant_education_info.forEach(function (d, i) {

                $scope.formData.appliciant_education_info[i].job_education_id += '';
            })
        });
        $scope.SelectedItemChanged = function () {
            $scope.disableDDT = true;
            httpService.unit($scope.formData.division_id).then(function (response) {
                $scope.district = response;
                $scope.thana = [];
                $scope.formData.unit_id = '';
                $scope.formData.thana_id = '';

                $scope.disableDDT = false;
            })
        };
        $scope.SelectedDistrictChanged = function () {
            $scope.disableDDT = true;
            httpService.thana($scope.formData.division_id, $scope.formData.unit_id).then(function (response) {
                $scope.thana = response;
                $scope.formData.thana_id = "";
                $scope.disableDDT = false;
            })
        };

        $scope.eduDeleteRows = function (index) {
            $scope.formData.appliciant_education_info.splice(index, 1);
        }
        $scope.addEducation = function () {
            $scope.formData.appliciant_education_info.push({
                job_education_id: '',
                job_applicant_id: $scope.formData.id,
                institute_name: '',
                gade_divission: '',
                passing_year: ''
            })
        }
        $scope.updateData = function () {
            $scope.allLoading = true;
            $http({
                method: 'post',
                data: $scope.formData,
                url: '{{URL::route('recruitment.applicant.update')}}'
            }).then(function (response) {
                $scope.allLoading = false;
                notificationService.notify(response.data.status, response.data.message)
                $("#edit-form").modal('toggle');
            }, function (response) {
                $scope.allLoading = false;
                if (response.status == 422) {
                    $scope.formSubmitResult['error'] = response.data;
                }
                else {
                    notificationService.notify('error', 'An unknown error occur. Please try again later')
                }
            })
        }
    });*/
</script>

<div id="entryform" ng-controller="fullEntryFormController" d-picker ng-init="loadApplicantDetail()">

    <div>

        <section class="content">

            <div id="entryform">
                <form id="pppp" class="form-horizontal" enctype="multipart/form-data" id="myForm"
                      method="post" ng-submit="updateData()">
                    {!! csrf_field() !!}
                    <fieldset>

                        <div class="level-title-session-entry">
                            <h5 style="text-align: center;">বাক্তিগত ও পারিবারিক তথ্য </h5>
                        </div>

                        <div class="box box-info">
                            <div class="overlay" ng-if="allLoading">
                                    <span class="fa">
                                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                                    </span>
                            </div>
                            <div class="box-body">
                                {{--Start Ansar Name (English) Field --}}
                                <div class="form group col-md-12" ng-if="isEditable('applicant_name_eng')>=0"
                                     ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.ansar_name_eng[0]}">

                                    <label class="control-label col-sm-2" for="email"><sup
                                                style="color: #ff0709;font-size: 1em">*</sup>Name:</label>

                                    <div class="col-sm-10">
                                        <input type="text" class="form-control " name="ansar_name_eng"
                                               ng-model="formData.ansar_name_eng"
                                               placeholder="Enter your name"/>
                                        <span style="color:red"
                                              ng-show="formSubmitResult.error.applicant_name_eng[0]">[[ formSubmitResult.error.applicant_name_eng[0] ]]</span>
                                    </div>

                                </div>
                                {{--End Ansar Name (English) Field --}}
                                {{--Start Ansar Name (Bangla) Field --}}
                                <div class="form-horizontal col-md-12 "
                                     ng-if="isEditable('applicant_name_bng')>=0"
                                     ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.ansar_name_bng[0]}">
                                    <label class="control-label col-sm-2" for="email"><sup
                                                style="color: #ff0709;font-size: 1em">*</sup>নাম:</label>

                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="ansar_name_bng"
                                               ng-model="formData.ansar_name_bng"
                                               placeholder="আপনার নাম লিখুন"/>
                                        <span style="color:red"
                                              ng-show="formSubmitResult.error.applicant_name_bng[0]">[[ formSubmitResult.error.applicant_name_bng[0] ]]</span>
                                    </div>
                                </div>
                                {{--End Ansar Name (Bangla) Field --}}
                                {{--Start Ansar Rank Field --}}
                                <div class="form-horizontal col-md-12 "
                                     ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.designation_id[0]}">

                                    <label class="control-label col-sm-2" for="email"><sup style="color: #ff0709;font-size: 1em">*</sup>বর্তমান পদবী :</label>

                                    <div class="col-sm-10 ">
                                        <select name="designation_id" ng-model="formData.designation_id"
                                                class="form-control" id="sell">
                                            <option value="">--পদবী নির্বাচন করুন--</option>
                                            <option ng-repeat="r in ranks" value="[[r.id]]">
                                                [[r.name_bng]]
                                            </option>
                                        </select>
                                        <span style="color:red"
                                              ng-show="formSubmitResult.error.designation_id[0]">[[ formSubmitResult.error.designation_id[0] ]]</span>
                                    </div>

                                </div>
                                {{--End Ansar Rank Field --}}
                                {{--Start Ansar Father Name (English) Field --}}
                                <div class="form group col-md-12 "
                                     ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.father_name_bng[0]}">
                                    <label class="control-label col-sm-2" for="email"><sup
                                                style="color: #ff0709;font-size: 1em">*</sup>Father Name</label>

                                    <div class="col-sm-10">
                                        <input class="form-control " id="father_name_bng"
                                               name="formData.father_name_eng" ng-model="formData.father_name_eng"
                                               type="text"
                                               placeholder="Father Name Eng">
                                        <span style="color:red"
                                              ng-show="formSubmitResult.error.father_name_eng[0]">[[ formSubmitResult.error.father_name_eng[0] ]]</span>
                                    </div>
                                </div>
                                {{--End Ansar Father Name (English) Field --}}
                                {{--Start Ansar Father Name (Bangla) Field --}}
                                <div class="form group col-md-12 " ng-if="isEditable('father_name_bng')>=0"
                                     ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.father_name_bng[0]}">
                                    <label class="control-label col-sm-2" for="email"><sup
                                                style="color: #ff0709;font-size: 1em">*</sup>পিতার
                                        নাম</label>

                                    <div class="col-sm-10">
                                        <input class="form-control " id="father_name_bng"
                                               name="formData.father_name_bng" ng-model="formData.father_name_bng"
                                               type="text"
                                               placeholder="পিতার নাম">
                                        <span style="color:red"
                                              ng-show="formSubmitResult.error.father_name_bng[0]">[[ formSubmitResult.error.father_name_bng[0] ]]</span>
                                    </div>
                                </div>
                                {{--End Ansar Father Name (Bangla) Field --}}
                                {{--Start Ansar Mother Name (English) Field --}}
                                <div class="form group col-md-12"
                                     ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.mother_name_eng[0]}">
                                    <label class="control-label col-sm-2" for="email"><sup
                                                style="color: #ff0709;font-size: 1em">*</sup>Mother Name</label>

                                    <div class="col-sm-10">
                                        <input class="form-control  " id="mother_name_eng"
                                               name="mother_name_eng" ng-model="formData.mother_name_eng"
                                               type="text"
                                               placeholder="Mother Name Eng">
                                        <span style="color:red"
                                              ng-show="formSubmitResult.error.mother_name_eng[0]">[[ formSubmitResult.error.mother_name_eng[0] ]]</span>
                                    </div>

                                </div>
                                {{--End Ansar Mother Name (English) Field --}}
                                {{--Start Ansar Mother Name (Bangla) Field --}}
                                <div class="form group col-md-12 " ng-if="isEditable('mother_name_bng')>=0"
                                     ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.mother_name_bng[0]}">
                                    <label class="control-label col-sm-2" for="email"><sup
                                                style="color: #ff0709;font-size: 1em">*</sup>মাতার
                                        নাম</label>

                                    <div class="col-sm-10">
                                        <input class="form-control  " id="mother_name_bng"
                                               name="mother_name_bng" ng-model="formData.mother_name_bng"
                                               type="text"
                                               placeholder="মাতার নাম">
                                        <span style="color:red"
                                              ng-show="formSubmitResult.error.mother_name_bng[0]">[[ formSubmitResult.error.mother_name_bng[0] ]]</span>
                                    </div>

                                </div>
                                {{--End Ansar Mother Name (Bangla) Field --}}
                                {{--Start Ansar Date of Birth Field --}}
                                <div class="form group col-md-12 " ng-if="isEditable('date_of_birth')>=0"
                                     ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.date_of_birth[0]}">
                                    <label class="control-label col-sm-2" for="email"><sup
                                                style="color: #ff0709;font-size: 1em">*</sup>Date of
                                        birth</label>

                                    <div class="col-sm-10">
                                        <input class="form-control picker " id="date_of_birth"
                                               name="date_of_birth" ng-model="formData.data_of_birth" date-picker=""
                                               placeholder="Date of birth">
                                        <span style="color:red"
                                              ng-show="formSubmitResult.error.data_of_birth[0]">[[ formSubmitResult.error.date_of_birth[0] ]]</span>
                                    </div>
                                </div>
                                {{--End Ansar Date of Birth Field --}}
                                {{--Start Ansar Married Status Field --}}
                                <div class="form-horizontal col-md-12 " ng-if="isEditable('marital_status')>=0"
                                     ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.marital_status[0]}">

                                    <label class="control-label col-sm-2" for="email"><sup
                                                style="color: #ff0709;font-size: 1em">*</sup>Marital
                                        status</label>

                                    <div class="col-sm-10 ">
                                        <select name="marital_status" ng-model="formData.marital_status"
                                                class="form-control" id="sell">
                                            <option value="">--Select your marital condition--</option>
                                            <option value="Married">
                                                Married
                                            </option>
                                            <option value="Unmarried">
                                                Unmarried
                                            </option>
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
                                               name="spouse_name_eng" ng-model="formData.spouse_name_eng" type="text"
                                               placeholder="Spouse Name"
                                               value="{{Request::old('spouse_name_eng')}}">

                                    </div>

                                </div>
                                <div class="form group col-md-12 ">
                                    <label class="control-label col-sm-2" for="email">স্ত্রী/স্বামীর নাম</label>
                                    <div class="col-sm-10">
                                        <input class="form-control  " id="spouse_name_bng" name="spouse_name_bng" ng-model="formData.spouse_name_bng" type="text" placeholder=" স্ত্রী/স্বামীর নাম " value="{{Request::old('spouse_name_bng')}}">
                                    </div>

                                </div>
                                {{--Start Ansar National Id Field --}}
                                <div class="form group col-md-12 " ng-if="isEditable('national_id_no')>=0"
                                     ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.national_id_no[0]}">
                                    <label class="control-label col-sm-2" for="email"><sup
                                                style="color: #ff0709;font-size: 1em">*</sup>National Id no</label>

                                    <div class="col-sm-10">
                                        <input class="form-control  " id="national_id_no"
                                               name="national_id_no" ng-model="formData.national_id_no" type="text"
                                               placeholder="National Id no(Numeric 17 digit for 13 digit add birth year before id no.)">
                                        <span style="color:red"
                                              ng-show="formSubmitResult.error.national_id_no[0]">[[ formSubmitResult.error.national_id_no[0] ]]</span>
                                    </div>

                                </div>
                                {{--End Ansar National Id Field --}}

                                <div class="form-horizontal col-md-12 ">
                                    <label class="control-label col-sm-2" for="email">দীর্ঘ মেয়াদি অসুখ</label>

                                    <div class="col-sm-10">
                                        <select name="long_term_disease" ng-model="formData.disease.long_term_disease"
                                                class="form-control" id="sell" ng-change="diseaseChange()">
                                            <option value="">--অসুখ নির্বাচন করুন--</option>
                                            <option ng-repeat="x in diseases" value="[[x.id]]">
                                                [[x.disease_name_bng]]
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-horizontal col-md-12 " ng-show="own_disease">
                                    <label class="control-label col-sm-2" for="email">অসুখ:</label>

                                    <div class="col-sm-10">
                                        <input class="form-control" id="own_disease" name="own_disease"
                                               ng-model="formData.disease.my_disease" type="text"
                                               placeholder="আপনার অসুখের নাম লিখুন">
                                    </div>
                                </div>

                                <div class="form-horizontal col-md-12 ">
                                    <label class="control-label col-sm-2" for="email">নির্দিষ্ট দক্ষতা</label>

                                    <div class="col-sm-10 ">
                                        <select name="particular_skill" ng-model="formData.skill_id"
                                                ng-change="own_particular_skill=formData.skill_id==1?true:false" class="form-control" id="sell">
                                            <option value="">--দক্ষতা নির্বাচন করুন--</option>
                                            <option ng-repeat="x in skills" value="[[x.id]]">
                                                [[x.skill_name_bng]]
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-horizontal col-md-12 " ng-show="own_particular_skill">
                                    <label class="control-label col-sm-2" for="email">দক্ষতা:</label>

                                    <div class="col-sm-10">
                                        <input class="form-control" ng-model="formData.own_particular_skill" type="text" placeholder="আপনার দক্ষতা লিখুন">
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
                                        <input class="form-control  " id="village_name" name="village_name" ng-model="formData.village_name" type="text" placeholder=" Village" value="{{Request::old('village_name')}}">
                                    </div>
                                </div>
                                <div class="form-horizontal col-md-12"
                                     ng-if="isEditable('village_name_bng')>=0">
                                    <label class="control-label col-sm-2" for="email">গ্রাম/বাড়ি নং:</label>
                                    <div class="col-sm-10">
                                        <input class="form-control  " id="village_name_bng"
                                               name="village_name_bng" ng-model="formData.village_name_bng"
                                               type="text" placeholder=" গ্রাম">
                                    </div>

                                </div>
                                <div class="form-horizontal col-md-12">
                                    <label class="control-label col-sm-2" for="email">Post office:</label>

                                    <div class="col-sm-10">
                                        <input class="form-control  " id="post_office_name" name="post_office_name" ng-model="formData.post_office_name" type="text" placeholder=" Post Office Name " value="{{Request::old('post_office_name')}}">
                                    </div>

                                </div>
                                <div class="form-horizontal col-md-12"
                                     ng-if="isEditable('post_office_name_bng')>=0">
                                    <label class="control-label col-sm-2" for="email">ডাকঘর:</label>

                                    <div class="col-sm-10">
                                        <input class="form-control  " id="post_office_name_bng"
                                               name="post_office_name_bng" ng-model="formData.post_office_name_bng"
                                               type="text" placeholder=" ডাকঘর ">
                                    </div>

                                </div>
                                <div class="form group col-md-12">
                                    <label class="control-label col-sm-2" for="email">Union/Word:</label>
                                    <div class="col-sm-10">
                                        <input class="form-control  " id="union_name_eng" name="union_name_eng" ng-model="formData.union_name_eng" type="text" placeholder=" Union Name" value="{{Request::old('union_name_eng')}}">
                                    </div>
                                </div>
                                <div class="form group col-md-12" ng-if="isEditable('union_name_bng')>=0">
                                    <label class="control-label col-sm-2" for="email">ইউনিয়ন
                                        নাম/ওয়ার্ড:</label>
                                    <div class="col-sm-10">
                                        <input class="form-control" id="union_name_bng"
                                               name="union_name_bng" ng-model="formData.union_name_bng" type="text"
                                               placeholder="ইউনিয়ন নাম">
                                    </div>
                                </div>
                                <div class="form-horizontal col-md-12 " ng-if="isEditable('division_id')>=0"
                                     ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.division_name_eng[0]}">
                                    <label class="control-label col-sm-2" for="email"><sup
                                                style="color: #ff0709;font-size: 1em">*</sup>বিভাগ</label>
                                    <div class="col-sm-10 ">
                                        <select name="division_name_eng" ng-disabled="disableDDT"
                                                class="form-control" id="sell"
                                                ng-model="formData.division_id"
                                                ng-change="SelectedItemChanged()">
                                            <option value="">--বিভাগ নির্বাচন করুন--</option>
                                            <option ng-repeat="x in division" value="[[x.id]]">
                                                [[x.division_name_bng]]
                                            </option>
                                        </select>
                                        <span style="color:red"
                                              ng-show="formSubmitResult.error.division_id[0]">[[ formSubmitResult.error.id[0] ]]</span>
                                    </div>

                                </div>
                                <div class="form-horizontal col-md-12 " ng-if="isEditable('unit_id')>=0"
                                     ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.unit_name_eng[0]}">
                                    <label class="control-label col-sm-2" for="email"><sup
                                                style="color: #ff0709;font-size: 1em">*</sup>জেলা</label>
                                    <div class="col-sm-10 ">
                                        <select ng-disabled="disableDDT" name="unit_name_eng"
                                                class="form-control" id="sell" ng-model="formData.unit_id"
                                                ng-change="SelectedDistrictChanged()">
                                            <option value="">--জেলা নির্বাচন করুন--</option>
                                            <option ng-repeat="x in district" value="[[x.id]]">[[
                                                x.unit_name_bng ]]
                                            </option>
                                        </select>
                                        <span style="color:red"
                                              ng-show="formSubmitResult.error.unit_id[0]">[[ formSubmitResult.error.unit_id[0] ]]</span>
                                    </div>

                                </div>
                                <div class="form-horizontal col-md-12 " ng-if="isEditable('thana_id')>=0"
                                     ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.thana_id[0]}">
                                    <label class="control-label col-sm-2" for="email"><sup
                                                style="color: #ff0709;font-size: 1em">*</sup>থানা</label>
                                    <div class="col-sm-10 ">
                                        <select ng-disabled="disableDDT" name="thana_name_eng"
                                                class="form-control" id="sell" ng-model="formData.thana_id"
                                                ng-change="SelectedThanaChanged()">
                                            <option value="">--থানা নির্বাচন করুন--</option>
                                            <option ng-repeat="x in thana" value="[[x.id]]">[[
                                                x.thana_name_bng ]]
                                            </option>
                                        </select>
                                        <span style="color:red"
                                              ng-show="formSubmitResult.error.thana_id[0]">[[ formSubmitResult.error.thana_id[0] ]]</span>
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
                                <div class="form-horizontal col-md-12 " ng-if="isEditable('height_feet')>=0"
                                     ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.height_feet[0]}">
                                    <label class="control-label col-sm-2" for="email"><sup
                                                style="color: #ff0709;font-size: 1em">*</sup>Height</label>
                                    <div class="col-sm-5">
                                        <input class="form-control  " id="hight_feet" name="hight_feet"
                                               ng-model="formData.hight_feet" type="text" placeholder=" FEET">
                                        <span style="color:red"
                                              ng-show="formSubmitResult.error.height_feet[0]">[[ formSubmitResult.error.hight_feet[0] ]]</span>
                                    </div>

                                    <div class="col-sm-5">
                                        <input class="form-control  " id="hight_inch" name="hight_inch"
                                               ng-model="formData.hight_inch"
                                               ng-change="formData.hight_inch=formData.hight_inch>=12?11:formData.hight_inch"
                                               type="text" placeholder=" INCHES">
                                        <span style="color:red"
                                              ng-show="formSubmitResult.error.hight_inch[0]">[[ formSubmitResult.error.hight_inch[0] ]]</span>
                                    </div>
                                </div>
                                <div class="form-horizontal col-md-12 "
                                     ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.blood_group_name_bng[0]}">

                                    <label class="control-label col-sm-2" for="email"><sup
                                                style="color: #ff0709;font-size: 1em">*</sup>রক্তের
                                        গ্রুপ:</label>

                                    <div class="col-sm-10 ">
                                        <select name="blood_group_name_bng" class="form-control" id="sell"
                                                ng-model="formData.blood_group_id">
                                            <option value="">--রক্তের গ্রুপ নির্বাচন করুন--</option>
                                            <option ng-repeat="x in blood">[[ x.blood_group_name_bng ]]</option>
                                        </select>
                                        <span style="color:red"
                                              ng-show="formSubmitResult.error.blood_group_name_bng[0]">[[ formSubmitResult.error.blood_group_name_bng[0] ]]</span>
                                    </div>

                                </div>
                                <div class="form-horizontal col-md-12">
                                    <label class="control-label col-sm-2" for="email">Eye color:</label>
                                    <div class="col-sm-10">
                                        <input class="form-control" ng-model="formData.eye_color" name="eye_color" type="text" placeholder="Eye color">
                                    </div>
                                </div>
                                <div class="form-horizontal col-md-12">
                                    <label class="control-label col-sm-2" for="email">চোখের রং:</label>

                                    <div class="col-sm-10">
                                        <input class="form-control" ng-model="formData.eye_color_bng" type="text" placeholder="চোখের রং">
                                    </div>
                                </div>
                                <div class="form group col-md-12">
                                    <label class="control-label col-sm-2" for="email">Skin color:</label>

                                    <div class="col-sm-10">
                                        <input class="form-control" ng-model="formData.skin_color" type="text" placeholder="Skin color">
                                    </div>
                                </div>
                                <div class="form group col-md-12">
                                    <label class="control-label col-sm-2" for="skin_color_bng">গায়ের
                                        রং:</label>

                                    <div class="col-sm-10">
                                        <input class="form-control" ng-model="formData.skin_color_bng" type="text" placeholder="গায়ের রং">
                                    </div>
                                </div>
                                <div class="form-horizontal col-md-12 "
                                     ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.gender[0]}">
                                    <label class="control-label col-sm-2" for="email"><sup
                                                style="color: #ff0709;font-size: 1em">*</sup>Gender</label>
                                    <div class="col-sm-10 ">
                                        <select name="sex" ng-model="formData.sex" class="form-control"
                                                id="sell">
                                            <option value="">--Select an option--</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                        <span style="color:red" ng-show="formSubmitResult.error.gender[0]">[[ formSubmitResult.error.sex[0] ]]</span>
                                    </div>

                                </div>
                                <div class="form group col-md-12">
                                    <label class="control-label col-sm-2" for="email">Identification
                                        mark:</label>

                                    <div class="col-sm-10">
                                        <input class="form-control" ng-model="formData.identification_mark" type="text" placeholder="Identification mark">
                                    </div>
                                </div>
                                <div class="form group col-md-12">
                                    <label class="control-label col-sm-2" for="email">সনাক্তকরন চিহ্ন:</label>
                                    <div class="col-sm-10">
                                        <input class="form-control" ng-model="formData.identification_mark_bng" type="text" placeholder="সনাক্তকরন চিহ্ন">
                                    </div>
                                </div>

                            </div>
                        </div>
                    </fieldset>

                    <fieldset >
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
                                        <tr ng-repeat="row in formData.appliciant_education_info">
                                            <td>
                                                <select name="educationIdBng[]"
                                                        ng-model="row.job_education_id">
                                                    <option value="">--অপশন নির্বাচন করুন--</option>
                                                    <option ng-repeat="r in ppp"
                                                            value="[[r.id]]">[[r.education_deg_bng]]
                                                    </option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text"
                                                       placeholder=""
                                                       ng-model="row.institute_name">
                                            </td>
                                            <td>
                                                <input type="text"
                                                       placeholder=""
                                                       ng-model="row.passing_year">
                                            </td>
                                            <td>
                                                <input type="text"
                                                       placeholder=""
                                                       ng-model="row.gade_divission">
                                            </td>
                                            <td>
                                                <a href=""
                                                   ng-click="(formData.appliciant_education_info.length > 1)?eduDeleteRows($index):''"><i
                                                            class="glyphicon glyphicon-trash"></i></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style=" border-top:0px;background: #ffffff !important;">
                                                <a href=""><p ng-click="addEducation()"
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
                                        <tr ng-repeat="row in formData.appliciant_education_info">
                                            <td>
                                                <select name="educationIdBng[]"
                                                        ng-model="row.job_education_id">
                                                    <option value="">--Select a option--</option>
                                                    <option ng-repeat="r in ppp"
                                                            value="[[r.id]]">[[r.education_deg_eng]]
                                                    </option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text"
                                                       placeholder=""
                                                       ng-model="row.institute_name_eng">
                                            </td>
                                            <td>
                                                <input type="text"
                                                       placeholder=""
                                                       ng-model="row.passing_year_eng">
                                            </td>
                                            <td>
                                                <input type="text"
                                                       placeholder=""
                                                       ng-model="row.gade_divission_eng">
                                            </td>
                                            <td>
                                                <a href=""
                                                   ng-click="(formData.appliciant_education_info.length > 1)?eduDeleteRows($index):''"><i
                                                            class="glyphicon glyphicon-trash"></i></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style=" border-top:0px;background: #ffffff !important;">
                                                <a href=""><p ng-click="addEducation()"
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


                                        <tr ng-repeat="row in formData.applicant_training_info">

                                            <td>
                                                <select ng-model="row.training_designation">
                                                    <option value="">--Select a option--</option>
                                                    <option ng-repeat="r in ranks" value="[[r.id]]">
                                                        [[r.name_bng]]
                                                    </option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" ng-model="row.training_institute_name">
                                            </td>
                                            <td>
                                                <input type="text" ng-model="row.training_start_date">
                                            </td>
                                            <td>
                                                <input type="text"
                                                       placeholder=""
                                                       ng-model="row.training_end_date">
                                            </td>
                                            <td>
                                                <input type="text" ng-model="row.trining_certificate_no">
                                            </td>
                                            <td>
                                                <a href="" ng-click="(formData.applicant_training_info.length > 1)?deleteTrainingInfo($index):''">
                                                    <i class="glyphicon glyphicon-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style=" border-top:0px;background: #ffffff;">
                                                <a class="btn btn-info" ng-click="addTrainingInfo()" >Add more</a>
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


                                        <tr ng-repeat="row in formData.applicant_training_info">

                                            <td>
                                                <select ng-model="row.training_designation">
                                                    <option value="">--Select a option--</option>
                                                    <option ng-repeat="r in ranks" value="[[r.id]]">
                                                        [[r.name_eng]]
                                                    </option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" ng-model="row.training_institute_name_eng">
                                            </td>
                                            <td>
                                                <input type="text" ng-model="row.training_start_date_eng">
                                            </td>
                                            <td>
                                                <input type="text"
                                                       placeholder=""
                                                       ng-model="row.training_end_date">
                                            </td>
                                            <td>
                                                <input type="text" ng-model="row.trining_certificate_no_eng">
                                            </td>
                                            <td>
                                                <a href="" ng-click="(formData.applicant_training_info.length > 1)?deleteTrainingInfo($index):''">
                                                    <i class="glyphicon glyphicon-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style=" border-top:0px;background: #ffffff;">
                                                <a class="btn btn-info" ng-click="addTrainingInfo($event)" >Add more</a>
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

                                        <tr ng-repeat="row in formData.applicant_nominee_info">

                                            <td>
                                                <input type="text" ng-model="row.name_of_nominee">
                                            </td>
                                            <td>
                                                <input type="text" ng-model="row.relation_with_nominee">
                                            </td>
                                            <td>
                                                <input type="text" ng-model="row.nominee_parcentage">
                                            </td>
                                            <td>
                                                <input type="text" ng-model="row.nominee_contact_no">
                                            </td>
                                            <td>
                                                <a href="" ng-click="(formData.applicant_nominee_info.length > 1)?deleteNomineeInfo($index):''">
                                                    <i class="glyphicon glyphicon-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style=" border-top:0px;background: #ffffff;">
                                                <a class="btn btn-info" ng-click="addNomineeInfo()" >Add more</a>
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

                                        <tr ng-repeat="row in formData.applicant_nominee_info">

                                            <td>
                                                <input type="text" ng-model="row.name_of_nominee">
                                            </td>
                                            <td>
                                                <input type="text" ng-model="row.relation_with_nominee">
                                            </td>
                                            <td>
                                                <input type="text" ng-model="row.nominee_parcentage">
                                            </td>
                                            <td>
                                                <input type="text" ng-model="row.nominee_contact_no">
                                            </td>
                                            <td>
                                                <a href="" ng-click="(formData.applicant_nominee_info.length > 1)?deleteNomineeInfo($index):''">
                                                    <i class="glyphicon glyphicon-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style=" border-top:0px;background: #ffffff;">
                                                <a class="btn btn-info" ng-click="addNomineeInfo()" >Add more</a>
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
                                <div class="form-horizontal col-md-12 " ng-if="isEditable('mobile_no_self')>=0"
                                     ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.mobile_no_self[0]}">
                                    <label class="control-label col-sm-2" for="email"><sup
                                                style="color: #ff0709;font-size: 1em">*</sup>Mobile
                                        no(Self)</label>

                                    <div class="col-sm-10">
                                        <div class="input-group">
                                            <span class="input-group-addon">+88</span>
                                            <input class="form-control  " id="mobile_no_self"
                                                   name="mobile_no_self" ng-model="formData.mobile_no_self"
                                                   ng-change="mobile_no_self=mobile_no_self.length>11?mobile_no_self.substring(0,11):mobile_no_self"
                                                   type="text" placeholder="Mobile no(Self)">
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
                                        <input class="form-control"
                                               ng-model="formData.mobile_no_request" type="text"
                                               placeholder="Mobile no(Request)">
                                    </div>

                                </div>
                                <div class="form-horizontal col-md-12">
                                    <label class="control-label col-sm-2" for="email">Land phone
                                        no(self):</label>

                                    <div class="col-sm-10">
                                        <input class="form-control"
                                               ng-model="formData.land_phone_self" type="text"
                                               placeholder="Land phone no(self)">
                                    </div>
                                </div>
                                <div class="form group col-md-12">
                                    <label class="control-label col-sm-2" for="email">Land phone
                                        no(request):</label>

                                    <div class="col-sm-10">
                                        <input class="form-control"
                                               ng-model="formData.land_phone_request" type="text"
                                               placeholder="Land phone no(request)">
                                    </div>
                                </div>
                                <div class="form-horizontal col-md-12 ">
                                    <label class="control-label col-sm-2" for="email">Email(Self):</label>

                                    <div class="col-sm-10">
                                        <input class="form-control" ng-model="formData.email_self"
                                               type="text" placeholder="Email(Self)">
                                    </div>


                                </div>
                                <div class="form-horizontal col-md-12">
                                    <label class="control-label col-sm-2"
                                           for="email">Email(Request):</label>

                                    <div class="col-sm-10">
                                        <input class="form-control" id="email_request"
                                               ng-model="formData.email_request" type="text"
                                               placeholder="Email(Request)">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    {{--Draft Save Button--}}
                    <div class="row" style="margin: 0 !important;">
                        <div class="form-horizontal pull-right">
                            <button form-submit id="submit" type="submit" name="submit"
                                    class="btn btn-primary"
                                    value="1">Save data for HRM
                            </button>
                        </div>
                        {{--Form Submit Button--}}
                    </div>

                </form>


            </div>
        </section>
    </div>
</div>