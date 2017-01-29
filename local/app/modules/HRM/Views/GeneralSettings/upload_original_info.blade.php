@extends('template.master')
@section('title','Upload Original Info')
@section('breadcrumb')
    {!! Breadcrumbs::render('orginal_info') !!}
@endsection
@section('content')
    <script>
    </script>

    <div>
        <section class="content">
            <div class="box box-solid">
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-8 col-md-8 col-xs-12 col-lg-6 col-centered">
                            <form method="post" action="{{URL::route('upload_original_info')}}" form-submit errors="errors" loading="loading">
                                {!! csrf_field() !!}
                                <div class="form-group">
                                    <label class="control-label">Ansar ID:</label>
                                    <input type="text" class="form-control" name="ansar_id" placeholder="Enter Ansar ID to see Entry Information">
                                    <p class="text text-danger" ng-if="errors.ansar_id!=undefined&&errors.ansar_id.length>0">
                                        [[errors.ansar_id[0] ]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Front Side:</label>
                                    <input type="file" name="front_side">
                                    <p class="text text-danger" ng-if="errors.front_side!=undefined&&errors.front_side.length>0">
                                        [[errors.front_side[0] ]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Back Side:</label>
                                    <input type="file" name="back_side">
                                    <p class="text text-danger" ng-if="errors.back_side!=undefined&&errors.back_side.length>0">
                                        [[errors.back_side[0] ]]
                                    </p>
                                </div>
                                <button type="submit" ng-disabled="loadfing" class="btn btn-primary">
                                    <i class="fa fa-upload" ng-if="!loading"></i>
                                    <i class="fa fa-spinner fa-pulse" ng-if="loading"></i>
                                    &nbsp;Upload Image
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@stop