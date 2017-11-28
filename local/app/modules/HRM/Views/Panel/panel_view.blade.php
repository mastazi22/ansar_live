{{--User: Shreya--}}
{{--Date: 10/15/2015--}}
{{--Time: 10:49 AM--}}

@extends('template.master')
@section('content')
    <script>
        GlobalApp.controller("PanelController", function ($scope, $window, $http, $timeout) {

            $scope.memorandumId = "";
            $scope.joinDate = "";
            $scope.isVerified = false;
            $scope.formData = {};
            $scope.isVerifying = false;

            $scope.verifyMemorandumId = function () {
                var data = {
                    memorandum_id: $scope.memorandumId
                }
                $scope.isVerified = false;
                $scope.isVerifying = true;
                $http.post('{{action('UserController@verifyMemorandumId')}}', data).then(function (response) {
//                    alert(response.data.status)
                    $scope.isVerified = response.data.status;
                    $scope.isVerifying = false;
                }, function (response) {

                })
            }
            $scope.loadPanel = function () {
                $scope.allLodaing =true;
                $http({
                    url:'{{URL::route('select_status')}}',
                    params:$scope.formData,
                    method:'get'
                }).then(function (response) {
                    $scope.allLodaing = false;
                    $scope.ansars = response.data;
                }, function (response) {

                })
            }

        })
        GlobalApp.directive('openHideModal', function () {
            return {
                restrict: 'AC',
                link: function (scope, elem, attr) {
                    $(elem).on('click', function () {
                        //alert("hh")
//                        scope.memorandumId = "";
//                        scope.kpi_withdraw_date = "";
                        scope.$digest()
                        $("#panel-modal").modal("toggle");
                    })
                }
            }
        })
    </script>

    <div class="content-wrapper" ng-controller="PanelController">
        <div id="all-loading"
             style="position:fixed;width: 100%;height: 100%;background-color: rgba(255, 255, 255, 0.27);z-index: 100; margin-left: 30%; display: none; overflow: hidden">
            <div style="position: fixed;width: 20%;height: auto;margin: 20% auto;text-align: center;background: #FFFFFF">
                <img class="img-responsive" src="{{asset('dist/img/loading-data.gif')}}"
                     style="position: relative;margin: 0 auto">
                <h4>Loading....</h4>
            </div>

        </div>
        <!-- Content Header (Page header) -->

        <!-- Main content -->
        @if(Session::has('success_message'))
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-ok"></span> {{Session::get('success_message')}}
                </div>
            </div>
        @endif
        <meta name="csrf-token" content="{{ csrf_token() }}"/>
        <section class="content">

            <div class="box box-solid" style="min-height: 200px;">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a data-toggle="tab" href="#pc">Panel Information</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active">
                            <div class="row" style="margin-left:0; margin-right: 0;padding-bottom: 10px">
                                <div class="col-md-4 pull-right">
                                    <a class="btn btn-primary pull-right" data-toggle="modal"
                                       data-target="#panel-modal" style="margin-bottom: 10px"><span
                                                class="glyphicon glyphicon-save"> Load for Panel</span></a>
                                </div>
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="pc-table">

                                            <tr class="info">
                                                <th>Ansar ID</th>
                                                <th>Ansar Name</th>
                                                <th>Ansar Rank</th>
                                                <th>Ansar Unit</th>
                                                <th>Ansar Thana</th>
                                                <th>Date of Birth</th>
                                                <th>Gender</th>
                                                <th>Merit List</th>
                                                <th>
                                                    <div class="styled-checkbox">
                                                        <input type="checkbox" id="check-all-panel">
                                                        <label for="check-all-panel"></label>
                                                    </div>
                                                </th>
                                                {{--<th><input type="checkbox" id="select-all-panel" name="" value=""--}}
                                                {{--style="height: 20px; width: 25px"> Select All--}}
                                                {{--</th>--}}
                                            </tr>
                                            <tbody id="status-all" class="status">
                                            <tr colspan="11" class="warning" id="not-find-info">
                                                <td colspan="11">No Ansar Found to add to Panel</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <!-- /.box
            -footer -->
            <div class="row">
                <div class="col-md-12">
                    <button class="btn btn-primary pull-right " data-toggle="modal"
                            data-target="#confirm-panel-modal" id="confirm-panel" open-hide-modal disabled>Add to Panel
                    </button>
                </div>
            </div>
            <!--Modal Open-->
            <div id="panel-modal" class="modal fade" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h3 class="modal-title">Panel Option</h3>
                        </div>
                        <div class="modal-body">
                            <div class="offer-loading" ng-show="showLoadingScreen">
                                <i class="fa fa-spinner fa-pulse fa-2x" style="position: relative;left:48%;top:40%"></i>
                            </div>
                            <div class="register-box" style="width: auto;margin: 0">
                                <div class="register-box-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group row">
                                                <div class="col-sm-12">
                                                    <form action="" method="get">
                                                        <select name='come_from_where' id='come_from_where'
                                                                class="form-control">
                                                            <option value="" disabled selected>--Select--</option>
                                                            <option value="1">Rest Status</option>
                                                            <option value="2">Free Status</option>
                                                        </select>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <ul style="padding: 0">
                                                <li class="btn select-choice" id="all-select">
                                                    <span><input type="radio" value="1" name="choice"
                                                                 checked> All</span>
                                                </li>
                                                <li class="btn select-choice" id="custom-select">
                                                    <span><input type="radio" value="0"
                                                                 name="choice"> Custom Select</span>
                                                </li>
                                            </ul>
                                            <ul style="list-style: none;display: none; margin-left: 0 !important;padding: 0"
                                                class="row custom-selected">
                                                <li class="form-group col-md-6 custom-selected" id="from-id">
                                                    <span>From(ID) <input type="text" name="from-id"
                                                                          class="form-control"></span>
                                                </li>
                                                <li class="form-group col-md-6" id="to-id">
                                                    <span>To(ID) <input type="text" name="to-id"
                                                                        class="form-control"></span>
                                                </li>

                                            </ul>
                                            <button class="btn btn-default pull-right" id="load-panel"><i
                                                        class="fa fa-download" open-hide-modal></i>
                                                Load
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Modal Close-->
            <!--Modal Open-->
            <div id="confirm-panel-modal" class="modal fade" role="dialog">
                <div class="modal-dialog" style="width: 70%;overflow: auto;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"
                                    ng-click="modalOpen = false">&times;</button>
                            <h3 class="modal-title">Add to Panel</h3>
                        </div>
                        {!! Form::open(array('url' => 'save-panel-entry', 'id'=>'panel-form', 'name' => 'panelForm', 'method' => 'post')) !!}
                        <div class="modal-body">
                            <div class="register-box" style="width: auto;margin: 0">
                                <div class="register-box-body  margin-bottom">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label">Memorandum no.&nbsp;&nbsp;&nbsp;<span
                                                            ng-show="isVerifying"><i
                                                                class="fa fa-spinner fa-pulse"></i>&nbsp;Verifying</span><span
                                                            class="text-danger"
                                                            ng-if="isVerified&&!memorandumId">Memorandum no. is required.</span><span
                                                            class="text-danger"
                                                            ng-if="isVerified&&memorandumId">This id already taken.</span></label>
                                                <input ng-blur="verifyMemorandumId()" ng-model="memorandumId"
                                                       type="text" class="form-control" name="memorandum_id"
                                                       placeholder="Enter Memorandum no." required>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label">Panel Date <span class="text-danger"
                                                                                              ng-show="panelForm.panel_date.$touched && panelForm.panel_date.$error.required"> Date is required.</span></label>
                                                &nbsp;&nbsp;&nbsp;</label>
                                                {!! Form::text('panel_date', $value = null, $attributes = array('class' => 'form-control', 'id' => 'panel_date', 'ng_model' => 'panel_date', 'required','date-picker'=>'moment()')) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="pc-table">
                                            <tr class="info">
                                                <th>Ansar ID</th>
                                                <th>Ansar Name</th>
                                                <th>Ansar Rank</th>
                                                <th>Ansar Unit</th>
                                                <th>Ansar Thana</th>
                                                <th>Date of Birth</th>
                                                <th>Sex</th>
                                                <th>Merit List</th>
                                            </tr>
                                            <tbody id="status-all-modal" class="status">
                                            <tr colspan="9" class="warning" id="not-find-info">
                                                <td colspan="9">No Ansar Found to Withdraw</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <button class="btn btn-primary pull-right" id="confirm-panel-entry">
                                        <i class="fa fa-check"></i>&nbsp;Confirm
                                    </button>
                                    {!! Form::close() !!}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <!--Modal Close-->
            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div><!-- /.content-wrapper -->
    <script>
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
        })
        var h = "";
        var choice=0;
        $('#load-panel').click(function (e) {
            $("#all-loading").css('display', 'block');
            e.preventDefault();
            $('#check-all-panel').prop('checked', false);
            $('.check-panel').prop('checked', false);
            selectedAnsars=[];
            selectedValue = $("#come_from_where").val();
            choice = $('input[name="choice"]:checked').val();
//            alert(choice)
            if (choice == 1) {
                var data = {status: selectedValue, select: choice}
            }
            else {
                var data = {
                    status: selectedValue,
                    select: choice,
                    from: $('input[name="from-id"]').val(),
                    to: $('input[name="to-id"]').val()
                }
            }
            //make the ajax call
            $.ajax({
                url: '{{action('PanelController@statusSelection')}}',
                type: 'get',
                data: data,
                success: function (data) {
                    //console.log(data)
                    //alert(data.view)
                    //alert(data)
                    if (data.result == undefined) {
                        $('#confirm-panel').prop('disabled', false);
                        $("#status-all").html(data);
                        h=data;
                        $("#all-loading").css('display', 'none');
                    }
                    else {
                        $('#confirm-panel').prop('disabled', true);
//                        alert($("#status-all").html())
                        $("#status-all").html('<tr colspan="11" class="warning" id="not-find-info"> <td colspan="11">No Ansar Found to add to Panel</td> </tr>');
                    }

                }
            });
            $(".close").trigger('click');
        });

        $('#confirm-panel').click(function (e) {
            e.preventDefault();
            var innerHtml = "";
            selectedAnsars.forEach(function (a, b, c) {
                var d = a.clone();
                var text = $(a.children('td')[7]).children('input').val();
                d.children('td')[0].innerHTML += "<input type='hidden' name='selected-ansar_id[]' value='" + $.trim($(d.children('td')[0]).text()) + "'>";
                // ansar_ids.push($.trim($(d.children('td')[0]).text()));
                d.children('td')[7].innerHTML = text + "<input type='hidden' name='ansar_merit[]' value='" + text + "'>";
                d.children('td')[8].remove();
                innerHtml += '<tr>' + d.html() + '</tr>';
            })
            $('#status-all-modal').html(innerHtml)
        });

        $(function () {
            $('input[name="choice"]').change(function () {
                if ($(this).val() == 0)
                    $('.custom-selected').show();
                else $('.custom-selected').hide();
            });
        });


        var selectedAnsars = []
        $(document).on('change', '.check-panel', function () {
            if( $('#check-all-panel').prop("checked") == true) {
                $('#check-all-panel').prop('checked', $(this).prop('checked'));
            }
           if($('.check-panel:checked').length==($('.check-panel').length)){
                   $('#check-all-panel').prop('checked','checked');
           }
            selectedAnsars=[]
            $('.check-panel:checked').each(function () {
                selectedAnsars.push($(this).parents('tr'))
            })
//            if (this.checked) {
//                //alert($(this).parents('tr').splice(7, 1).html())
//                selectedAnsars.push($(this).parents('tr'))
//            } else {
////                alert("Hello");
//                selectedAnsars.splice(selectedAnsars.indexOf($(this).parents('tr')), 1)
//            }
            //alert(selectedAnsars.length)
        })

        $('#confirm-panel-entry').click(function (e) {
            $("#all-loading").css('display', 'block');
            e.preventDefault();
            $("#panel-form").ajaxSubmit({
                success: function (a, b, c, d) {
                    console.log(a)
                    selectedAnsars.forEach(function (a, b, c) {
                        a.remove()
                    })
                    if (a.status) {
                        $("#all-loading").css('display', 'none');
                        $('body').notifyDialog({type: 'success', message: a.message}).showDialog()

                    }
                },

                error: function (a, b, c, d) {
                    console.log(a)
                    $("#all-loading").css('display', 'none');
                    document.write(a.responseText)
                    $('body').notifyDialog({type: 'error', message: "Server Error. Please Try again!"}).showDialog()

                },
                beforeSubmit: function (arr) {
//                    $("#all-loading").css('display', 'block');
                    arr.push({type:'text', value: selectedValue, name: 'come_from_where'})
                    console.log(arr)

                }
            })
            $(".close").trigger('click');
        });
        $("#check-all-panel").change(function () {
            $(".check-panel").prop('checked', $(this).prop('checked'));
            $(".check-panel").trigger('change')
        });
        /****************************************************/

    </script>
@endsection