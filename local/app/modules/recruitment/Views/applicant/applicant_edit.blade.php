<div id="entryform" ng-controller="fullEntryFormController" d-picker ng-init="loadApplicantDetail()">

    <div>

        <section class="content">

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
                                               ng-model="formData.applicant_name_eng"
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
                                               name="formData.applicant_name_bng"
                                               ng-model="formData.applicant_name_bng"
                                               placeholder="আপনার নাম লিখুন"/>
                                        <span style="color:red"
                                              ng-show="formSubmitResult.error.applicant_name_bng[0]">[[ formSubmitResult.error.applicant_name_bng[0] ]]</span>
                                    </div>
                                </div>
                                {{--End Ansar Name (Bangla) Field --}}
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
                                               name="date_of_birth" ng-model="formData.date_of_birth" date-picker=""
                                               placeholder="Date of birth"
                                               value="{{Request::old('date_of_birth')}}">
                                        <span style="color:red"
                                              ng-show="formSubmitResult.error.date_of_birth[0]">[[ formSubmitResult.error.date_of_birth[0] ]]</span>
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
                            </div>
                        </div>

                    </fieldset>
                    <fieldset>
                        <div class="level-title-session-entry">
                            <h5 style="text-align: center;">স্থায়ী ঠিকানা</h5>
                        </div>
                        <div class="box box-info">
                            <div class="box-body">

                                <div class="form-horizontal col-md-12"
                                     ng-if="isEditable('village_name_bng')>=0">
                                    <label class="control-label col-sm-2" for="email">গ্রাম/বাড়ি নং:</label>
                                    <div class="col-sm-10">
                                        <input class="form-control  " id="village_name_bng"
                                               name="village_name_bng" ng-model="formData.village_name_bng"
                                               type="text" placeholder=" গ্রাম">
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
                                               ng-model="formData.height_feet" type="text" placeholder=" FEET">
                                        <span style="color:red"
                                              ng-show="formSubmitResult.error.height_feet[0]">[[ formSubmitResult.error.hight_feet[0] ]]</span>
                                    </div>
                                    <div class="col-sm-5">
                                        <input class="form-control  " id="hight_inch" name="hight_inch"
                                               ng-model="formData.height_inch"
                                               ng-change="formData.height_inch=formData.height_inch>=12?11:formData.height_inch"
                                               type="text" placeholder=" INCHES">
                                        <span style="color:red"
                                              ng-show="formSubmitResult.error.height_inch[0]">[[ formSubmitResult.error.height_inch[0] ]]</span>
                                    </div>
                                </div>
                                <div class="form-horizontal col-md-12" ng-if="isEditable('weight')>=0">
                                    <label class="control-label col-sm-2" for="email">Weight</label>

                                    <div class="col-sm-10">
                                        <input class="form-control  " id="weight" name="weight"
                                               ng-model="formData.weight" type="text" placeholder="weight">
                                    </div>
                                </div>
                                <div class="form-horizontal col-md-12"
                                     ng-if="isEditable('chest_normal')>=0||isEditable('chest_extended')>=0">
                                    <label for="" class="control-label col-sm-2">

                                    </label>
                                    <div class="col-sm-10">
                                        <div class="row">
                                            <div class="col-sm-6" ng-if="isEditable('chest_normal')>=0">
                                                <label for="" class="control-label">Chest normal</label>
                                                <input type="text" class="form-control"
                                                       placeholder="chest normal"
                                                       ng-model="formData.chest_normal">
                                            </div>
                                            <div class="col-sm-6" ng-if="isEditable('chest_extended')>=0">
                                                <label for="" class="control-label">Chest extended</label>
                                                <input type="text" class="form-control"
                                                       placeholder="chest extended"
                                                       ng-model="formData.chest_extended">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-horizontal col-md-12 " ng-if="isEditable('gender')>=0"
                                     ng-class="{'has-error':formSubmitResult.status==false&&formSubmitResult.error.gender[0]}">
                                    <label class="control-label col-sm-2" for="email"><sup
                                                style="color: #ff0709;font-size: 1em">*</sup>Gender</label>
                                    <div class="col-sm-10 ">
                                        <select name="sex" ng-model="formData.gender" class="form-control"
                                                id="sell">
                                            <option value="">--Select an option--</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                        <span style="color:red" ng-show="formSubmitResult.error.gender[0]">[[ formSubmitResult.error.sex[0] ]]</span>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </fieldset>

                    <fieldset ng-if="isEditable('education')>=0">
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
                                <div class="form-horizontal col-md-12" ng-if="isEditable('training_info')>=0">
                                    <label class="control-label col-sm-2" for="email">Training info</label>

                                    <div class="col-sm-10">
                                        <select class="form-control  "
                                               ng-model="formData.training_info" type="text">
                                            <option value="">--প্রশিক্ষন নির্বাচন করুন--</option>
                                            <option value="No training">No training</option>
                                            <option value="VDP training">VDP training</option>
                                            <option value="TDP training">TDP training</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-horizontal col-md-12" ng-if="isEditable('connection_name')>=0">
                                    <label class="control-label col-sm-2" for="email">Reference name</label>

                                    <div class="col-sm-10">
                                        <input class="form-control  "
                                               ng-model="formData.connection_name" type="text"
                                               placeholder="Reference name">
                                    </div>
                                </div>
                                <div class="form-horizontal col-md-12"
                                     ng-if="isEditable('connection_relation')>=0">
                                    <label class="control-label col-sm-2" for="email">Relation with
                                        reference</label>

                                    <div class="col-sm-10">
                                        <select class="form-control  " ng-model="formData.connection_relation">
                                            <option ng-repeat="(key,value) in relations" value="[[key]]">[[value]]
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-horizontal col-md-12"
                                     ng-if="isEditable('connection_address')>=0">
                                    <label class="control-label col-sm-2" for="email">Reference address</label>

                                    <div class="col-sm-10">
                                            <textarea class="form-control" rows="10" cols="30"
                                                      ng-model="formData.connection_address" type="text"
                                                      placeholder="Reference address">

                                            </textarea>
                                    </div>
                                </div>
                                <div class="form-horizontal col-md-12"
                                     ng-if="isEditable('connection_mobile_no')>=0">
                                    <label class="control-label col-sm-2" for="email">Reference mobile no</label>

                                    <div class="col-sm-10">
                                        <input class="form-control  "
                                               ng-model="formData.connection_mobile_no" type="text"
                                               placeholder="Reference mobile no">
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
                                    value="1">Update
                            </button>
                        </div>
                        {{--Form Submit Button--}}
                    </div>

                </form>


            </div>
        </section>
    </div>
</div>