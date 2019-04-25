@extends('template.master')
@section('title','Dashboard')
@section('breadcrumb')
    {!! Breadcrumbs::render('recruitment') !!}
@endsection
@section('content')
    <style>
        table.sm-table, table.sm-table tr, table.sm-table tr td {
            background-color: #ffffff !important;
        }
        .box-body .small-box-footer {
            position: relative;
            text-align: center;
            padding: 3px 0;
            color: #fff;
            color: rgba(255,255,255,0.8);
            display: block;
            z-index: 10;
            background: rgba(0,0,0,0.1);
            text-decoration: none;
        }
    </style>
    <script>
        GlobalApp.controller("RecruitmentSummary", function ($http, $scope) {
            $scope.loadSummary = function () {
                $http({
                    url: "{{URL::to('recruitment/getRecruitmentSummary')}}",
                    method: "get"
                }).then(function (response) {
                    jQuery("div#summaryResult").empty();
                    if (response.data && Array.isArray(response.data)) {
                        jQuery.each(response.data, function (key, category) {
                            var dataHTML = getCircularsAccordion(category.circular);
                            var data = '<div class="col-sm-12 col-md-6"><div class="box box-solid"><div class="box-body">' +
                                '<h4 style="background-color:#f7f7f7; font-size: 18px; text-align: center; padding: 7px 10px; margin-top: 0;">' +
                                '<a href="{{URL::to("recruitment/applicant?category=")}}' + category.id + '">' + category.category_name_bng + '</a>' +
                                '</h4>' +
                                '<div class="media"><div class="media-body"><div class="clearfix">' +
                                dataHTML +
                                '</div></div></div>' +
                                '<a href="{{URL::to("recruitment/applicant?category=")}}' + category.id + '" class="small-box-footer bg-aqua">More info <i class="fa fa-arrow-circle-right"></i></a>';

                            data += '</div></div></div>';
                            jQuery("div#summaryResult").append(data);
                        });
                    }
                });
            };
            $scope.loadSummary();
        });

        function getCircularsAccordion(circulars) {
            var dataHTML = '';
            if (circulars && Array.isArray(circulars) && circulars.length > 0) {
                var totalApplicant = 0;
                var totalMaleApplicant = 0;
                var totalFemaleApplicant = 0;
                var totalInitialApplicant = 0;
                var totalNotApply = 0;
                var totalPaid = 0;
                jQuery.each(circulars, function (key, circular) {
                    totalApplicant += +circular.appliciant_count;
                    totalMaleApplicant += +circular.appliciant_male_count;
                    totalFemaleApplicant += +circular.appliciant_female_count;
                    totalInitialApplicant += +circular.appliciant_initial_count;
                    totalNotApply += +circular.appliciant_paid_not_apply_count;
                    totalPaid += +circular.appliciant_paid_count;
                });

                dataHTML += '<table class="table sm-table"><tbody>' +
                    '<tr><td><span>Total Circular</span></td><td><span class="badge pull-right bg-aqua">' + circulars.length + '</span></td></tr>' +
                    '<tr><td><span>Total Applicant</span></td><td><span class="badge pull-right bg-aqua">' + totalApplicant + '</span></td></tr>' +
                    '<tr><td><span>Total Male Applicant</span></td><td><span class="badge pull-right bg-aqua">' + totalMaleApplicant + '</span></td></tr>' +
                    '<tr><td><span>Total Female Applicant</span></td><td><span class="badge pull-right bg-aqua">' + totalFemaleApplicant + '</span></td></tr>' +
                    '<tr><td><span>Total Not Paid Applicant</span></td><td><span class="badge pull-right bg-aqua">' + totalInitialApplicant + '</span></td></tr>' +
                    '<tr><td><span>Total Paid Applicant (Not Applied)</span></td><td><span class="badge pull-right bg-aqua">' + totalNotApply + '</span></td></tr>' +
                    '<tr><td><span>Total Applied Applicant</span></td><td><span class="badge pull-right bg-aqua">' + totalPaid + '</span></td></tr>' +
                    '</tbody></table>';

            } else {
                dataHTML += '<table class="table sm-table"><tbody>' +
                    '<tr><td><span>Total Circular</span></td><td><span class="badge pull-right bg-aqua">0</span></td></tr>' +
                    '<tr><td><span>Total Applicant</span></td><td><span class="badge pull-right bg-aqua">0</span></td></tr>' +
                    '<tr><td><span>Total Male Applicant</span></td><td><span class="badge pull-right bg-aqua">0</span></td></tr>' +
                    '<tr><td><span>Total Female Applicant</span></td><td><span class="badge pull-right bg-aqua">0</span></td></tr>' +
                    '<tr><td><span>Total Not Paid Applicant</span></td><td><span class="badge pull-right bg-aqua">0</span></td></tr>' +
                    '<tr><td><span>Total Paid Applicant (Not Applied)</span></td><td><span class="badge pull-right bg-aqua">0</span></td></tr>' +
                    '<tr><td><span>Total Applied Applicant</span></td><td><span class="badge pull-right bg-aqua">0</span></td></tr>' +
                    '</tbody></table>';
            }
            return dataHTML;
        }
    </script>
    <div class="container-fluid" ng-controller="RecruitmentSummary">
        <div class="row" id="summaryResult">
            <img class="image" style="text-align: center" src="http://ansarerpdev.com/dist/img/loading.gif" width="50">
        </div>
    </div>
@endsection