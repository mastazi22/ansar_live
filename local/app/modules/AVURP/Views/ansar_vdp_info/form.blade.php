<style>
    .control-label {
        text-align: left !important;
    }
    p.text-danger{
        margin-bottom: 5px !important;
    }
    table{
        margin-bottom: 5px !important;
    }
</style>
<script>
    var formData = new FormData();
    GlobalApp.controller('InfoController', function ($scope, $http, httpService, $q, notificationService) {

        $scope.info = {urL: '', form: {}};
        $scope.errors = {};
        $scope.subTraining = [];
        $scope.info.url = '{{$url}}'
        $scope.educationDegrees = [1];
        $scope.training_info = [1];
        $scope.allLoading = true
        $scope.genders = [
            {
                value: 'Male',
                text: 'Male'
            },
            {
                value: 'Female',
                text: 'Female'
            }
        ]
        $scope.unionWords = [
            {id: 1, number_bng: 'ওয়ার্ড-০১'},
            {id: 2, number_bng: 'ওয়ার্ড-০২'},
            {id: 3, number_bng: 'ওয়ার্ড-০৩'},
            {id: 4, number_bng: 'ওয়ার্ড-০৪'},
            {id: 5, number_bng: 'ওয়ার্ড-০৫'},
            {id: 6, number_bng: 'ওয়ার্ড-০৬'},
            {id: 7, number_bng: 'ওয়ার্ড-০৭'},
            {id: 8, number_bng: 'ওয়ার্ড-০৮'},
            {id: 9, number_bng: 'ওয়ার্ড-০৯'},
        ]
        $scope.ranks = [
            {value: 'ansar', text: 'আনসার'},
            {value: 'vdp', text: 'ভিডিপি'},
            {value: 'tdp', text: 'টিডিপি'},
            {value: 'basic', text: 'মৌলিক/পেশাভিত্তিক'},
        ]
        $q.all([
            httpService.range(),
            httpService.bloodGroup(),
            httpService.education(),
            httpService.mainTraining()
            @if(isset($id))
            , $http.get("{{URL::route('AVURP.info.edit',['id'=>$id])}}")
            @endif

        ]).then(function (response) {
            $scope.divisions = response[0];
            $scope.bloodGroups = response[1];
            $scope.educations = response[2];
            $scope.mainTraining = response[3];
            @if(isset($id))
                $scope.info.form = response[4].data;
            $scope.info.form['_method'] = 'patch';
            $scope.info.form['educationInfo'] = $scope.info.form['education'];
            delete $scope.info.form['education'];
            $scope.info.form['division_id'] += '';
            $scope.info.form['unit_id'] += '';
            $scope.info.form['thana_id'] += '';
            $scope.info.form['union_id'] += '';
            $scope.info.form['educationInfo'].forEach(function (v, index) {
                $scope.info.form['educationInfo'][index].education_id += '';
            })
            $scope.info.form['training_info'].forEach(function (v, index) {
                $scope.info.form['training_info'][index].training_id += '';
                $scope.info.form['training_info'][index].sub_training_id += '';
                $scope.subTraining[index] = $scope.info.form['training_info'][index].main_training.sub_training
                delete  $scope.info.form['training_info'][index].main_training;
            })
            $scope.educationDegrees = new Array($scope.info.form['educationInfo'].length)
            $scope.training_info = new Array($scope.info.form['training_info'].length)
            $scope.info.form['blood_group_id'] += '';
            $scope.loadUnit($scope.info.form['division_id']);
            $scope.loadThana($scope.info.form['division_id'], $scope.info.form['unit_id']);
            $scope.loadUnion($scope.info.form['division_id'], $scope.info.form['unit_id'], $scope.info.form['thana_id']);
            Object.keys($scope.info.form).forEach(function (key) {
                if(!$scope.info.form[key]) delete $scope.info.form[key];
            })
            @endif
                $scope.allLoading = false
        })
        $scope.loadUnit = function (rangeId) {
            $scope.units = $scope.thanas = $scope.unions = [];
            httpService.unit(rangeId).then(function (response) {
                $scope.units = response;
            })
        }
        $scope.loadThana = function (rangeId, unitId) {
            $scope.thanas = $scope.unions = [];
            httpService.thana(rangeId, unitId).then(function (response) {
                $scope.thanas = response;
            })
        }
        $scope.loadUnion = function (rangeId, unitId, thanaId) {
            $scope.unions = [];
            httpService.union(rangeId, unitId, thanaId).then(function (response) {
                $scope.unions = response;
            })
        }
        $scope.loadSubTraining = function (id, index) {
            if($scope.subTraining[index]!==undefined&&$scope.info.form.training_info[index]!==undefined){
                $scope.info.form.training_info[index].sub_training_id = '';
                $scope.subTraining[index] = {}
            }
            httpService.subTraining(id).then(function (data) {
                $scope.subTraining[index] = data;
            })
        }
        $scope.removeTraining = function (index) {
            var l = $scope.training_info.length;
            if (l > 1) {
                $scope.training_info.splice(index, 1)
                if ($scope.info.form.training_info &&
                    $scope.info.form.training_info instanceof Object  &&
                    $scope.info.form.training_info[index]) {
                    delete $scope.info.form.training_info[index]
                }
                if ($scope.subTraining &&
                    $scope.subTraining.constructor instanceof Array &&
                    $scope.subTraining[index]) {
                    $scope.subTraining.splice(index, 1)
                }
            }
        }
        $scope.removeEducation = function (index) {
            var l = $scope.educationDegrees.length;
            if (l > 1) {
                $scope.educationDegrees.splice(index, 1)
                if ($scope.info.form.educationInfo &&
                    $scope.info.form.educationInfo instanceof Object  &&
                    $scope.info.form.educationInfo[index]) {
                    delete $scope.info.form.educationInfo[index]
                }
            }
        }
        $scope.submitForm = function (event) {
            $scope.allLoading = true;
            event.preventDefault();
            var data = new FormData();
            console.log($scope.info.form)
            Object.keys($scope.info.form).forEach(function (key) {
                console.log();
                if (key === 'educationInfo'||key==='training_info') {
                    for (var i = 0; i < Object.keys($scope.info.form[key]).length; i++) {
                        Object.keys($scope.info.form[key][i]).forEach(function (k) {
                            data.append(`${key}[${i}][${k}]`, $scope.info.form[key][i][k]);
                        })
                    }

                }
                else  data.append(key, $scope.info.form[key]);
            })
//            console.log(data.getAll('educationInfo'))
            $http({
                method: 'post',
                url: $scope.info.url,
                data: data,
                headers: {
                    'content-type': undefined
                }
            }).then(function (response) {
//                console.log(response.data)
                window.location.href = '{{URL::route('AVURP.info.index')}}'
            }, function (response) {
                $scope.allLoading = false
                if (response.status === 422) {
                    $scope.errors = response.data;
                }
                else {
                    notificationService.notify("error", response.data.message)
                }
            })
        }
    })
    GlobalApp.directive('fileParse', function () {
        return {
            restrict: 'A',
            link: function (scope, elem, attrs) {
                $(elem).on('change', function () {
                    scope.info.form['profile_pic'] = elem[0].files[0];
                })
            }
        }
    })
</script>
<div ng-controller="InfoController">
    <div class="overlay" style="z-index:100;position: absolute;width: 100%;height: 100%;" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
    </div>
    <form class="form-horizontal" ng-submit="submitForm($event)">
        <fieldset>
            <legend>জিও কোড ভিত্তিক আইডির জন্য তথ্য</legend>
            <div class="form-group">
                <label for="division_id" class="control-label col-sm-4">বিভাগ<sup class="text-red">*</sup>
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <select class="form-control" ng-model="info.form.division_id" id="division_id"
                            ng-change="loadUnit(info.form.division_id)">
                        <option value="">--বিভাগ নির্বাচন করুন--</option>
                        <option ng-repeat="d in divisions" value="[[d.id]]">[[d.division_name_bng]]</option>
                    </select>
                    <p ng-if="errors.division_id&&errors.division_id.length>0" class="text text-danger">
                        [[errors.division_id[0] ]]</p>
                </div>
            </div>
            <div class="form-group">
                <label for="unit_id" class="control-label col-sm-4">জেলা<sup class="text-red">*</sup>
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <select class="form-control" ng-model="info.form.unit_id" id="unit_id"
                            ng-change="loadThana(info.form.division_id,info.form.unit_id)">
                        <option value="">--জেলা নির্বাচন করুন--</option>
                        <option ng-repeat="u in units" value="[[u.id]]">[[u.unit_name_bng]]</option>
                    </select>
                    <p ng-if="errors.unit_id&&errors.unit_id.length>0" class="text text-danger">[[errors.unit_id[0]
                        ]]</p>
                </div>
            </div>
            <div class="form-group">
                <label for="thana_id" class="control-label col-sm-4">উপজেলা<sup class="text-red">*</sup>
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <select class="form-control" ng-model="info.form.thana_id" id="thana_id"
                            ng-change="loadUnion(info.form.division_id,info.form.unit_id,info.form.thana_id)">
                        <option value="">--উপজেলা নির্বাচন করুন--</option>
                        <option ng-repeat="t in thanas" value="[[t.id]]">[[t.thana_name_bng]]</option>
                    </select>
                    <p ng-if="errors.thana_id&&errors.thana_id.length>0" class="text text-danger">[[errors.thana_id[0]
                        ]]</p>
                </div>
            </div>
            <div class="form-group">
                <label for="union_id" class="control-label col-sm-4">ইউনিয়ন<sup class="text-red">*</sup>
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <select class="form-control" ng-model="info.form.union_id" id="union_id">
                        <option value="">--ইউনিয়ন নির্বাচন করুন--</option>
                        <option ng-repeat="u in unions" value="[[u.id]]">[[u.union_name_bng]]</option>
                    </select>
                    <p ng-if="errors.union_id&&errors.union_id.length>0" class="text text-danger">[[errors.union_id[0]
                        ]]</p>
                </div>
            </div>
            <div class="form-group">
                <label for="union_word_id" class="control-label col-sm-4">ইউনিয়নের ওয়ার্ড<sup class="text-red">*</sup>
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <select class="form-control" ng-model="info.form.union_word_id" id="union_word_id">
                        <option value="">--ইউনিয়নের ওয়ার্ড নির্বাচন করুন--</option>
                        <option ng-repeat="uw in unionWords" value="[[uw.id]]">[[uw.number_bng]]</option>
                    </select>
                    <p ng-if="errors.union_word_id&&errors.union_word_id.length>0" class="text text-danger">
                        [[errors.union_word_id[0] ]]</p>
                </div>
            </div>
            <div class="form-group">
                <label for="village_house_no" class="control-label col-sm-4">গ্রাম/বাড়ি নম্বর<sup
                            class="text-red">*</sup>
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" placeholder="গ্রাম/বাড়ি নম্বর"
                           ng-model="info.form.village_house_no" id="village_house_no">
                    <p ng-if="errors.village_house_no&&errors.village_house_no.length>0" class="text text-danger">
                        [[errors.village_house_no[0] ]]</p>
                </div>
            </div>
            <div class="form-group">
                <label for="post_office_name" class="control-label col-sm-4">ডাকঘর<sup class="text-red">*</sup>
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" placeholder="ডাকঘর" ng-model="info.form.post_office_name"
                           id="post_office_name">
                    <p ng-if="errors.post_office_name&&errors.post_office_name.length>0" class="text text-danger">
                        [[errors.post_office_name[0] ]]</p>
                </div>
            </div>
        </fieldset>
        <fieldset>
            <legend>ব্যক্তিগত ও পারিবারিক তথ্য</legend>
            <div class="form-group">
                <label for="ansar_name_eng" class="control-label col-sm-4">Name(CAP)<sup class="text-red">*</sup>
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" placeholder="Name(CAP)" ng-model="info.form.ansar_name_eng"
                           id="ansar_name_eng">
                    <p ng-if="errors.ansar_name_eng&&errors.ansar_name_eng.length>0" class="text text-danger">
                        [[errors.ansar_name_eng[0] ]]</p>
                </div>
            </div>
            <div class="form-group">
                <label for="ansar_name_bng" class="control-label col-sm-4">নাম<sup class="text-red">*</sup>
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" placeholder="নাম" ng-model="info.form.ansar_name_bng"
                           id="ansar_name_bng">
                    <p ng-if="errors.ansar_name_bng&&errors.ansar_name_bng.length>0" class="text text-danger">
                        [[errors.ansar_name_bng[0] ]]</p>
                </div>
            </div>
            <div class="form-group">
                <label for="rank" class="control-label col-sm-4">বর্তমান পদবী<sup class="text-red">*</sup>
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" placeholder="বর্তমান পদবী" ng-model="info.form.designation"
                           id="rank">
                    <p ng-if="errors.designation&&errors.designation.length>0" class="text text-danger">
                        [[errors.designation[0] ]]</p>
                </div>
            </div>
            <div class="form-group">
                <label for="father_name_bng" class="control-label col-sm-4">পিতার নাম<sup class="text-red">*</sup>
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" placeholder="পিতার নাম" ng-model="info.form.father_name_bng"
                           id="father_name_bng">
                    <p ng-if="errors.father_name_bng&&errors.father_name_bng.length>0" class="text text-danger">
                        [[errors.father_name_bng[0] ]]</p>
                </div>
            </div>
            <div class="form-group">
                <label for="mother_name_bng" class="control-label col-sm-4">মাতার নাম<sup class="text-red">*</sup>
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" placeholder="মাতার নাম" ng-model="info.form.mother_name_bng"
                           id="mother_name_bng">
                    <p ng-if="errors.mother_name_bng&&errors.mother_name_bng.length>0" class="text text-danger">
                        [[errors.mother_name_bng[0] ]]</p>
                </div>
            </div>
            <div class="form-group">
                <label for="date_of_birth" class="control-label col-sm-4">জন্ম তারিখ<sup class="text-red">*</sup>
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <input type="text" date-picker class="form-control" ng-model="info.form.date_of_birth"
                           id="date_of_birth" date-picker-big="[[info.form.date_of_birth]]">
                    <p ng-if="errors.date_of_birth&&errors.date_of_birth.length>0" class="text text-danger">
                        [[errors.date_of_birth[0] ]]</p>
                </div>
            </div>
            {{--        <div class="form-group">
                        <label for="base_of_birth_date" class="control-label col-sm-4">জন্মতারিখের ভিত্তি<sup class="text-red">*</sup>
                            <span class="pull-right">:</span>
                        </label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" placeholder="জন্মতারিখের ভিত্তি" ng-model="info.form.base_of_birth_date" id="base_of_birth_date">
                        </div>
                    </div>--}}
            <div class="form-group">
                <label for="marital_status" class="control-label col-sm-4">বৈবাহিক অবস্থা<sup class="text-red">*</sup>
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <select class="form-control" ng-model="info.form.marital_status" id="marital_status">
                        <option value="">বৈবাহিক অবস্থা নির্বাচন করুন</option>
                        <option value="Married">বিবাহিত</option>
                        <option value="Unmarried">অবিবাহিত</option>
                        <option value="Widow">বিধবা</option>
                        <option value="Divorced">তালাকপ্রাপ্ত</option>
                    </select>
                    <p ng-if="errors.matital_status&&errors.matital_status.length>0" class="text text-danger">
                        [[errors.matital_status[0] ]]</p>
                </div>
            </div>
            <div class="form-group">
                <label for="spouse_name" class="control-label col-sm-4">স্ত্রী/স্বামীর নাম
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" placeholder="স্ত্রী/স্বামীর নাম"
                           ng-model="info.form.spouse_name" id="spouse_name">
                    <p ng-if="errors.spouse_name&&errors.spouse_name.length>0" class="text text-danger">
                        [[errors.spouse_name[0] ]]</p>
                </div>
            </div>
            <div class="form-group">
                <label for="national_id_no" class="control-label col-sm-4">জাতীয় পরিচয় পত্র নম্বর<sup
                            class="text-red">*</sup>
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" ng-keypress="validateKey(47,56,$event)"
                           placeholder="জাতীয় পরিচয় পত্র নম্বর" ng-model="info.form.national_id_no" id="national_id_no">
                    <p ng-if="errors.national_id_no&&errors.national_id_no.length>0" class="text text-danger">
                        [[errors.national_id_no[0] ]]</p>
                </div>
            </div>
            <div class="form-group">
                <label for="smart_card_id" class="control-label col-sm-4">স্মার্টকার্ড আইডি
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" ng-keypress="validateKey(47,56,$event)"
                           placeholder="স্মার্টকার্ড আইডি" ng-model="info.form.smart_card_id" id="smart_card_id">

                </div>
            </div>
            <div class="form-group">
                <label for="avub_id" class="control-label col-sm-4">এভিইউবি আইডি
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" placeholder="এভিইউবি আইডি" ng-model="info.form.avub_id"
                           id="avub_id">

                </div>
            </div>

        </fieldset>
        <fieldset>
            <legend>যোগাযোগ</legend>
            <div class="form-group">
                <label for="mobile_no_self" class="control-label col-sm-4">মোবাইল নম্বর(নিজ)<sup
                            class="text-red">*</sup>
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" placeholder="মোবাইল নম্বর"
                           ng-model="info.form.mobile_no_self" id="mobile_no_self">
                    <p ng-if="errors.mobile_no_self&&errors.mobile_no_self.length>0" class="text text-danger">
                        [[errors.mobile_no_self[0] ]]</p>
                </div>
            </div>
            <div class="form-group">
                <label for="mobile_no_request" class="control-label col-sm-4">মোবাইল নম্বর(অনুরোধ)
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" placeholder="মোবাইল নম্বর"
                           ng-model="info.form.mobile_no_request" id="mobile_no_request">

                </div>
            </div>
            <div class="form-group">
                <label for="email_id" class="control-label col-sm-4">ইমেইল
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" placeholder="ইমেইল"
                           ng-model="info.form.email_id" id="email_id">

                </div>
            </div>
            <div class="form-group">
                <label for="fb_id" class="control-label col-sm-4">ফেসবুক আইডি
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" placeholder="ফেসবুক আইডি"
                           ng-model="info.form.fb_id" id="fb_id">

                </div>
            </div>

        </fieldset>
        <fieldset>
            <legend>শারিরিক যোগ্যতার তথ্য</legend>
            <div class="form-group">
                <label for="" class="control-label col-sm-4">উচ্চতা<sup class="text-red">*</sup>
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <div class="form-group">
                        <div class="col-sm-6">
                            <input type="text" class="form-control" placeholder="Feet" ng-model="info.form.height_feet"
                                   id="height_feet">
                            <p ng-if="errors.height_feet&&errors.height_feet.length>0" class="text text-danger">
                                [[errors.height_feet[0] ]]</p>
                        </div>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" placeholder="Inch" ng-model="info.form.height_inch"
                                   id="height_inch">
                            <p ng-if="errors.height_inch&&errors.height_inch.length>0" class="text text-danger">
                                [[errors.height_inch[0] ]]</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="blood_group_id" class="control-label col-sm-4">রক্তের গ্রুপ<sup class="text-red">*</sup>
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <select id="blood_group_id" ng-model="info.form.blood_group_id" class="form-control">
                        <option value="">--রক্তের গ্রুপ নির্বাচন করুন</option>
                        <option ng-repeat="b in bloodGroups" value="[[b.id]]">[[b.blood_group_name_bng]]</option>
                    </select>
                    <p ng-if="errors.blood_group_id&&errors.blood_group_id.length>0" class="text text-danger">
                        [[errors.blood_group_id[0] ]]</p>
                </div>
            </div>
            <div class="form-group">
                <label for="gender" class="control-label col-sm-4">লিঙ্গ<sup class="text-red">*</sup>
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <select id="gender" ng-model="info.form.gender" class="form-control">
                        <option value="">--লিঙ্গ নির্বাচন করুন</option>
                        <option ng-repeat="g in genders" value="[[g.value]]">[[g.text]]</option>
                    </select>
                    <p ng-if="errors.gender&&errors.gender.length>0" class="text text-danger">[[errors.gender[0] ]]</p>
                </div>
            </div>
            <div class="form-group">
                <label for="health_condition" class="control-label col-sm-4">স্বাস্থ্যগত অবস্থা<sup
                            class="text-red">*</sup>
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" ng-model="info.form.health_condition"
                           placeholder="স্বাস্থ্যগত অবস্থা">
                    <p ng-if="errors.health_condition&&errors.health_condition.length>0" class="text text-danger">
                        [[errors.health_condition[0] ]]</p>
                </div>
            </div>

        </fieldset>
        <fieldset>
            <legend>
                শিক্ষাগত যোগ্যতার ও প্রশিক্ষনের তথ্য
            </legend>
            <div class="form-group">
                <div class="row">
                    <label for="" class="control-label col-sm-4">শিক্ষাগত যোগ্যতা<sup class="text-red">*</sup>
                        <span class="pull-right">:</span>
                    </label>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <table class="table table-bordered table-condensed">
                                <tr>
                                    <th>শিক্ষাগত যোগ্যতা</th>
                                    <th>শিক্ষা প্রতিষ্ঠানের নাম</th>
                                    <th>পাশ করার সাল</th>
                                    <th>বিভাগ / শ্রেণী</th>
                                    <th>Action</th>
                                </tr>
                                <tr ng-repeat="e in educationDegrees track by $index">
                                    <td>
                                        <select name="" id="" ng-model="info.form.educationInfo[$index].education_id">
                                            <option value="">--নির্বাচন করুন--</option>
                                            <option ng-repeat="deg in educations" value="[[deg.id]]">
                                                [[deg.education_deg_bng]]
                                            </option>
                                        </select>
                                        <p ng-if="errors['educationInfo.'+$index+'.education_id']&&errors['educationInfo.'+$index+'.education_id'].length>0"
                                           class="text text-danger">[[errors['educationInfo.'+$index+'.education_id'][0]
                                            ]]</p>
                                    </td>
                                    <td>
                                        <input type="text" ng-model="info.form.educationInfo[$index].institute_name"
                                               placeholder="প্রতিষ্ঠানের নাম">
                                        <p ng-if="errors['educationInfo.'+$index+'.institute_name']&&errors['educationInfo.'+$index+'.institute_name'].length>0"
                                           class="text text-danger">
                                            [[errors['educationInfo.'+$index+'.institute_name'][0]
                                            ]]</p>
                                    </td>
                                    <td>
                                        <input type="text" ng-model="info.form.educationInfo[$index].passing_year"
                                               placeholder="পাশের সাল">
                                    </td>
                                    <td>
                                        <input type="text" ng-model="info.form.educationInfo[$index].gade_divission"
                                               placeholder="গ্রেড/বিভাগ">
                                    </td>
                                    <td>
                                        <a class="btn btn-danger btn-xs"
                                           ng-click="removeEducation($index)">
                                            <i class="fa fa-minus"></i>
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            <p ng-if="errors.educationInfo&&errors.educationInfo.length>0" class="text text-danger">
                                [[errors.educationInfo[0] ]]</p>
                        </div>

                        <a class="btn btn-primary pull-right btn-xs"
                           ng-click="educationDegrees.push(educationDegrees.length+1)">
                            <i class="fa fa-plus"></i>&nbsp;Add More
                        </a>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label for="training_info" class="control-label col-sm-4">প্রশিক্ষণ<sup class="text-red">*</sup>
                        <span class="pull-right">:</span>
                    </label>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <table class="table table-bordered table-condensed">
                                <tr>
                                    <th>প্রধান প্রশিক্ষণ নাম</th>
                                    <th>উপ প্রশিক্ষণ নাম</th>
                                    <th>প্রতিষ্ঠানের নাম</th>
                                    <th>সনদ পত্র নং</th>
                                    <th>প্রশিক্ষণ শুরুর তারিখ</th>
                                    <th>প্রশিক্ষণ শেষের তারিখ</th>
                                    <th>Action</th>
                                </tr>
                                <tr ng-repeat="t in training_info track by $index">
                                    <td>
                                        <select ng-model="info.form.training_info[$index].training_id"
                                                ng-change="loadSubTraining(info.form.training_info[$index].training_id,$index)">
                                            <option value="">--নির্বাচন করুন--</option>
                                            <option ng-repeat="mt in mainTraining" value="[[mt.id]]">
                                                [[mt.training_name_bng]]
                                            </option>
                                        </select>
                                        <p ng-if="errors['training_info.'+$index+'.training_id']&&errors['training_info.'+$index+'.training_id'].length>0"
                                           class="text text-danger">[[errors['training_info.'+$index+'.training_id'][0]
                                            ]]</p>
                                    </td>
                                    <td>
                                        <select ng-model="info.form.training_info[$index].sub_training_id">
                                            <option value="">--নির্বাচন করুন--</option>
                                            <option ng-repeat="st in subTraining[$index]" value="[[st.id]]">
                                                [[st.training_name_bng]]
                                            </option>
                                        </select>
                                        <p ng-if="errors['training_info.'+$index+'.sub_training_id']&&errors['training_info.'+$index+'.sub_training_id'].length>0"
                                           class="text text-danger">[[errors['training_info.'+$index+'.sub_training_id'][0]
                                            ]]</p>
                                    </td>
                                    <td>
                                        <input type="text" ng-model="info.form.training_info[$index].institute_name"
                                               placeholder="প্রতিষ্ঠানের নাম">
                                    </td>
                                    <td>
                                        <input type="text" ng-model="info.form.training_info[$index].certificate_no"
                                               placeholder="সনদ পত্র নং">
                                    </td>
                                    <td>
                                        <input type="text"
                                               date-picker-big="[[info.form.training_info[$index].training_start_date]]"
                                               ng-model="info.form.training_info[$index].training_start_date"
                                               placeholder="প্রশিক্ষণ শুরুর তারিখ">
                                    </td>
                                    <td>
                                        <input type="text"
                                               date-picker-big="[[info.form.training_info[$index].training_end_date]]"
                                               ng-model="info.form.training_info[$index].training_end_date"
                                               placeholder="প্রশিক্ষণ শেষের তারিখ">
                                    </td>
                                    <td>
                                        <a class="btn btn-danger btn-xs" ng-click="removeTraining($index)">
                                            <i class="fa fa-minus"></i>
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <p ng-if="errors.training_info&&errors.training_info.length>0" class="text text-danger">
                            [[errors.training_info[0] ]]</p>
                        <a class="btn btn-primary pull-right btn-xs"
                           ng-click="training_info.push(training_info.length+1)">
                            <i class="fa fa-plus"></i>&nbsp;Add More
                        </a>
                    </div>
                </div>

            </div>
        </fieldset>
        <fieldset>
            <legend>ছবি সুমুহ</legend>
            <div class="form-group">
                <label for="profile_pic" class="control-label col-sm-4">প্রোফাইল পিকচার<span class="pull-right">:</span></label>
                <div class="col-sm-8">
                    <input type="file" file-parse>
                </div>
            </div>
        </fieldset>
        <button type="submit" class="btn btn-primary pull-right">
            <i class="fa fa-file"></i>&nbsp;Submit
        </button>
    </form>
</div>