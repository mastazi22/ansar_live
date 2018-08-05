<?php $i = 1; ?>

<table class="table table-bordered table-condensed">
    <tr>
        <th>#</th>
        <th>VDP ID</th>
        <th>Name(English)</th>
        <th>Name(Bangla)</th>
        <th>Date of Birth</th>
        <th>Division</th>
        <th>District</th>
        <th>Thana</th>
        <th>Union</th>
        <th>Ward</th>
        <th>Bank Account no</th>
        <th>Bank Name</th>
        <th>Status</th>

    </tr>

    @if(count($vdp_infos))
        @foreach($vdp_infos as $info)
            <tr>
                <td>{{$i++}}</td>
                <td>{{"id-".$info->geo_id}}</td>
                <td>{{$info->ansar_name_eng}}</td>
                <td>{{$info->ansar_name_bng}}</td>
                <td>{{$info->date_of_birth}}</td>
                <td>{{$info->division->division_name_bng}}</td>
                <td>{{$info->unit->unit_name_bng}}</td>
                <td>{{$info->thana->thana_name_bng}}</td>
                <td>{{$info->union->union_name_bng}}</td>
                <td>{{$info->union_word_id}}</td>
                <td>{{$info->account?($info->account->prefer_choice=="general"?$info->account->account_no:$info->account->mobile_bank_account_no):"n\a"}}</td>
                <td>{{$info->account?($info->account->prefer_choice=="general"?$info->account->bank_name:$info->account->mobile_bank_type):'n\a'}}</td>
                @if($info->status=='new')
                    <td>
                        <span class="label label-danger">Unverified</span>
                    </td>
                @elseif($info->status=='verified')
                    <td>
                        <span class="label label-warning">Verified</span>
                    </td>
                @else
                    <td>
                        <span class="label label-success">Approved</span>
                    </td>
                @endif
        @endforeach
    @else
        <tr>
            <td colspan="12" class="bg-warning">
                No VPD member found
            </td>
        </tr>
    @endif

</table>
