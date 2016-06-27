@if(count($kpi_info)<=0)
    <h3 style="text-align: center">No KPI Found</h3>
@else
    @foreach($kpi_info as $kpi)
    <div class="form-group">
        <label class="control-label">KPI Name</label>

        <p>
            {{$kpi->kpi}}
        </p>
    </div>
    <div class="form-group">
        <label class="control-label">Division</label>

        <p>
            {{$kpi->division}}
        </p>
    </div>
    <div class="form-group">
        <label class="control-label">Unit</label>

        <p>
            {{$kpi->unit}}
        </p>
    </div>
    <div class="form-group">
        <label class="control-label">Thana</label>

        <p>
            {{$kpi->thana}}
        </p>
    </div>
    @endforeach
@endif