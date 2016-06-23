<?php
$user = Auth::user();
$usertype = $user->type;
?>
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
    @foreach ($personalinfos as $personalinfo)
    <tr>
        <!--<td><a href="{{ URL::to('/ansardetails/'.$personalinfo->ansar_id) }}">{{ $personalinfo->ansar_id }}</a></td>-->
        <td>
            <a href="{{ URL::to('/entryreport/'.$personalinfo->ansar_id) }}">{{ $personalinfo->ansar_id }}</a>
        </td>
        <td>{{ $personalinfo->ansar_name_bng }}</td>
        <td>{{ $personalinfo->division->division_name_eng }}</td>
        <td>{{ $personalinfo->mobile_no_self }}</td>
        <td>
            <div class="row" style="margin-right: -100px;">

                @if($usertype == 55)
                @if($personalinfo->verified == 0)
                <div class="col-xs-1">
                    <a class="btn btn-primary btn-xs " title="edit" href="{{ url('editEntry/'.$personalinfo->ansar_id)}}"><span
                            class="glyphicon glyphicon-edit"></span></a>
                </div>
                @endif

                @if($personalinfo->verified == 1)
                <div class="col-xs-1">
                    <a class="btn btn-primary btn-xs " title="edit" ><span
                            class="glyphicon glyphicon-edit"></span></a>
                </div>
                @endif



                @if($personalinfo->verified == 0)

                <div class="col-xs-1">
                    <a class="btn btn-success btn-xs verification" title="verify"
                       data-verfication="{{$personalinfo->ansar_id}}" ><span
                            class="fa fa-check"></span>
                        <i class="fa fa-spinner fa-pulse" style="display: none"></i>
                    </a>
                </div>

                @endif

                @if($personalinfo->verified == 1)
                <div class="col-xs-1">
                    <a class="btn btn-success btn-xs verification disabled" title="verify"><span
                            class="fa fa-check"></span>
                        <i class="fa fa-spinner fa-pulse" style="display: none"></i>
                    </a>
                </div>
                @endif

                <div class="col-xs-1">
                    <a class="btn btn-danger btn-xs" title="block"><span
                            class="glyphicon glyphicon-remove-circle"></span></a>
                </div>
                @endif

                @if($usertype == 44)
                @if($personalinfo->verified == 1)
                <div class="col-xs-1">
                    <a class="btn btn-primary btn-xs " title="edit" href="{{ url('editEntry/'.$personalinfo->ansar_id)}}"><span
                            class="glyphicon glyphicon-edit"></span></a>
                </div>
                @endif

                @if($personalinfo->verified == 2)
                <div class="col-xs-1">
                    <a class="btn btn-primary btn-xs " title="edit" ><span
                            class="glyphicon glyphicon-edit"></span></a>
                </div>
                @endif


                @if($personalinfo->verified == 1)
                <div class="col-xs-1">
                    <a class="btn btn-success btn-xs verification" title="verify"
                       data-verfication="{{$personalinfo->ansar_id}}" ><span
                            class="fa fa-check"></span>
                        <i class="fa fa-spinner fa-pulse" style="display: none"></i>
                    </a>
                </div>
                @endif

                @if($personalinfo->verified == 2)
                <div class="col-xs-1">
                    <a class="btn btn-success btn-xs verification disabled" title="verify"><span
                            class="fa fa-check"></span>
                        <i class="fa fa-spinner fa-pulse" style="display: none"></i>
                    </a>
                </div>
                @endif
                @if($personalinfo->verified == 1)
                <div class="col-xs-1">
                    <a class="btn btn-success btn-xs verification" title="Reject"
                       data-verfication="{{$personalinfo->ansar_id}}" ><span
                            class="fa fa-retweet"></span>
                        <i class="fa fa-spinner fa-pulse" style="display: none"></i>
                    </a>
                </div>
                @endif

                <div class="col-xs-1">
                    <a class="btn btn-danger btn-xs verification" title="block"><span
                            class="glyphicon glyphicon-remove-circle"></span></a>
                </div>
                @endif

                @if($usertype == 11 || $usertype == 22 || $usertype == 33 || $usertype == 66)
                @if($personalinfo->verified == 0 || $personalinfo->verified == 1)
                <div class="col-xs-1">
                    <a class="btn btn-primary btn-xs " title="edit" href="{{ url('editEntry/'.$personalinfo->ansar_id)}}"><span
                            class="glyphicon glyphicon-edit"></span></a>
                </div>
                @endif

                @if($personalinfo->verified == 2)
                <div class="col-xs-1">
                    <a class="btn btn-primary btn-xs " title="edit" ><span
                            class="glyphicon glyphicon-edit"></span></a>
                </div>
                @endif


                @if($personalinfo->verified == 0 || $personalinfo->verified == 1)

                <div class="col-xs-1">
                    <a class="btn btn-success btn-xs verification" title="verify"
                       data-verfication="{{$personalinfo->ansar_id}}" ><span
                            class="fa fa-check"></span>
                        <i class="fa fa-spinner fa-pulse" style="display: none"></i>
                    </a>
                </div>

                @endif


                @if($personalinfo->verified == 2)
                <div class="col-xs-1">
                    <a class="btn btn-success btn-xs verification disabled" title="verify"><span
                            class="fa fa-check"></span>
                        <i class="fa fa-spinner fa-pulse" style="display: none"></i>
                    </a>
                </div>
                @endif
                <div class="col-xs-1">
                    <a class="btn btn-danger btn-xs verification" title="block"><span
                            class="glyphicon glyphicon-remove-circle"></span></a>
                </div>
                @endif

            </div>
        </td>
    </tr>
    @endforeach
</table>