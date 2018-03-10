@extends('template.master')
@section('title','Applicant Mark Rules')
@section('breadcrumb')
    {!! Breadcrumbs::render('recruitment.point.index') !!}
@endsection
@section('content')
    <section class="content" ng-controller="applicantQuota">
        <div class="box box-solid">
            <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-condensed">
                    <caption style="font-size: 20px">Mark Rules<a href="{{URL::route('recruitment.marks_rules.create')}}" class="btn btn-primary btn-xs pull-right">Add new field</a></caption>
                    <tr>
                        <th>SL. No</th>
                        <th>Circular name</th>
                        <th>Rule name</th>
                        <th>Rule for</th>
                        <th>Rules</th>
                        <th>Action</th>
                    </tr>
                    <?php $i=1;?>
                    @forelse($points as $point)
                        <td>{{$i++}}</td>
                        <td>{{$point->circular->circular_name}}</td>
                        <td>{{$point->rule_name}}</td>
                        <td>{{$point->point_for}}</td>
                        <td>{{$point->rules}}</td>
                        <td></td>
                        @empty
                        <tr>
                            <td colspan="6" class="bg-warning">
                                No Point Rule available.
                            </td>
                        </tr>
                    @endforelse
                </table>
            </div>
        </div>
    </section>
@endsection