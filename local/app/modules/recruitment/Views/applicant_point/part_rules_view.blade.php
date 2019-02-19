<table class="table table-bordered table-condensed">
    <caption style="font-size: 20px">Mark Rules<a
                href="{{URL::route('recruitment.marks_rules.create')}}"
                class="btn btn-primary btn-xs pull-right">Add new field</a></caption>
    <tr>
        <th>SL. No</th>
        <th>Circular name</th>
        <th>Rule name</th>
        <th>Rule for</th>
        <th>Rules</th>
        <th>Action</th>
    </tr>
    <?php $i = 1;?>
    @forelse($points as $point)
        <tr>
            <td>{{$i++}}</td>
            <td>{{$point->circular->circular_name}}</td>
            <td>{{$point->rule_name}}</td>
            <td>{{$point->point_for}}</td>
            @if($point->rule_name==='education')
                <td>{!!  $point->getEducationRules()!!}</td>
            @elseif($point->rule_name==='height')
                <td>{!! $point->getHeightRules() !!}</td>
            @elseif($point->rule_name==='training')
                <td>{!! $point->getTrainingRules() !!}</td>
            @elseif($point->rule_name==='experience')
                <td>{!! $point->getExperienceRules() !!}</td>
            @elseif($point->rule_name==='age')
                <td>{!! $point->getAgeRules() !!}</td>
            @endif
            <td>
                <a class="btn btn-primary btn-xs" href="{{URL::route('recruitment.marks_rules.edit',['id'=>$point->id])}}">
                    <i class="fa fa-edit"></i>&nbsp;Edit
                </a>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="bg-warning">
                No Point Rule available.
            </td>
        </tr>
    @endforelse
</table>