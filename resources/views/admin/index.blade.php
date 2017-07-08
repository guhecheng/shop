@include('/admin/header')
@yield('title', '卡券列表页')
@include('/admin/menu')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            修改密码
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">修改密码</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="form-group">
                            <label for="name">原密码</label>
                            <input type="password" class="form-control" id="old_pass" placeholder="原密码">
                        </div>
                        <div class="form-group">
                            <label for="name">新密码</label>
                            <input type="password" class="form-control" id="new_pass" placeholder="新密码">
                        </div>
                        <div class="form-group">
                            <label for="name">重复密码</label>
                            <input type="password" class="form-control" id="repeat_pass" placeholder="重复密码">
                        </div>
                        <button type="submit" class="btn btn-default" id="mod_btn">提交</button>
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
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $("#mod_btn").on('click', function () {
        var old_pass =$("#old_pass").val();
        var new_pass = $("#new_pass").val();
        var repeat_pass = $("#repeat_pass").val();
        if (old_pass == '' || new_pass == '' || repeat_pass == '') {
            alert('密码不能为空');
            return false;
        }
        if (new_pass != repeat_pass) {
            alert('新密码两次不一致');
            return false;
        }
        if (new_pass.length < 6) {
            alert('密码过短');
            return false;
        }
        $.ajax({
            url: '/admin/modify',
            type: 'post',
            data: { 'old_pass': old_pass, 'new_pass': new_pass, 'repeat_pass': repeat_pass },
            dataType: 'json',
            success: function(data) {
                if (data.rs == 1) {
                    alert('密码修改成功');
                    return false;
                } else {
                    alert(data.errmsg);
                    return;
                }

            }
        })
    });
    $(function() {
    });
</script>