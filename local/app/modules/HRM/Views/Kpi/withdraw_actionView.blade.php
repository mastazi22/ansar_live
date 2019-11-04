<div class="row">
    <div class="col-md-12">
        <div class="alert alert-danger" ng-if="error!=undefined&&error.id!=undefined">
            <i class="fa fa-warning"></i>&nbsp; Invalid request
        </div>
        <form ng-submit="submitForm()">
            <input type="hidden" ng-model="formData.id" ng-init="formData.id=info.id">
            <div class="form-group">
                <label for="">Memorandum No.</label>
                <input type="text" ng-model="formData.mem_id" placeholder="Memorandum No." class="form-control"
                       required>
                <p class="text text-danger" ng-if="error!=undefined&&error.mem_id!=undefined">[[error.mem_id[0] ]]</p>
            </div>
            <datepicker-separate-fields label="Withdraw Date:" notify="kpiWithdrawInvalidDate"
                                        rdata="formData.date"></datepicker-separate-fields>
            <button class="btn btn-primary pull-right" type="submit"
                    ng-disabled="isSubmitting || kpiWithdrawInvalidDate">
                <i class="fa fa-spinner fa-pulse" ng-if="isSubmitting"></i>&nbsp;Withdraw Kpi
            </button>
        </form>
    </div>
    <div class="col-md-12" style="margin-top: 1%;">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <tr>
                    <th>Kpi Name</th>
                    <td>[[info.kpi_name]]</td>
                </tr>
                <tr>
                    <th class="text text-bold">Kpi Address</th>
                    <td>[[info.kpi_address]]</td>
                </tr>
                <tr>
                    <th class="text text-bold">Kpi Contact No.</th>
                    <td>[[info.kpi_contact_no]]</td>
                </tr>
                <tr>
                    <th class="text text-bold">Kpi Division</th>
                    <td>[[info.division.division_name_bng]]</td>
                </tr>
                <tr>
                    <th class="text text-bold">Kpi Unit</th>
                    <td>[[info.unit.unit_name_bng]]</td>
                </tr>
            </table>
        </div>
    </div>
</div>
<script>
    $("#date-picker").datepicker({dateFormat: 'dd-M-yy'});
</script>