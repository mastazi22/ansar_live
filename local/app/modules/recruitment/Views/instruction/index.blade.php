@extends('template.master')
@section('title','Application Instruction')
@section('breadcrumb')
    {!! Breadcrumbs::render('recruitment') !!}
@endsection
@section('content')
    <section class="content">
        @if(Session::has('success'))
            <div class="alert alert-success">
                {!! Session::get('success') !!}
            </div>
        @elseif(Session::has('error'))
            <div class="alert alert-danger">
                {!! Session::get('error') !!}
            </div>
        @endif
        <div class="box box-solid">

            <div class="box-body">
                <div class="row" style="margin-bottom: 20px">
                    <div class="col-sm-12">
                        <span class="text-bold" style="font-size: 20px">All Instruction</span>
                        <a href="{{URL::route('recruitment.instruction.create')}}" class="btn btn-primary pull-right">
                            <i class="fa fa-plus"></i>&nbsp;New Instruction
                        </a>
                    </div>
                </div>
                <div class="panel-group" id="accordion">
                    @forelse($instructions as $instruction)
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#{{$instruction->type}}">
                                        {{ucwords(implode(" ",explode("_",$instruction->type)))}}</a>
                                    <a href="{{URL::route('recruitment.instruction.edit',['id'=>$instruction->id])}}" class="btn btn-link" title="edit instruction">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </h4>
                            </div>
                            <div id="{{$instruction->type}}" class="panel-collapse collapse">
                                <div class="panel-body">
                                    {!! $instruction->instruction !!}
                                </div>
                            </div>
                        </div>
                        @empty
                    @endforelse
                </div>
            </div>
        </div>
    </section>
@endsection