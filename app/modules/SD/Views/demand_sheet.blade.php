@extends('template.master')
@section('content')
    <section class="content-header">
        <h1>Demand Sheet</h1>
    </section>
    <section class="content">
        <div class="box box-primary">
            <!-- form start -->
            <form role="form" id="demand_sheet_form" action="{{URL::to('SD/generatedemandsheet')}}" method="post">
                {!! csrf_field() !!}
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-4">
                            @if(isset($units))
                                <div class="form-group">
                                    <label for="unit_list">Select District</label>
                                    <select class="form-control" id="unit_list">
                                        <option value="">--Select a unit--</option>
                                        @foreach($units as $unit)
                                            <option value="{{$unit->id}}">{{$unit->unit_name_bng}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <div class="form-group">
                                <label for="kpi_list">Select KPI</label>
                                <select class="form-control" name="kpi" id="kpi_list">
                                    <option value="">--Select a kpi--</option>
                                    @if(isset($kpis))
                                        @foreach($kpis as $kpi)
                                            <option value="{{$kpi->id}}">{{$kpi->kpi_name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="from_date">From date</label>
                                <input class="form-control dddd" id="from_date" name="form_date" type="text">
                            </div>
                            <div class="form-group">
                                <label for="to_date">To date</label>
                                <input class="form-control dddd" id="to_date" name="to_date" type="text">
                            </div>
                            <div class="form-group">
                                <label for="Other_date">Request payment date</label>
                                <input class="form-control dddd" id="Other_date" name="other_date" type="text">
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i id="llllll" style="display: none" class="fa fa-refresh fa-spin"></i>&nbsp;&nbsp;Generate Demand Sheet</button>
                            </div>
                        </div>
                        <div class="col-sm-8">

                        </div>
                    </div>
                </div><!-- /.box-body -->

                <div class="box-footer">

                </div>
            </form>
        </div>
    </section>
    <script>
        $(".dddd").datePicker(false)
        $("#demand_sheet_form").ajaxForm({
            beforeSubmit: function () {
                $("#llllll").show();
            },
            success: function (response) {
                console.log(response);
                $("#llllll").hide();
            }
        })
    </script>
@endsection