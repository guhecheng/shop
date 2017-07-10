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
                        <h3 class="box-title">管理员列表</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table id="example2" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>管理员名</th>
                                <th>登陆名</th>
                                <th>权限列表</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if (!empty($adminuser))
                                @foreach($adminuser as $admin)
                                    <tr>
                                        <td>
                                            {{ $admin->nick_name}}
                                        </td>
                                        <td>{{ $admin->name }}</td>
                                        <td>
                                            @foreach($auths as $auth)
                                                @if ($auth->auth_pid == 0 && in_array($auth->auth_id, explode(',', $admin->auth_ids)))
                                                    <div class="auth-item" style="margin-bottom:5px;">
                                                        <div class="auth-item-p">{{ $auth->auth_name }}</div>
                                                        <div class="auth-item-s">
                                                            @foreach ($auths as $item)
                                                                @if ($item->auth_pid == $auth->auth_id && in_array($auth->auth_id, explode(',', $admin->auth_ids)))
                                                                    {{ $item->auth_name }} &nbsp;&nbsp;
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </td>
                                        <td>
                                            <button class="btn btn-primary abled_btn" attr-id="{{ $admin->admin_id }}" style="{{ $admin->is_disabled?'':'display:none' }}">启用</button>
                                            <button class="btn btn-primary disable_btn" attr-id="{{ $admin->admin_id }}" style="{{ $admin->is_disabled?'display:none':'' }}">禁用</button>
                                            <button class="btn btn-primary mod-auth" attr-id="{{ $admin->admin_id }}">修改权限</button>
                                            <input type="hidden" class="admin_auth_ids" value="{{ $admin->auth_ids }}" />
                                        </td>
                                    </tr>

                                @endforeach
                            @endif
                            </tbody>
                            <tfoot>
                            <tr>
                                <td rowspan="4">
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
                <h4 class="modal-title">权限管理</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="cardname" class="col-sm-4 control-label">账户名</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" name="admin_name" id="admin_name" placeholder="账户名">
                    </div>
                    <br clear="all" />
                </div>
                <div class="form-group">
                    <label for="cardname" class="col-sm-4 control-label">登录名</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" name="login_name" id="login_name" placeholder="请输入登录名">
                    </div>
                    <br clear="all" />
                </div>
                <div class="form-group">
                    <label for="cardname" class="col-sm-4 control-label">登录密码</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" name="login_pass" id="login_pass" placeholder="请输入密码" value="123456">
                    </div>
                    <br clear="all" />
                </div>
                <div class="form-group">
                    <label for="cardname" class="col-sm-4 control-label">选择权限</label>
                    <div class="col-sm-8">
                        @foreach($auths as $auth)
                            @if ($auth->auth_pid == 0)
                            <div class="auth-item">
                                <div class="auth-item-p"><input type="checkbox" class="auth_p" value="{{ $auth->auth_id }}" />{{ $auth->auth_name }}</div>
                                <div class="auth-item-s">
                                @foreach ($auths as $item)
                                    @if ($item->auth_pid == $auth->auth_id)
                                        <input type="checkbox" class="auth" value="{{ $item->auth_id }}" />{{ $item->auth_name }} &nbsp;&nbsp;
                                    @endif
                                @endforeach
                                </div>
                                <br />
                            </div>
                            @endif
                        @endforeach
                    </div>
                    <br clear="all" />
                </div>
                <br clear="all" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <input type="submit" class="btn btn-primary add_admin" value="添加" />
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
                <h4 class="modal-title">权限管理</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="cardname" class="col-sm-4 control-label">账户名</label>
                    <div class="col-sm-8">
                        <span id="mod_auth_name"></span>
                    </div>
                    <br clear="all" />
                </div>
                <div class="form-group">
                    <label for="cardname" class="col-sm-4 control-label">选择权限</label>
                    <div class="col-sm-8">
                        @foreach($auths as $auth)
                            @if ($auth->auth_pid == 0)
                                <div class="auth-item">
                                    <div class="auth-item-p"><input type="checkbox" class="mod_auth_p" value="{{ $auth->auth_id }}" />{{ $auth->auth_name }}</div>
                                    <div class="auth-item-s">
                                        @foreach ($auths as $item)
                                            @if ($item->auth_pid == $auth->auth_id)
                                                <input type="checkbox" class="mod_auth_check" value="{{ $item->auth_id }}" />{{ $item->auth_name }} &nbsp;&nbsp;
                                            @endif
                                        @endforeach
                                    </div>
                                    <br />
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <br clear="all" />
                </div>
                <input type="hidden" id="mod_admin_id" value="" />
                <br clear="all" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <input type="submit" class="btn btn-primary mod_admin" value="修改" />
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
<style type="text/css">
    .auth-item-p, .auth-item-s {
        float:left;
    }
    .auth-item:after {
        display:block;clear:both;content:"";visibility:hidden;height:0
    }
    .auth-item-p { width: 30%;}
</style>
<script type="text/javascript">
    $(function() {
        $(".auth,.mod_auth_check").on("click", function () {
            var flag = false;
            $(this).parent().find("input").each(function() {
                if ($(this).prop("checked")==true) flag = true;
            });
            $(this).parent().parent().find(".auth_p").prop("checked", flag);
        });
        $(".mod_auth_check").on("click", function () {
            var flag = false;
            $(this).parent().find("input").each(function() {
                if ($(this).prop("checked")==true) flag = true;
            });
            $(this).parent().parent().find(".mod_auth_p").prop("checked", flag);
        });
        $(".auth_p, .mod_auth_p").on("click", function () {
            if ($(this).prop("checked"))
                $(this).parent().parent().find(".mod_auth_check").prop("checked", true);
            else
                $(this).parent().parent().find(".mod_auth_check").prop("checked", false);
        });
        $(".mod_auth_p").on("click", function () {
            if ($(this).prop("checked"))
                $(this).parent().parent().find(".auth").prop("checked", true);
            else
                $(this).parent().parent().find(".auth").prop("checked", false);
        });
        $(".mod-auth").on('click',function () {
            $("#mod-modal").modal(true);
            $("#mod_admin_id").val($(this).attr("attr-id"));
            $("#mod_auth_name").text($.trim($(this).parent().parent().find("td:eq(0)").text()));
            var auth_ids = $(this).parent().find(".admin_auth_ids").val();
            var auth_ids_arr = auth_ids.split(',');
            console.log(auth_ids_arr);
            $(".mod_auth_p,.mod_auth_check").each(function () {
                if (auth_ids_arr.indexOf($(this).val()) >= 0) {
                    $(this).prop("checked", true);
                }
            });
        });
    });
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(".add-btn").on("click", function() {
        $("#add-modal").modal(true);
    });
    $(".add_admin").on('click', function () {
        var admin_name = $("#admin_name").val();
        var login_name = $("#login_name").val();
        var login_pass = $("#login_pass").val();
        var ids = '';
        $(".auth_p, .auth").each(function () {
            if ($(this).prop("checked"))
                ids += $(this).val() + ',';
        });
        if (admin_name == '' || login_name == '' || login_pass == '' || ids == '') {
            alert('请确保填写完整');
            return false;
        }
        $.ajax({
            url: '/admin/auth/addAdmin',
            type:'post',
            data: {'admin_name':admin_name, 'login_name': login_name, 'login_pass': login_pass, 'auth_ids':ids},
            dataType:'json',
            success: function (data) {
                if (data.rs == 1) {
                    alert('修改成功');
                    location.reload();
                } else {
                    alert(data.errmsg);
                    return false;
                }
            }
        });

    });
    $(".disable_btn").on("click", function () {
        var th = $(this);
        $.ajax({
            url: '/admin/auth/disable',
            data: {'admin_id': $(this).attr("attr-id"), 'status': 1},
            dataType:'json',
            type:'get',
            success: function(data) {
                if (data.rs == 1) {
                    th.parent().find(".abled_btn").show();
                    th.hide();
                }
            }
        })
    })
    $(".abled_btn").on("click", function () {
        var th = $(this);
        $.ajax({
            url: '/admin/auth/disable',
            data: {'admin_id': $(this).attr("attr-id"), 'status': 0},
            dataType:'json',
            type:'get',
            success: function(data) {
                if (data.rs == 1) {
                    th.parent().find(".disable_btn").show();
                    th.hide();
                }
            }
        })
    });

    $(".mod_admin").on("click", function () {
        var admin_id = $("#mod_admin_id").val();
        var ids = '';
        $(".mod_auth_p, .mod_auth_check").each(function () {
            if ($(this).prop("checked"))
                ids += $(this).val() + ',';
        });
        if (admin_id == '' || ids == '') {
            alert('请确保填写完整');
            return false;
        }
        $.ajax({
            url: '/admin/auth/updateAdmin',
            type:'post',
            data: {'auth_ids': ids, 'admin_id': admin_id},
            dataType:'json',
            success: function (data) {
                if (data.rs == 1) {
                    alert('修改成功');
                    location.reload();
                } else {
                    alert(data.errmsg);
                    return false;
                }
            }
        });
    });
</script>