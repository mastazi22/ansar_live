
<style>
    .control-label{
        text-align: left !important;
    }
</style>
<form action="[[info.url]]" class="form-horizontal">
    <fieldset>
        <legend>জিও কোড ভিত্তিক আইডির জন্য তথ্য</legend>
        <div class="form-group">
            <label for="division_id" class="control-label col-sm-4">বিভাগ<sup class="text-red">*</sup>
                <span class="pull-right">:</span>
            </label>
            <div class="col-sm-8">
                <select class="form-control" ng-model="info.form.division_id" id="division_id">
                    <option value="">--বিভাগ নির্বাচন করুন--</option>
                    <option ng-repeat="d in divisions" value="[[d.id]]">[[d.division_name_bng]]</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="unit_id" class="control-label col-sm-4">জেলা<sup class="text-red">*</sup>
                <span class="pull-right">:</span>
            </label>
            <div class="col-sm-8">
                <select class="form-control" ng-model="info.form.unit_id" id="unit_id">
                    <option value="">--জেলা নির্বাচন করুন--</option>
                    <option ng-repeat="u in units" value="[[u.id]]">[[u.unit_name_bng]]</option>
                </select>
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
            </div>
        </div>
        <div class="form-group">
            <label for="village_house_no" class="control-label col-sm-4">গ্রাম/বাড়ি নম্বর<sup class="text-red">*</sup>
                <span class="pull-right">:</span>
            </label>
            <div class="col-sm-8">
                <input type="text" class="form-control" placeholder="গ্রাম/বাড়ি নম্বর" ng-model="info.form.village_house_no" id="village_house_no">
            </div>
        </div>
        <div class="form-group">
            <label for="post_office" class="control-label col-sm-4">ডাকঘর<sup class="text-red">*</sup>
                <span class="pull-right">:</span>
            </label>
            <div class="col-sm-8">
                <input type="text" class="form-control" placeholder="ডাকঘর" ng-model="info.form.post_office" id="post_office">
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
            </div>
        </div>
        <div class="form-group">
            <label for="ansar_name_bng" class="control-label col-sm-4">নাম<sup class="text-red">*</sup>
                <span class="pull-right">:</span>
            </label>
            <div class="col-sm-8">
                <input type="text" class="form-control" placeholder="নাম" ng-model="info.form.ansar_name_bng" id="ansar_name_bng">
            </div>
        </div>
        <div class="form-group">
            <label for="rank" class="control-label col-sm-4">বর্তমান পদবী<sup class="text-red">*</sup>
                <span class="pull-right">:</span>
            </label>
            <div class="col-sm-8">
                <input type="text" class="form-control" placeholder="বর্তমান পদবী" ng-model="info.form.rank" id="rank">
            </div>
        </div>
        <div class="form-group">
            <label for="father_name_bng" class="control-label col-sm-4">পিতার নাম<sup class="text-red">*</sup>
                <span class="pull-right">:</span>
            </label>
            <div class="col-sm-8">
                <input type="text" class="form-control" placeholder="পিতার নাম" ng-model="info.form.father_name_bng" id="father_name_bng">
            </div>
        </div>
        <div class="form-group">
            <label for="mother_name_bng" class="control-label col-sm-4">মাতার নাম<sup class="text-red">*</sup>
                <span class="pull-right">:</span>
            </label>
            <div class="col-sm-8">
                <input type="text" class="form-control" placeholder="মাতার নাম" ng-model="info.form.mother_name_bng" id="mother_name_bng">
            </div>
        </div>
        <div class="form-group">
            <label for="date_of_birth" class="control-label col-sm-4">জন্ম তারিখ<sup class="text-red">*</sup>
                <span class="pull-right">:</span>
            </label>
            <div class="col-sm-8">
                <input type="text" date-picker class="form-control" ng-model="info.form.date_of_birth" id="date_of_birth">
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
                    <option value="married">Married</option>
                    <option value="unmarried">Unmarried</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="spouse_name" class="control-label col-sm-4">স্ত্রী/স্বামীর নাম
                <span class="pull-right">:</span>
            </label>
            <div class="col-sm-8">
                <input type="text" class="form-control" placeholder="স্ত্রী/স্বামীর নাম" ng-model="info.form.spouse_name" id="spouse_name">

            </div>
        </div>
        <div class="form-group">
            <label for="national_id_no" class="control-label col-sm-4">জাতীয় পরিচয় পত্র নম্বর<sup class="text-red">*</sup>
                <span class="pull-right">:</span>
            </label>
            <div class="col-sm-8">
                <input type="text" class="form-control" ng-keypress="validateKey(47,56,$event)" placeholder="জাতীয় পরিচয় পত্র নম্বর" ng-model="info.form.national_id_no" id="national_id_no">

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
                    </div>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" placeholder="Inch" ng-model="info.form.height_inch" id="height_inch">
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="blood_group" class="control-label col-sm-4">রক্তের গ্রুপ<sup class="text-red">*</sup>
                <span class="pull-right">:</span>
            </label>
            <div class="col-sm-8">
                <select id="blood_group" ng-model="info.data.blood_group" class="form-control">
                    <option value="">--রক্তের গ্রুপ নির্বাচন করুন</option>
                    <option ng-repeat="b in bloodGroups" value="[[b.id]]">[[b.blood_group_name_bng]]</option>
                </select>

            </div>
        </div>
        <div class="form-group">
            <label for="gender" class="control-label col-sm-4">লিঙ্গ<sup class="text-red">*</sup>
                <span class="pull-right">:</span>
            </label>
            <div class="col-sm-8">
                <select id="gender" ng-model="info.data.gender" class="form-control">
                    <option value="">--লিঙ্গ নির্বাচন করুন</option>
                    <option ng-repeat="g in gender" value="[[g.value]]">[[g.text]]</option>
                </select>

            </div>
        </div>
        <div class="form-group">
            <label for="health_condition" class="control-label col-sm-4">স্বাস্থ্যগত অবস্থা<sup class="text-red">*</sup>
                <span class="pull-right">:</span>
            </label>
            <div class="col-sm-8">
                <input type="text" class="form-control" ng-model = "info.data.health_condition" placeholder="স্বাস্থ্যগত অবস্থা">
            </div>
        </div>

    </fieldset>
    <fieldset>
        <legend>
            শিক্ষাগত যোগ্যতার ও প্রশিক্ষনের তথ্য
        </legend>
    </fieldset>
</form>