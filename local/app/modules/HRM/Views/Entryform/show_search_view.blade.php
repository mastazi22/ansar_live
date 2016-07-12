<div class="loading-data">
    <i class="fa fa-4x fa-refresh fa-spin loading-icon"></i>
</div>
<table class="table table-responsive table-bordered table-striped" id="ansar-table">

    <tr>
        <th>ID No</th>
        <th>Name</th>
        <th>Division</th>
        <th>Mobile</th>
        <th style="width:140px">Action</th>
    </tr>
        <tr>
            <td><a href="{{ URL::to('/ansardetails/'.$personalinfo->ansar_id) }}">{{ $personalinfo->ansar_id }}</a></td>
            <td>{{ $personalinfo->ansar_name_bng }}</td>
            <td>{{ $personalinfo->division->division_name_eng }}</td>
            <td>{{ $personalinfo->mobile_no_self }}</td>
            <td>
                <div class="row" style="margin-right: -100px;">
                    <div class="col-xs-1">
                        <a class="btn btn-primary btn-xs" title="edit"><span
                                    class="glyphicon glyphicon-edit"></span></a>
                    </div>

                    <div class="col-xs-1">
                        <a class="btn btn-danger btn-xs" title="block"><span
                                    class="glyphicon glyphicon-remove-circle"></span></a>
                    </div>
                    @if($personalinfo->verified == 0)

                        <div class="col-xs-1">
                            <a class="btn btn-success btn-xs verification" title="Verify"
                               data-verfication="{{$personalinfo->ansar_id}}"><i class="fa fa-spinner fa-pulse"
                                                                                 style="display: none"></i> Verify </a>
                        </div>

                    @endif

                    @if($personalinfo->verified == 1)
                        <div class="col-xs-1">
                            <a class="btn btn-default btn-xs disabled" title="Verify">Verify </a>
                        </div>
                    @endif

                </div>
            </td>
        </tr>
</table>
