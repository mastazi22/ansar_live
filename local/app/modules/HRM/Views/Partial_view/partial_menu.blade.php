@foreach($menus as $menu_title=>$values)
    @if(isset($values['route']))
        @if($values['route']=='#'&&isset($values['children'])&&UserPermission::isMenuExists($values['children']))
            <li class="treeview">
                <a href="{{URL::to($values['route'])}}">
                    <i class="fa {{$values['icon']}}"></i>
                    <span>{{$menu_title}}</span>
                    <i class="fa fa-angle-right pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    @include('HRM::Partial_view.partial_menu',['menus'=>$values['children']])
                </ul>
            </li>
        @elseif($values['route']=='#'&&isset($values['children']))
            <li class="treeview">
                <a href="{{URL::to($values['route'])}}">
                    <i class="fa {{$values['icon']}}"></i>
                    <span>{{$menu_title}}</span>
                    <i class="fa fa-angle-right pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    @include('HRM::Partial_view.partial_menu',['menus'=>$values['children']])
                </ul>
            </li>
        @elseif($values['route']=='#')
            <li>
                <a href="{{URL::to($values['route'])}}">
                    <i class="fa {{$values['icon']}}"></i>
                    <span>{{$menu_title}}</span>
                </a>
            </li>
        @elseif(UserPermission::isMenuExists($values['route']))
            <li>
                <a href="{{URL::route($values['route'])}}">
                    <i class="fa {{$values['icon']}}"></i>
                    <span>{{$menu_title}}</span>
                </a>
            </li>
        @endif
    @endif
@endforeach