@extends('template.master')
@section('content')
    <section class="content-header">
        <h1>Demand Sheet</h1>
    </section>
    <section class="content">
        <div class="box box-primary">
            <!-- form start -->
            <form role="form" action="{{URL::to('SD/updateconstant')}}" method="post">
                {!! csrf_field() !!}
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="kpi_list">Select KPI</label>
                                <select class="form-control" id="kpi_list">
                                    <option value="">--Select a kpi--</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="from_date">From date</label>
                                <input class="form-control dddd" id="from_date" name="from_date" type="text">
                            </div>
                            <div class="form-group">
                                <label for="to_date">To date</label>
                                <input class="form-control dddd" id="to_date" name="to_date" type="text">
                            </div>
                            <div class="form-group">
                                <label for="Other_date">Other date</label>
                                <input class="form-control dddd" id="Other_date" name="other_date" type="text">
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Generate Demand Sheet</button>
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <embed src="https://bitcoin.org/bitcoin.pdf" style="width: 100%;height: 600px"></embed>
                        </div>
                    </div>
                </div><!-- /.box-body -->

                <div class="box-footer">

                </div>
            </form>
        </div>
    </section>
    <script>
        $(".dddd").datepicker({                dateFormat:'dd-M-yy'            })(false)
    </script>
@endsection