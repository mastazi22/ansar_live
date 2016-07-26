@extends('template.master')
@section('title','Offer Quota')
@section('breadcrumb')
    {!! Breadcrumbs::render('offer_quota') !!}
    @endsection
@section('content')
    <div ng-controller="QuotaController">
        {{--<div class="breadcrumbplace">--}}
            {{--{!! Breadcrumbs::render('offer_quota') !!}--}}
        {{--</div>--}}
        <section class="content">
            @if(Session::has('success'))
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <i class="fa fa-check"></i>&nbsp;{{Session::get('success')}}
                </div>
                @endif
            <div class="box box-solid">
                <div class="box-body" style="width: 70%;margin: 0 auto">
                    <form id="offer-quota-form" action="{{URL::route('update_offer_quota')}}" method="post">
                        {{csrf_field()}}
                        @foreach($quota as $q)
                        <div class="row margin-bottom-input form-group">
                            <div class="col-sm-4">
                                <label class="control-label">{{$q->unit_name_eng}}</label>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <input type="hidden" name="quota_id[]" value="{{$q->unit}}">
                                    <input type="text"  class="form-control" name="quota_value[]"
                                           placeholder="Enter quota" value="{{$q->quota}}">
                                </div>
                            </div>
                        </div>
                        @endforeach
                        <button id="update-quota"  type="submit" class="btn btn-primary">
                            <i id="ni" class="fa fa-save"></i></i>&nbsp; Save</button>
                    </form>
                </div>
            </div>
        </section>
    </div>
@stop