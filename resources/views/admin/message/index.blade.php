@include('/admin/header')
@yield('title', '卡券列表页')
@include('/admin/menu')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            消息列表
        </h1>
        <button type="button" class="btn btn-primary add-btn">添加</button>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">信息列表</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table id="example2" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>信息内容</th>
                                <th>是否发送</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody class="content-body">
                            @foreach($messages as $message)
                                <tr>
                                    <td>{!!$message->content !!}</td>
                                    <td>@if ($message->is_send == 1)
                                            已发送
                                        @else
                                            未发送
                                        @endif
                                    </td>
                                    <td>
                                        @if ($message->is_send == 0)
                                            <button class="send_btn" attr-id="{{ $message->id }}">发送</button>
                                            <button class="mod_btn" attr-id="{{ $message->id }}">修改</button>
                                        @endif
                                        <button class="del_btn" attr-id="{{ $message->id }}">删除</button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <td rowspan="4">
                                    {{ $messages->links() }}
                                </td>
                            </tr>
                            </tfoot>
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
                <h4 class="modal-title">添加信息</h4>
            </div>
            <div class="modal-body">
            <div class="form-group">
                <label for="cardname" class="col-sm-2 control-label">发送内容</label>
                <div class="col-sm-10">
                    <textarea id="content"></textarea>
                </div>
            </div><br clear="all" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <input type="submit" class="btn btn-primary add_msg" value="添加" />
            </div>
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
                <h4 class="modal-title">编辑内容</h4>
            </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="cardname" class="col-sm-2 control-label">发送内容</label>
                        <div class="col-sm-10">
                            <textarea id="mod_content"></textarea>
                        </div>
                    </div><br clear="all" />
                </div>
                <input type="hidden" id="mod_id" value="" />
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <input type="submit" class="btn btn-primary update_btn" value="修改" />
                </div>
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
        $(document).on("click", ".add-btn", function() {
            $("#add-modal").modal('show');
        });
        $(document).on("click", ".mod_btn", function() {
            $("#mod-modal").modal('show');
            var content = $(this).parent().parent().find("td:eq(0)").text();
            $("#mod_content").val($.trim(content));
            $("#mod_id").val($(this).attr('attr-id'));
        });
        $(".add_msg").on("click", function () {
            var content = $("#content").val();
            if (content == '') {
                alert('信息不能为空');
                return false;
            }
            $.ajax({
                url: '/admin/message/add',
                data: { 'content': content},
                dataType:'json',
                type:'post',
                success: function (data) {
                    if (data.rs == 1) {
                        alert('添加成功');
                        var html = '<tr>';
                        html += '<td>'+content+'</td>';
                        html += '<td>未发送</td>';
                        html += '<td>';
                        html += '<button class="send_btn" attr-id="'+data.id+'">发送</button>';
                        html += '<button class="mod_btn" attr-id="'+data.id+'">修改</button>';
                        html += '<button class="del_btn" attr-id="'+data.id+'">删除</button>'
                        html += '</td>';
                        html += '</tr>';
                        $(".content-body").prepend(html);
                        $("#add-modal").modal('hide');
                        $("#content").val('');
                    } else {
                        alert('修改失败');
                        return false;
                    }
                }
            });
        });
        $(".update_btn").on("click", function () {
            var id = $("#mod_id").val();
            console.log(id);
            var content = $.trim($("#mod_content").val());
            if (content == '') {
                alert('不能提交空信息');
                return false;
            }
            $.ajax({
                url: '/admin/message/update',
                data: { 'content': content, 'id': id},
                dataType:'json',
                type:'post',
                success: function (data) {
                    if (data.rs == 1) {
                        alert('修改成功');
                        $(".mod_btn").each(function() {
                            if ($(this).attr('attr-id') == id) {
                                $(this).parent().parent().find("td:eq(0)").text(content);
                            }
                        });
                        $("#mod-modal").modal('hide');
                    } else {
                        alert('修改失败');
                    }
                }
            });
        });

        $(document).on('click', ".del_btn", function() {
            if (!confirm('确定删除?')) return ;
            var messsage_id = $(this).attr("attr-id");
            if (messsage_id == '' || typeof messsage_id == 'undefined') return false;
            var that = $(this);

            $.ajax({
                url:'/admin/message/delete',
                data: {'id': messsage_id},
                type:"get",
                dataType:'json',
                success: function(data) {
                    if (data.rs == 1)
                        that.parent().parent().remove();
                }
            })
        });

        $(document).on('click', ".send_btn", function() {
            if (!confirm('确定发送?')) return ;
            var messsage_id = $(this).attr("attr-id");
            if (messsage_id == '' || typeof messsage_id == 'undefined') return false;
            var that = $(this);

            $.ajax({
                url:'/admin/message/send',
                data: {'id': messsage_id},
                type:"get",
                dataType:'json',
                success: function(data) {
                    if (data.rs == 1)
                        //that.parent().parent().remove();
                        location.reload();
                }
            })
        });
    });
</script>