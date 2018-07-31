<div ng-class="{row:!layoutVertical}" ng-init="parseItem()">
    <div ng-class="fieldWidth.range" ng-if="show('range')">
        <div class="form-group">
            <label class="control-label">{{trim(Lang::get('title.range'))}}
                <img ng-show="loading.range" src="{{asset('dist/img/facebook.gif')}}" width="16">
            </label>
            <select id="range" ng-disabled="rangeFieldDisabled||loading.range||loading.unit||loading.thana||loading.kpi" name="[[fieldName.range]]" class="form-control" ng-model="selected.range" ng-change="loadUnit(selected.range)">
                <option value="all" ng-if="type=='all'">All</option>
                <option value="" ng-if="type=='single'||type==undefined">--@lang('title.range')--</option>
                <option ng-repeat="d in ranges" value="[[d.id]]" ng-disabled="rangeDisabled==d.id">[[d.division_name_bng]]</option>
            </select>
            <p class="text-danger" ng-if="errorKey.range!=undefined||errorMessage[errorKey.range]">[[errorMessage[errorKey.range] ]]</p>
        </div>
    </div>
    <div ng-class="fieldWidth.unit" ng-if="show('unit')">
        <div class="form-group">
            <label class="control-label">{{trim(Lang::get('title.unit'))}}
                <img ng-show="loading.unit" src="{{asset('dist/img/facebook.gif')}}" width="16">
            </label>
            <select id="unit" ng-disabled="unitFieldDisabled||loading.range||loading.unit||loading.thana||loading.kpi" name="[[fieldName.unit]]" class="form-control" ng-model="selected.unit" ng-change="loadThana(selected.range||undefined,selected.unit)">
                <option value="all" ng-if="type=='all'">All</option>
                <option value="" ng-if="type=='single'||type==undefined">--@lang('title.unit')--</option>
                <option ng-repeat="d in units" value="[[d.id]]" ng-disabled="unitDisabled==d.id">[[d.unit_name_bng]]</option>
            </select>
            <p class="text-danger" ng-if="errorKey.unit!=undefined||errorMessage[errorKey.unit]">[[errorMessage[errorKey.unit] ]]</p>
        </div>
    </div>
    <div ng-class="fieldWidth.thana" ng-if="show('thana')">
        <div class="form-group">
            <label class="control-label">@lang('title.thana')
                <img ng-show="loading.thana" src="{{asset('dist/img/facebook.gif')}}" width="16">
            </label>
            <select id="thana" ng-disabled="thanaFieldDisabled||loading.range||loading.unit||loading.thana||loading.kpi" name="[[fieldName.thana]]" class="form-control" ng-model="selected.thana" ng-change="loadKPI(selected.range||undefined,selected.unit||undefined,selected.thana);loadUnion(selected.range||undefined,selected.unit||undefined,selected.thana);loadShortKPI(selected.range||undefined,selected.unit||undefined,selected.thana)">
                <option value="all" ng-if="type=='all'">All</option>
                <option value="" ng-if="type=='single'||type==undefined">--@lang('title.thana')--</option>
                <option ng-repeat="t in thanas" value="[[t.id]]" ng-disabled="thanaDisabled==t.id">[[t.thana_name_bng]]</option>
            </select>
            <p class="text-danger" ng-if="errorKey.thana!=undefined||errorMessage[errorKey.thana]">[[errorMessage[errorKey.thana] ]]</p>
        </div>
    </div>
    <div ng-class="fieldWidth.union" ng-if="show('union')">
        <div class="form-group">
            <label class="control-label">@lang('title.union')
                <img ng-show="loading.union" src="{{asset('dist/img/facebook.gif')}}" width="16">
            </label>
            <select id="union" ng-disabled="unionFieldDisabled||loading.range||loading.unit||loading.union||loading.kpi" name="[[fieldName.union]]" class="form-control" ng-model="selected.union">
                <option value="all" ng-if="type=='all'">All</option>
                <option value="" ng-if="type=='single'||type==undefined">--@lang('title.union')--</option>
                <option ng-repeat="t in unions" value="[[t.id]]" ng-disabled="unionDisabled==t.id">[[t.union_name_bng]]</option>
            </select>
            <p class="text-danger" ng-if="errorKey.union!=undefined||errorMessage[errorKey.union]">[[errorMessage[errorKey.union] ]]</p>
        </div>
    </div>
    <div ng-class="fieldWidth.kpi" ng-if="show('kpi')">
        <div class="form-group">
            <label class="control-label">@lang('title.kpi')
                <img ng-show="loading.kpi" src="{{asset('dist/img/facebook.gif')}}" width="16">
            </label>
            <select id="kpi" ng-disabled="kpiFieldDisabled||loading.range||loading.unit||loading.thana||loading.kpi" name="[[fieldName.kpi]]" class="form-control" ng-model="selected.kpi">
                <option value="all" ng-if="type=='all'">All</option>
                <option value="" ng-if="type=='single'||type==undefined">--@lang('title.kpi')--</option>
                <option ng-repeat="t in kpis" value="[[t.id]]" ng-disabled="kpiDisabled==t.id">[[t.kpi_name]]</option>
            </select>
            <p class="text-danger" ng-if="errorKey.kpi!=undefined||errorMessage[errorKey.kpi]">[[errorMessage[errorKey.kpi] ]]</p>
        </div>
    </div>
    <div ng-class="fieldWidth.shortKpi" ng-if="show('short_kpi')">
        <div class="form-group">
            <label class="control-label">@lang('title.shortKpi')
                <img ng-show="loading.shortKpi" src="{{asset('dist/img/facebook.gif')}}" width="16">
            </label>
            <select id="shortKpi" ng-disabled="shortKpiFieldDisabled||loading.range||loading.unit||loading.thana||loading.shortKpi" name="[[fieldName.shortKpi]]" class="form-control" ng-model="selected.shortKpi">
                <option value="all" ng-if="type=='all'">All</option>
                <option value="" ng-if="type=='single'||type==undefined">--@lang('title.shortKpi')--</option>
                <option ng-repeat="t in shortKpis" value="[[t.id]]" ng-disabled="shortKpiDisabled==t.id">[[t.kpi_name]]</option>
            </select>
            <p class="text-danger" ng-if="errorKey.shortKpi!=undefined||errorMessage[errorKey.shortKpi]">[[errorMessage[errorKey.shortKpi] ]]</p>
        </div>
    </div>
    <div ng-class="fieldWidth.rank" ng-if="show('rank')">
        <div class="form-group">
            <label class="control-label">@lang('title.rank')
            </label>
            <select id="rank" class="form-control" ng-model="selected.rank" ng-disabled="loading.range||loading.unit||loading.thana||loading.kpi">
                <option value="all" ng-if="type=='all'">All</option>
                <option value="" ng-if="type=='single'||type==undefined">--Select Gender--</option>
                <option ng-repeat="t in ranks" value="[[t.id]]">[[t.code=='ANSAR'?'Ansar':t.code]]</option>
            </select>
            <p class="text-danger" ng-if="errorKey.rank!=undefined||errorMessage[errorKey.rank]">[[errorMessage[errorKey.rank] ]]</p>
        </div>
    </div>
    <div ng-class="fieldWidth.gender" ng-if="show('gender')">
        <div class="form-group">
            <label class="control-label">@lang('title.sex')
            </label>
            <select id="gender" class="form-control" ng-model="selected.gender" ng-disabled="loading.range||loading.unit||loading.thana||loading.kpi">
                <option value="all" ng-if="type=='all'">All</option>
                <option value="" ng-if="type=='single'||type==undefined">--Select Gender--</option>
                <option ng-repeat="t in genders" value="[[t.value]]">[[t.text]]</option>
            </select>
            <p class="text-danger" ng-if="errorKey.gender!=undefined||errorMessage[errorKey.gender]">[[errorMessage[errorKey.gender] ]]</p>
        </div>
    </div>
    <div ng-class="fieldWidth.custom" ng-if="customField==true">
        <div class="form-group">
            <label class="control-label">[[customLabel]]
            </label>
            <select id="custom" class="form-control" ng-model="selected.custom" ng-disabled="loading.range||loading.unit||loading.thana||loading.kpi">
                <option ng-repeat="(key,value) in customData" value="[[value]]">[[key]]</option>
            </select>
            <p class="text-danger" ng-if="errorKey.custom!=undefined||errorMessage[errorKey.custom]">[[errorMessage[errorKey.custom] ]]</p>
        </div>
    </div>
</div>