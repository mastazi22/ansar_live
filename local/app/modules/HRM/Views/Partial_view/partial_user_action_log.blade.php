@if(isset($logs)&&count($logs)>0)
    <ul class="timeline">
        @forelse($logs as $date=>$log)
            <li class="time-label">
                            <span class="bg-green">
                                {{$date}}
                            </span>
            </li>
            @foreach($log as $item)
                <li>
                    <!-- timeline icon -->
                    <i class="fa fa-cog bg-blue"></i>

                    <div class="timeline-item">
                        <span class="time"><i class="fa fa-clock-o"></i> {{$item->time}}</span>

                        <h3 class="timeline-header" style="background: rgba(0, 120, 112, 0.15);"><a
                                    href="#">{{$item->action_type or 'UNDEFINED'}}</a></h3>

                        <div class="timeline-body">
                            Ansar({{$item->ansar_id}}) transferred to status {{$item->to_state}}
                        </div>
                    </div>
                </li>
            @endforeach
        @empty

        @endforelse
    </ul>
@elseif(isset($logs))
    <div class="alert alert-warning">
        <i class="fa fa-warning"></i>&nbsp;No Activity Available
    </div>
@endif