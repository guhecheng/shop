@include('/admin/header')
@yield('title', '卡券列表页')
@include('/admin/menu')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <a href="/admin/type"><h2>{{ $typename }}属性列表</h2></a>
        <button type="button" class="btn btn-primary add-btn">添加</button>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">属性列表</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table id="example2" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>属性名</th>
                                <th>是否选择</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($propertys as $property)
                                <tr>
                                    <td>{{ $property->key_name }}</td>
                                    <td>{{ empty($property->is_enum) ? '否' : '是'  }}</td>
                                    <td>
                                        <input type="hidden" name="is_enum" class="is_enum" value="{{ $property->is_enum }}" />
                                        <button type="button" class="btn btn-primary modify-btn" attr-id="{{ $property->key_id }}">修改</button>
                                        <button type="button" class="btn btn-primary del-btn" attr-id="{{ $property->key_id }}">删除</button>
                                        @if (!empty($property->is_enum))
                                        <a href="/admin/property/listvalue?keyid={{ $property->key_id }}&typeid={{ $typeid }}"><button type="button" class="btn btn-primary" attr-id="{{ $property->key_id }}">添加属性值</button></a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->

            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>

<div class="modal fade" id="add-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">添加属性</h4>
            </div>
            <form action="/admin/property/addkey" method="post" >
                <div class="modal-body">
                    <div class="form-group">
                        <label for="cardname" class="col-sm-2 control-label">属性名</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="keyname" id="keyname" placeholder="请输入属性名">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="cardname" class="col-sm-2 control-label">是否选择</label>
                        <div class="col-sm-10">
                            <input type="radio" name="is_enum" value="0">否
                            <input type="radio" name="is_enum" value="1">是
                        </div>
                    </div>
                    <input type="hidden" name="typeid" value="{{ $typeid }}" />
                    {{ csrf_field() }}
                    <br clear="all" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <input type="submit" class="btn btn-primary add_type" value="添加" />
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<div class="modal fade" id="mod-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">编辑种类</h4>
            </div>
            <form enctype="multipart/form-data" method="post" action="/admin/property/modifykey">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="cardname" class="col-sm-2 control-label">种类名</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="keyname" id="edit_keyname" placeholder="请输入种类名">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="cardname" class="col-sm-2 control-label">是否选择</label>
                        <div class="col-sm-10">
                            <input type="radio" name="edit_is_enum" value="0">否
                            <input type="radio" name="edit_is_enum" value="1">是
                        </div>
                    </div>
                    <input type="hidden" value="" name="keyid" id="edit_keyid" />
                    <input type="hidden" value="{{ $typeid }}" name="typeid" />
                    {{ csrf_field() }}
                    <br clear="all" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <input type="submit" class="btn btn-primary update_card" value="修改" />
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<!-- /.content-wrapper -->
<footer class="main-footer">
    <div class="pull-right hidden-xs">
    </div>
    <strong>Copyright &copy; 2014-2017</strong> All rights
    reserved.
</footer>

<!-- Add the sidebar's background. This div must be placed
     immediately after the control sidebar -->
<div class="control-sidebar-bg"></div>
</div>

<script src="/css/admin/plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="/css/admin/bootstrap/js/bootstrap.min.js"></script>
<!-- DataTables -->
<script src="/css/admin/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="/css/admin/plugins/datatables/dataTables.bootstrap.min.js"></script>
<!-- SlimScroll -->
<script src="/css/admin/plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="/css/admin/plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="/css/admin/dist/js/app.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="/css/admin/dist/js/demo.js"></script>

<link href="/css/fileinput.min.css" media="all" rel="stylesheet" type="text/css"/>
<link href="/css/admin/themes/explorer/theme.css" media="all" rel="stylesheet" type="text/css"/>
<script src="/js/plugins/sortable.js" type="text/javascript"></script>
<script src="/js/fileinput.min.js" type="text/javascript"></script>
<script src="/js/zh.js" type="text/javascript"></script>
<script src="/css/admin/themes/explorer/theme.js" type="text/javascript"></script>

<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(function() {
        $(".add-btn").on("click", function() {
            $("#add-modal").modal('show');
        });

        $(".modify-btn").on("click", function () {
            var attr_id = $(this).attr("attr-id");
            var par = $(this).parent().parent();
            $("#edit_keyname").val($.trim(par.find("td:eq(0)").text()));
            $("input[name='edit_is_enum'][value='" + par.find(".is_enum").val() + "']").prop("checked", "checked");
            $("#edit_keyid").val(attr_id);
            console.log(attr_id);
            $("#mod-modal").modal('show');
        });

        $(".del-btn").on("click", function() {
            if (!confirm('确定删除?')) return ;
            var keyid = $(this).attr("attr-id");
            if (keyid == '' || typeof keyid == 'undefined') return false;
            var that = $(this);

            $.ajax({
                url:'/admin/property/deletekey',
                data: {'keyid': keyid},
                type:"get",
                dataType:'json',
                success: function(data) {
                    if (data.rs == 1)
                        that.parent().parent().remove();
                }
            })
        });

    });
</script>