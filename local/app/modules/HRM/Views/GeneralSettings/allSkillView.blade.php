@extends('template/master')
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
                    <div style="margin:0 16px;">
                        <div class="table-header">
                            <h5>Skill list</h5>
                        </div>
                    </div>
                    <div style="float:left;padding: 3px 5px;">
                           <a class="btn btn-primary" href="{{URL::to('HRM/add_skill')}}">Add new Skill</a>
                    </div>
                </div>
                <div class="box">

                    <div class="box-body">
                        <table id="unit-table" class="table table-bordered table-hover table-striped">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Skill Name in English</th>
                                <th>Skill Name in Bangla</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($skill_infos as $skill_info)
                                <tr>
                                    <th scope="row">{{ $i++}}</th>
                                    <td>{{ $skill_info->skill_name_eng }}</td>
                                    <td>{{ $skill_info->skill_name_bng }}</td>
                                    <td><a href="{{ URL::to('HRM/skill_edit/'.$skill_info->id) }}" class="btn btn-primary btn-xs" title="Edit"><span
                                                    class="glyphicon glyphicon-edit"></span></a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- /.box-body -->
                    <div class="table_pagination">
                        {!! $skill_infos->render() !!}
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
