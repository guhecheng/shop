@include('/admin/header')
@yield('title', '卡券列表页')
@include('/admin/menu')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            权限列表
        </h1>
        <button type="button" class="btn btn-primary add-btn">添加</button>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">权限列表</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">

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
                <h4 class="modal-title">权限管理</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="cardname" class="col-sm-2 control-label">父类权限</label>
                    <div class="col-sm-10">
                        <select id="parent_auth">
                            <option value="0">顶级权限</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="cardname" class="col-sm-2 control-label">添加权限名</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="add_authname" id="add_authname" placeholder="请输入权限名">
                    </div>
                </div>
                <div class="form-group">
                    <label for="cardname" class="col-sm-2 control-label">添加对应url</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="add_url" id="add_url" placeholder="请输入对应地址">
                    </div>
                </div>
                <br clear="all" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <input type="submit" class="btn btn-primary add_auth" value="添加" />
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<!-- /.content-wrapper -->
<footer class="main-footer">
    <div class="pull-right hidden-xs">
    </div>
    <strong>Copyright &copy; 2014-2017</strong> All rights
    reserved.
</footer>

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
    $(function() {

    });
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(".add-btn").on("click", function() {
        $("#add-modal").modal(true);
    });
    $(".add_auth").on("click", function () {
        var parent_auth_id = $("#parent_auth").val();
        var auth_url = $("#add_url").val();
        var auth_name = $("#add_authname").val();
        if (auth_name == '' || auth_url == '') {
            alert('信息填写不全');
            return false;
        }
        $.ajax({
            url:'/admin/auth/add',
            type: 'post',
            data: {'parent_auth_id': parent_auth_id, 'auth_name': auth_name, 'auth_url' : auth_url},
            dataType: 'json',
            success: function(data) {

            }
        })
    });
</script>