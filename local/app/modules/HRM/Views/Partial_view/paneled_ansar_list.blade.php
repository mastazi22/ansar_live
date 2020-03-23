<table class="table table-bordered">
    <tr>
        <th>Ansar<br>ID</th>
        <th>Rank</th>
        <th>Name</th>
        <th>Birth<br>Date</th>
        <th>Home<br>District</th>
        <th>Thana</th>
        <th ng-click="sortList('panel_date')">Global Panel<br>Date & Time</th>
        <th ng-click="sortList('re_panel_date')">Regional Panel<br>Date & Time</th>
        @if(Auth::user()->type==11)
            <th>Global<br>Position</th>
            <th>Regional<br>Position</th>
            <th>Offer<br>Count</th>
        @endif
    </tr>
    <tbody>
    <tr ng-repeat="ansar in data.ansars">
        <td><a href="{{URL::to('HRM/entryreport')}}/[[ansar.id]]">[[ansar.id]]</a></td>
        <td>[[ansar.rank]]</td>
        <td>[[ansar.name]]</td>
        <td>[[ansar.birth_date|dateformat:"DD-MMM-YYYY"]]</td>
        <td>[[ansar.unit]]</td>
        <td>[[ansar.thana]]</td>
        <td>[[ansar.panel_date|dateformat:"DD-MMM-YYYY"]]</td>
        <td>[[ansar.re_panel_date|dateformat:"DD-MMM-YYYY"]]</td>
        @if(Auth::user()->type==11)
            <td ng-style="(ansar.locked==1 && ansar.last_offer_region=='GB')? {'background': 'red','color':'white'} : (ansar.offer_type.split('DG').join('GB').split('CG').join('GB').split('GB').length-1>=3) ? {'background': 'orange','color':'white'} : {'background': 'transparent'}">
                [[ansar.offer_type.split('DG').join('GB').split('CG').join('GB').split('GB').length-1>=3?'Offer Blocked':ansar.go_panel_position]]
            </td>
            <td ng-style="(ansar.locked==1 && ansar.last_offer_region=='RE')? {'background': 'red','color':'white'} : (ansar.offer_type.split('RE').length-1>=3) ? {'background': 'orange','color':'white'} : {'background': 'transparent'}">
                [[ansar.offer_type.split('RE').length-1>=3?'Offer Blocked':ansar.re_panel_position]]
            </td>
            <td>[[ansar.offer_type.split('DG').join('GB').split('CG').join('GB')]]</td>
        @endif
    </tr>
    <tr ng-if="data.ansars.length<=0">
        <td class="warning" colspan="9">No Ansar Found</td>
    </tr>
    </tbody>
</table>