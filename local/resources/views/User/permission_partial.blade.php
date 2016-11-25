@foreach($data as $p)
    <li>
        @if(isset($p->name))
            <label class="control-label">
                <div class="styled-checkbox">
                    {!! Form::checkBox(implode('_',explode(' ',$p->name)),$p->value,null,['id'=>implode('_',explode(' ',$p->name))]) !!}
                    {{--{!! Form::label(,'') !!}--}}
                    <label for="{{implode('_',explode(' ',$p->name))}}"></label>
                </div>
                {{$p->name}}
            </label>
        @elseif(isset($p->text))
            <ul class="sub-permission">
                <li>
                <span class="title text text-bold">
                    <a class="tree-view" href="#">
                        <i class="fa fa-minus fa-xs"></i>
                    </a>&nbsp;{{$p->text}}
                </span>
                    <ul>
                        @include('user.permission_partial',['data'=>$p->actions])
                    </ul>
                </li>
            </ul>
        @endif
    </li>
@endforeach