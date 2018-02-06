<?php $i=1 ?>
<div class="table-responsive">
    <table class="table table-bordered">
        <tr>
            <th>SL. No</th>
            <th>Job Circular Name</th>
            <th>Selection Date</th>
            <th>Selection Place</th>
            <th>Selection Units</th>
            <th>Written Viva Date</th>
            <th>Written Viva Place</th>
            <th>Written Viva Units</th>
            <th style="width: 100px;">Action</th>
        </tr>
        @forelse($data as $d)
            <tr>
                <td>{{$i++}}</td>
                <td>{{$d->circular->circular_name}}</td>
                <td>{{$d->selection_date}}</td>
                <td>{{$d->selection_place}}</td>
                <td>{{$d->selection_units}}</td>
                <td>{{$d->written_viva_date}}</td>
                <td>{{$d->written_viva_place}}</td>
                <td>{{$d->written_viva_units}}</td>
                <td>
                    <a class="btn btn-primary btn-xs" href="{{URL::route('recruitment.exam-center.edit',['id'=>$d->id])}}">
                        <i class="fa fa-edit"></i>
                    </a>
                    {!! Form::open(['route'=>['recruitment.exam-center.destroy',$d->id],'method'=>'delete','style'=>'display:inline-block']) !!}
                        <button class="btn btn-danger btn-xs">
                            <i class="fa fa-trash"></i>
                        </button>
                    {!! Form::close() !!}
                </td>
            </tr>
        @empty
            <tr>
                <td class="bg-warning" colspan="9">No Exam Center Available</td>
            </tr>
        @endforelse
    </table>
</div>