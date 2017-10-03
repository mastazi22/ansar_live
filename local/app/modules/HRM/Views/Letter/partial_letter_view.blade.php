<table class="table table-bordered table-striped">
    <caption>
        <table-search q="q" results="results" place-holder="Search Memorandum no."></table-search>
    </caption>
    <tr>
        <th>#</th>
        <th>Memorandum no.</th>
        <th>Memorandum Date</th>
        <th>Unit</th>
        <th>Action</th>
    </tr>
    <?php $i = 1; ?>
    @foreach($data as $mem)
        <tr>
            <td>{{$i++}}</td>
            <td>
                {{$mem->memorandum_id}}
            </td>
            <td>{{$mem->mem_date?($mem->mem_date):'n/a'}}</td>
            <td>
                @if(auth()->user()->type!=22)
                    <select class="form-control" name="unit_list">
                        <option value="">--@lang('title.unit')--</option>
                        @foreach($units as $u)
                            <option value="{{$u->id}}">{{$u->unit_name_bng}}</option>
                        @endforeach
                    </select>
                @else
                    <div>
                        {{auth()->user()->district?auth()->user()->district->unit_name_eng:''}}

                    </div>
                @endif
            </td>
            <td>
                {!! Form::open(['route'=>'print_letter','target'=>'_blank']) !!}
                {!! Form::hidden('option','memorandumNo') !!}
                {!! Form::hidden('id',$mem->memorandum_id) !!}
                {!! Form::hidden('type','TRANSFER') !!}
                {!! Form::hidden('unit',auth()->user()->district?auth()->user()->district->id:'',['id'=>'unit_mem']) !!}
                <button class="btn btn-primary">Generate Letter</button>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    <tr ng-if="datas==undefined||datas.length<=0||results.length<=0">
        <td class="warning" colspan="5">No Memorandum no. available</td>
    </tr>
</table>
{!! $data->render() !!}