@extends('template.master')
@section('content')
    <section class="content-header">
        <h1>Demand Constant</h1>
    </section>
    <section class="content">
        @if(Session::has('constant_update_success'))
            <div class="alert alert-success">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                {{Session::get('constant_update_success')}}
            </div>
        @endif
        <div class="box box-primary">
            <!-- form start -->
            <form role="form" action="{{URL::to('SD/updateconstant')}}" method="post">
                {!! csrf_field() !!}
                <div class="box-body">
                    @foreach($constants as $constant)
                        <div class="form-group @if($errors->has($constant->cons_name))has-error @endif">
                            <label style="text-transform: capitalize" for="{{$constant->cons_name}}">{{implode(" ",explode("_",$constant->cons_name))}}</label>
                            <input type="number" name="{{$constant->cons_name}}" class="form-control" value="{{Request::old($constant->cons_name)==''?$constant->cons_value:Request::old($constant->cons_name)}}" id="{{$constant->cons_name}}" placeholder="Enter a value">
                            @if($errors->has($constant->cons_name))
                                <p class="help-block">{{$errors->first($constant->cons_name)}}</p>
                            @endif
                        </div>
                    @endforeach
                </div><!-- /.box-body -->

                <div class="box-footer">
                    <button type="submit" class="btn btn-primary">Update Value</button>
                </div>
            </form>
        </div>
    </section>
@endsection