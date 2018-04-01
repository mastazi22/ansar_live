
<style>
    .control-label{
        text-align: left !important;
    }
</style>
<script>
    GlobalApp.controller('InfoController',function ($scope, $http, httpService,$q) {
        $scope.info = {};
        $scope.errors = {};
        $scope.info.url = '{{$url}}'
        $scope.educationDegrees = [1];
        $scope.genders = [
            {
                value:'Male',
                text:'Male'
            },
            {
                value:'Female',
                text:'Female'
            }
        ]
        $scope.unionWords = [
            {id:1,number_bng:'ওয়ার্ড-০১'},
            {id:2,number_bng:'ওয়ার্ড-০২'},
            {id:3,number_bng:'ওয়ার্ড-০৩'},
            {id:4,number_bng:'ওয়ার্ড-০৪'},
            {id:5,number_bng:'ওয়ার্ড-০৫'},
            {id:6,number_bng:'ওয়ার্ড-০৬'},
            {id:7,number_bng:'ওয়ার্ড-০৭'},
            {id:8,number_bng:'ওয়ার্ড-০৮'},
            {id:9,number_bng:'ওয়ার্ড-০৯'},
        ]
        $scope.ranks = [
            {value:'ansar',text:'আনসার'},
            {value:'vdp',text:'ভিডিপি'},
            {value:'tdp',text:'টিডিপি'},
            {value:'basic',text:'মৌলিক/পেশাভিত্তিক'},
        ]
        $q.all([
            httpService.range(),
            httpService.bloodGroup(),
            httpService.education()
        ]).then(function (response) {
            $scope.divisions = response[0];
            $scope.bloodGroups = response[1];
            $scope.educations = response[2];
        })
        $scope.loadUnit= function (rangeId) {
            httpService.unit(rangeId).then(function (response) {
                $scope.units = response;
            })
        }
        $scope.loadThana= function (rangeId,unitId) {
            httpService.thana(rangeId,unitId).then(function (response) {
                $scope.thanas = response;
            })
        }
        $scope.submitForm = function (event) {
            event.preventDefault();
            $http({
                method:'post',
                url:$scope.info.url,
                data:angular.toJson($scope.info.form)
            }).then(function (response) {
                console.log(response.data);
            },function (response) {
                if(response.status===422) {
                    $scope.errors = response.data;
                }
                console.log(response.data)
            })
        }
    })
</script>
<div ng-controller="InfoController">
    <form  class="form-horizontal" ng-submit="submitForm($event)">
        <fieldset>
            <legend>জিও কোড ভিত্তিক আইডির জন্য তথ্য</legend>
            <div class="form-group">
                <label for="division_id" class="control-label col-sm-4">বিভাগ<sup class="text-red">*</sup>
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <select class="form-control" ng-model="info.form.division_id" id="division_id" ng-change="loadUnit(info.form.division_id)">
                        <option value="">--বিভাগ নির্বাচন করুন--</option>
                        <option ng-repeat="d in divisions" value="[[d.id]]">[[d.division_name_bng]]</option>
                    </select>
                    <p ng-if="errors.division_id&&errors.division_id.length>0" class="text text-danger">[[errors.division_id[0] ]]</p>
                </div>
            </div>
            <div class="form-group">
                <label for="unit_id" class="control-label col-sm-4">জেলা<sup class="text-red">*</sup>
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <select class="form-control" ng-model="info.form.unit_id" id="unit_id" ng-change="loadThana(info.form.division_id,info.form.unit_id)">
                        <option value="">--জেলা নির্বাচন করুন--</option>
                        <option ng-repeat="u in units" value="[[u.id]]">[[u.unit_name_bng]]</option>
                    </select>
                    <p ng-if="errors.unit_id&&errors.unit_id.length>0" class="text text-danger">[[errors.unit_id[0] ]]</p>
                </div>
            </div>
            <div class="form-group">
                <label for="thana_id" class="control-label col-sm-4">উপজেলা<sup class="text-red">*</sup>
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <select class="form-control" ng-model="info.form.thana_id" id="thana_id">
                        <option value="">--উপজেলা নির্বাচন করুন--</option>
                        <option ng-repeat="t in thanas" value="[[t.id]]">[[t.thana_name_bng]]</option>
                    </select>
                    <p ng-if="errors.thana_id&&errors.thana_id.length>0" class="text text-danger">[[errors.thana_id[0] ]]</p>
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
                    <p ng-if="errors.union_id&&errors.union_id.length>0" class="text text-danger">[[errors.union_id[0] ]]</p>
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
                    <p ng-if="errors.union_word_id&&errors.union_word_id.length>0" class="text text-danger">[[errors.union_word_id[0] ]]</p>
                </div>
            </div>
            <div class="form-group">
                <label for="village_house_no" class="control-label col-sm-4">গ্রাম/বাড়ি নম্বর<sup class="text-red">*</sup>
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" placeholder="গ্রাম/বাড়ি নম্বর" ng-model="info.form.village_house_no" id="village_house_no">
                    <p ng-if="errors.village_house_no&&errors.village_house_no.length>0" class="text text-danger">[[errors.village_house_no[0] ]]</p>
                </div>
            </div>
            <div class="form-group">
                <label for="post_office" class="control-label col-sm-4">ডাকঘর<sup class="text-red">*</sup>
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" placeholder="ডাকঘর" ng-model="info.form.post_office" id="post_office">
                    <p ng-if="errors.post_office&&errors.post_office.length>0" class="text text-danger">[[errors.post_office[0] ]]</p>
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
                    <input type="text" class="form-control" placeholder="Name(CAP)" ng-model="info.form.ansar_name_eng" id="ansar_name_eng">
                    <p ng-if="errors.ansar_name_eng&&errors.ansar_name_eng.length>0" class="text text-danger">[[errors.ansar_name_eng[0] ]]</p>
                </div>
            </div>
            <div class="form-group">
                <label for="ansar_name_bng" class="control-label col-sm-4">নাম<sup class="text-red">*</sup>
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" placeholder="নাম" ng-model="info.form.ansar_name_bng" id="ansar_name_bng">
                    <p ng-if="errors.ansar_name_bng&&errors.ansar_name_bng.length>0" class="text text-danger">[[errors.ansar_name_bng[0] ]]</p>
                </div>
            </div>
            <div class="form-group">
                <label for="rank" class="control-label col-sm-4">বর্তমান পদবী<sup class="text-red">*</sup>
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" placeholder="বর্তমান পদবী" ng-model="info.form.designation" id="rank">
                    <p ng-if="errors.designation&&errors.designation.length>0" class="text text-danger">[[errors.designation[0] ]]</p>
                </div>
            </div>
            <div class="form-group">
                <label for="father_name_bng" class="control-label col-sm-4">পিতার নাম<sup class="text-red">*</sup>
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" placeholder="পিতার নাম" ng-model="info.form.father_name_bng" id="father_name_bng">
                    <p ng-if="errors.father_name_bng&&errors.father_name_bng.length>0" class="text text-danger">[[errors.father_name_bng[0] ]]</p>
                </div>
            </div>
            <div class="form-group">
                <label for="mother_name_bng" class="control-label col-sm-4">মাতার নাম<sup class="text-red">*</sup>
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" placeholder="মাতার নাম" ng-model="info.form.mother_name_bng" id="mother_name_bng">
                    <p ng-if="errors.mother_name_bng&&errors.mother_name_bng.length>0" class="text text-danger">[[errors.mother_name_bng[0] ]]</p>
                </div>
            </div>
            <div class="form-group">
                <label for="date_of_birth" class="control-label col-sm-4">জন্ম তারিখ<sup class="text-red">*</sup>
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <input type="text" date-picker class="form-control" ng-model="info.form.date_of_birth" id="date_of_birth">
                    <p ng-if="errors.date_of_birth&&errors.date_of_birth.length>0" class="text text-danger">[[errors.date_of_birth[0] ]]</p>
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
                        <option value="Married">Married</option>
                        <option value="Unmarried">Unmarried</option>
                    </select>
                    <p ng-if="errors.matital_status&&errors.matital_status.length>0" class="text text-danger">[[errors.matital_status[0] ]]</p>
                </div>
            </div>
            <div class="form-group">
                <label for="spouse_name" class="control-label col-sm-4">স্ত্রী/স্বামীর নাম
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" placeholder="স্ত্রী/স্বামীর নাম" ng-model="info.form.spouse_name" id="spouse_name">
                    <p ng-if="errors.spouse_name&&errors.spouse_name.length>0" class="text text-danger">[[errors.spouse_name[0] ]]</p>
                </div>
            </div>
            <div class="form-group">
                <label for="national_id_no" class="control-label col-sm-4">জাতীয় পরিচয় পত্র নম্বর<sup class="text-red">*</sup>
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" ng-keypress="validateKey(47,56,$event)" placeholder="জাতীয় পরিচয় পত্র নম্বর" ng-model="info.form.national_id_no" id="national_id_no">
                    <p ng-if="errors.national_id_no&&errors.national_id_no.length>0" class="text text-danger">[[errors.national_id_no[0] ]]</p>
                </div>
            </div>
            <div class="form-group">
                <label for="ansar_id" class="control-label col-sm-4">স্মার্টকার্ড আইডি
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" ng-keypress="validateKey(47,56,$event)" placeholder="স্মার্টকার্ড আইডি" ng-model="info.form.ansar_id" id="ansar_id">

                </div>
            </div>
            <div class="form-group">
                <label for="avub_id" class="control-label col-sm-4">এভিইউবি আইডি
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" placeholder="এভিইউবি আইডি" ng-model="info.form.avub_id" id="avub_id">

                </div>
            </div>

        </fieldset>
        <fieldset>
            <legend>যোগাযোগ</legend>
            <div class="form-group">
                <label for="mobile_no_self" class="control-label col-sm-4">মোবাইল নম্বর(নিজ)<sup class="text-red">*</sup>
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" placeholder="মোবাইল নম্বর" ng-model="info.form.mobile_no_self" id="mobile_no_self">
                    <p ng-if="errors.mobile_no_self&&errors.mobile_no_self.length>0" class="text text-danger">[[errors.mobile_no_self[0] ]]</p>
                </div>
            </div>
            <div class="form-group">
                <label for="mobile_no_request" class="control-label col-sm-4">মোবাইল নম্বর(অনুরোধ)
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" placeholder="মোবাইল নম্বর" ng-model="info.form.mobile_no_request" id="mobile_no_request">

                </div>
            </div>
            <div class="form-group">
                <label for="email_or_fb_id" class="control-label col-sm-4">ইমেইল/ফেসবুক আইডি
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" placeholder="ইমেইল/ফেসবুক আইডি" ng-model="info.form.email_or_fb_id" id="email_or_fb_id">

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
                            <input type="text" class="form-control" placeholder="Feet" ng-model="info.form.height_feet" id="height_feet">
                            <p ng-if="errors.height_feet&&errors.height_feet.length>0" class="text text-danger">[[errors.height_feet[0] ]]</p>
                        </div>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" placeholder="Inch" ng-model="info.form.height_inch" id="height_inch">
                            <p ng-if="errors.height_inch&&errors.height_inch.length>0" class="text text-danger">[[errors.height_inch[0] ]]</p>
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
                    <p ng-if="errors.blood_group_id&&errors.blood_group_id.length>0" class="text text-danger">[[errors.blood_group_id[0] ]]</p>
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
                <label for="health_condition" class="control-label col-sm-4">স্বাস্থ্যগত অবস্থা<sup class="text-red">*</sup>
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" ng-model = "info.form.health_condition" placeholder="স্বাস্থ্যগত অবস্থা">
                    <p ng-if="errors.health_condition&&errors.health_condition.length>0" class="text text-danger">[[errors.health_condition[0] ]]</p>
                </div>
            </div>

        </fieldset>
        <fieldset>
            <legend>
                শিক্ষাগত যোগ্যতার ও প্রশিক্ষনের তথ্য
            </legend>
            <div class="form-group">
                <label for="" class="control-label col-sm-2">শিক্ষাগত যোগ্যতা<sup class="text-red">*</sup></label>
                <div class="col-sm-10">
                    <div class="table-responsive">
                        <table class="table table-bordered table-condensed">
                            <tr>
                                <th>শিক্ষাগত যোগ্যতা</th>
                                <th>শিক্ষা প্রতিষ্ঠানের নাম</th>
                                <th>পাশ করার সাল</th>
                                <th>বিভাগ / শ্রেণী</th>
                                <th>Action</th>
                            </tr>
                            <tr ng-repeat="e in educationDegrees">
                                <td>
                                    <select name="" id="" ng-model="info.form.educationInfo[$index].education_id">
                                        <option value="">--নির্বাচন করুন--</option>
                                        <option ng-repeat="deg in educations" value="[[deg.id]]">[[deg.education_deg_bng]]</option>
                                    </select>
                                    <p ng-if="errors['educationInfo.'+$index+'.education_id']&&errors['educationInfo.'+$index+'.education_id'].length>0" class="text text-danger">[[errors['educationInfo.'+$index+'.education_id'][0] ]]</p>
                                </td>
                                <td>
                                    <input type="text" ng-model="info.form.educationInfo[$index].institute_name" placeholder="প্রতিষ্ঠানের নাম">
                                    <p ng-if="errors['educationInfo.'+$index+'.institute_name']&&errors['educationInfo.'+$index+'.institute_name'].length>0" class="text text-danger">[[errors['educationInfo.'+$index+'.institute_name'][0] ]]</p>
                                </td>
                                <td>
                                    <input type="text" ng-model="info.form.educationInfo[$index].passing_year" placeholder="পাশের সাল">
                                </td>
                                <td>
                                    <input type="text" ng-model="info.form.educationInfo[$index].gade_divission" placeholder="গ্রেড/বিভাগ">
                                </td>
                                <td>
                                    <a class="btn btn-danger btn-xs" ng-click="educationDegrees.length>1?educationDegrees.splice($index):''">
                                        <i class="fa fa-minus"></i>
                                    </a>
                                </td>
                            </tr>
                        </table>
                        <p ng-if="errors.educationInfo&&errors.educationInfo.length>0" class="text text-danger">[[errors.educationInfo[0] ]]</p>
                    </div>

                    <a class="btn btn-primary pull-right btn-xs" ng-click="educationDegrees.push(educationDegrees.length+1)">
                        <i class="fa fa-plus"></i>&nbsp;Add More
                    </a>
                </div>
            </div>
            <div class="form-group">
                <label for="training_info" class="control-label col-sm-4">প্রশিক্ষণ<sup class="text-red">*</sup>
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <select id="training_info" ng-model="info.form.training_info" class="form-control">
                        <option value="">--প্রশিক্ষণ নির্বাচন করুন</option>
                        <option ng-repeat="r in ranks" value="[[r.value]]">[[r.text]]</option>
                    </select>
                    <p ng-if="errors.training_info&&errors.training_info.length>0" class="text text-danger">[[errors.training_info[0] ]]</p>
                </div>
            </div>
        </fieldset>
        <button type="submit" class="btn btn-primary pull-right">
            <i class="fa fa-file"></i>&nbsp;Submit
        </button>
    </form>
</div>