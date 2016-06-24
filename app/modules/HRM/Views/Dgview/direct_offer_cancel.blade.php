@extends('template.master')
@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="box box-solid">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a>Direct offer(DG)</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="ansar_id" class="control-label">Ansar Id to send offer</label>
                                        <input type="text" id="ansar_id" class="form-control" placeholder="Enter Ansar Id" ng-model="ansarId">
                                        <button class="btn btn-default" ng-click="sendOffer()">Send Offer</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@stop