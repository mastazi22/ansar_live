@extends('template.master')
@section('title','Dashboard')
@section('breadcrumb')
    {!! Breadcrumbs::render('AVURP') !!}
@endsection
@section('content')
    <div class="box box-solid" ng-controller="OfferController">
        <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
        </div>
        <div class="box-header">
            {{--<filter-template
                    show-item="['range','unit','thana']"
                    type="all"
                    range-change="loadPage()"
                    unit-change="loadPage()"
                    thana-change="loadPage()"
                    data="param"
                    start-load="range"
                    on-load="loadPage()"
                    field-width="{range:'col-sm-4',unit:'col-sm-4',thana:'col-sm-4'}"
            >

            </filter-template>--}}
        </div>
        <div class="box-body">


            <div class="container-fluid">
                <div ng-bind-html="searchedVDP" compile-html>

                </div>
            </div>
        </div>
        <div id="embodimentModel" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Modal Header</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="message_box" class="control-label">Message(optional):</label>
                            <textarea ng-model="submitData.message" class="form-control" name="" id="" cols="30" rows="10"></textarea>
                        </div>
                        <div class="form-group">
                            <filter-template
                                    show-item="['range','unit']"
                                    type="single"
                                    data="submitData"
                                    layout-vertical="1"
                                    start-load="range"
                            >

                            </filter-template>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" ng-click="confirmOffer()" class="btn btn-primary" data-dismiss="modal">Send Offer</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection