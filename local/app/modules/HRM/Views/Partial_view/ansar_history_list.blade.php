<style>
    .font-bolddd *{
        font-weight: bold !important;
        font-size: 17px;
    }
</style>

<div ng-if="!data.apid">
    <h4 style="text-align: center">No Ansar is available to show</h4>
</div>
<div ng-if="data.apid">
    <div class="form-group font-bolddd">
        <div class="col-sm-12 col-xs-12 col-centered">
            <div class="table-responsive">
                <table class="table table-bordered" style="margin: 0 auto;width: auto !important;">
                    <tr>
                        <td rowspan="10"  style="vertical-align: middle;width: 130px;height: 150px;background: #ffffff">
                            <img  style="width: 120px;height: 150px" src="{{URL::to('image').'?file='}}[[data.apid.profile_pic]]" alt="">
                        </td>
                        <th style="background: #ffffff">ID</th>
                        <td style="background: #ffffff">[[data.apid.ansar_id]]</td>
                    </tr>
                    <tr>

                        <th style="background: #ffffff">Name</th>
                        <td style="background: #ffffff">[[data.apid.ansar_name_bng]]</td>
                    </tr>
                    <tr>
                        <th style="background: #ffffff">Rank</th>
                        <td style="background: #ffffff">[[data.apid.name_bng]]</td>
                    </tr>
                    <tr>
                        <th style="background: #ffffff">Mobile No.</th>

                        <td style="background: #ffffff">[[data.apid.mobile_no_self|checkpermission:"view_mobile_no":"embodied":data.apid.ansar_id ]]</td>
                    </tr>
                    <tr>
                        <th style="background: #ffffff">Home District</th>
                        <td style="background: #ffffff">[[data.apid.unit_name_bng]]</td>
                    </tr>
                    <tr>
                        <th style="background: #ffffff">Date of birth</th>
                        <td style="background: #ffffff">[[data.apid.dob]]</td>
                    </tr>
                    <tr>
                        <th style="background: #ffffff">Age</th>
                        <td style="background: #ffffff">[[data.apid.age]]</td>
                    </tr>
                    <tr>
                        <th style="background: #ffffff">Account No</th>
                        <td style="background: #ffffff">[[data.apid.prefer_choice=='general'?data.account_no:data.mobile_bank_account_no]]</td>
                    </tr>
                    <tr>
                        <th style="background: #ffffff">Bank name/Mobile account type</th>
                        <td style="background: #ffffff">[[data.apid.prefer_choice=='general'?data.bank_name:data.mobile_bank_type]]</td>
                    </tr>
                    <tr>
                        <th style="background: #ffffff">AVUB Share ID</th>
                        <td style="background: #ffffff">[[data.apid.avub_share_id]]</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="form-group font-bolddd">
        <div class="col-sm-12">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <caption>পানেল্ভুক্তির ও অফারের বিবরণ</caption>
                    <tr>
                        <td>পানেল্ভুক্তির তারিখ</td>
                        <td>প্যানেল আইডি নং</td>
                        <td>বর্তমান অবস্থা</td>
                        <td>অফারের তারিখ</td>
                        <td>অফারের জেলা</td>
                        <td>অফার বাতিলের তারিখ</td>
                    </tr>
                    <tr>
                        <td>[[data.api.panel_date?(data.api.panel_date|dateformat:'DD-MMMM-YYYY':'bn'):"--"]]</td>
                        <td>[[data.api.memorandum_id?data.api.memorandum_id:"--"]]</td>
                        <td>[[data.status]]</td>
                        <td>[[data.aod.offerDate?(data.aod.offerDate|dateformat:'DD-MMMM-YYYY':'bn'):'--']]</td>
                        <td>[[data.aod.offerUnit?data.aod.offerUnit:'--']]</td>
                        <td>[[data.aoci.offerCancel?(data.aoci.offerCancel|dateformat:'DD-MMMM-YYYY':'bn'):'--']]</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="form-group font-bolddd">
        <div class="col-sm-12">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <td style="background: #ffffff">
                            <table class="table table-bordered">
                                <caption>সর্বশেষ অঙ্গিভুতির বিবরণ</caption>
                                <tr>
                                    <td>অঙ্গিভুতির  তারিখ</td>
                                    <td>অঙ্গিভুতির আইডি নং</td>
                                    <td>জেলার নাম</td>
                                    <td>অঙ্গিভুতির সংস্থা</td>
                                </tr>
                                <tr>
                                    <td>[[data.aei.joining_date?(data.aei.joining_date|dateformat:'DD-MMMM-YYYY':'bn'):"--"]]</td>
                                    <td>[[data.aei.memorandum_id?data.aei.memorandum_id:"--"]]</td>
                                    <td>[[data.aei.unit_name_bng?data.aei.unit_name_bng:"--"]]</td>
                                    <td>[[data.aei.kpi_name?data.aei.kpi_name:"--"]]</td>
                                </tr>
                            </table>
                        </td>
                        <td style="background: #ffffff">
                            <table class="table table-bordered">
                                <caption>সর্বশেষ অ-অঙ্গিভুতির বিবরণ</caption>
                                <tr>
                                    <td>অ-অঙ্গিভুতির  তারিখ</td>
                                    <td>অ-অঙ্গিভুতির কারন</td>
                                </tr>
                                <tr>
                                    <td>[[data.adei.disembodiedDate?(data.adei.disembodiedDate|dateformat:'DD-MMMM-YYYY':'bn'):"--"]]</td>
                                    <td>[[data.adei.disembodiedReason?data.adei.disembodiedReason:"--"]]</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>