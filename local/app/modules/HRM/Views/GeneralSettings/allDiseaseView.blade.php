@extends('template.master')
@section('title','Disease Information')
@section('small_title')
    <a class="btn btn-primary" href="{{URL::to('HRM/add_disease')}}">
        <span class="glyphicon glyphicon-plus"></span> Add New Unit
    </a>

@endsection
@section('breadcrumb')
    {!! Breadcrumbs::render('disease_information_list') !!}
@endsection
@section('content')

<?php $i = 1; ?>
    <div>
        @if(Session::has('success_message'))
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-ok"></span> {{Session::get('success_message')}}
                </div>
            </div>
        @endif
        <!-- Content Header (Page header) -->
        <!-- Main content -->
        <section class="content">

            <div class="row" style="margin-left: 20px; margin-right: 20px">

                <div class="row">
                    {{--<div style="margin:0 16px;">
                        <div class="table-header">
                            <h5>Disease list</h5>
                        </div>
                    </div>--}}
                </div>
                <div class="box">

                    <div class="box-body">
                        <table id="unit-table" class="table table-bordered table-hover table-striped">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Disease Name in English</th>
                                <th>Disease Name in Bangla</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($disease_infos as $disease_info)
                                <tr>
                                    <th scope="row">{{ $i++}}</th>
                                    <td>{{ $disease_info->disease_name_eng }}</td>
                                    <td>{{ $disease_info->disease_name_bng }}</td>
                                    <td><a href="{{ URL::to('HRM/disease_edit/'.$disease_info->id) }}" class="btn btn-primary btn-xs" title="Edit"><span
                                                    class="glyphicon glyphicon-edit"></span></a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- /.box-body -->
                    <div class="table_pagination">
                        {!! $disease_infos->render() !!}
                    </div>
                </div>
                <!-- /.box -->
            </div>
            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div><!-- /.content-wrapper -->
    <script>
        $("#unit-table").sortTable({
            exclude:7
        })
    </script>

@endsection
