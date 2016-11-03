<div class="row">
    <div ng-class="fieldWidth.range" ng-if="show('range')">
        <div class="form-group">
            <label class="control-label">@lang('title.range')&nbsp;
                <img ng-show="loading.range" src="{{asset('dist/img/facebook.gif')}}" width="16">
            </label>
            <select id="range" class="form-control" ng-model="selected.range" ng-disabled="loading.range||loading.unit||loading.thana||loading.kpi" ng-change="loadUnit(selected.range)">
                <option value="all" ng-if="type=='all'">All</option>
                <option value="" ng-if="type=='single'||type==undefined">--@lang('title.range')--</option>
                <option ng-repeat="d in ranges" value="[[d.id]]">[[d.division_name_bng]]</option>
            </select>
        </div>
    </div>
    <div ng-class="fieldWidth.unit" ng-if="show('unit')">
        <div class="form-group">
            <label class="control-label">@lang('title.unit')&nbsp;
                <img ng-show="loading.unit" src="{{asset('dist/img/facebook.gif')}}" width="16">
            </label>
            <select id="unit" class="form-control" ng-model="selected.unit" ng-disabled="loading.range||loading.unit||loading.thana||loading.kpi" ng-change="loadThana(selected.unit)">
                <option value="all" ng-if="type=='all'">All</option>
                <option value="" ng-if="type=='single'||type==undefined">--@lang('title.unit')--</option>
                <option ng-repeat="d in units" value="[[d.id]]">[[d.unit_name_bng]]</option>
            </select>
        </div>
    </div>
    <div ng-class="fieldWidth.thana" ng-if="show('thana')">
        <div class="form-group">
            <label class="control-label">@lang('title.thana')&nbsp;
                <img ng-show="loading.thana" src="{{asset('dist/img/facebook.gif')}}" width="16">
            </label>
            <select id="thana" class="form-control" ng-model="selected.thana" ng-disabled="loading.range||loading.unit||loading.thana||loading.kpi" ng-change="loadKPI(selected.thana)">
                <option value="all" ng-if="type=='all'">All</option>
                <option value="" ng-if="type=='single'||type==undefined">--@lang('title.thana')--</option>
                <option ng-repeat="t in thanas" value="[[t.id]]">[[t.thana_name_bng]]</option>
            </select>
        </div>
    </div>
    <div ng-class="fieldWidth.kpi" ng-if="show('kpi')">
        <div class="form-group">
            <label class="control-label">@lang('title.kpi')&nbsp;
                <img ng-show="loading.kpi" src="{{asset('dist/img/facebook.gif')}}" width="16">
            </label>
            <select id="kpi" class="form-control" ng-model="selected.kpi" ng-disabled="loading.range||loading.unit||loading.thana||loading.kpi">
                <option value="all" ng-if="type=='all'">All</option>
                <option value="" ng-if="type=='single'||type==undefined">--@lang('title.kpi')--</option>
                <option ng-repeat="t in kpis" value="[[t.id]]">[[t.kpi_name]]</option>
            </select>
        </div>
    </div>
</div>