@include('/admin/header')
@yield('title', '卡券列表页')
@include('/admin/menu')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            会员信息
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table id="example2" class="table table-bordered table-hover">
                            <tdead>

                            </tdead>
                            <tbody>
                            <tr>
                                <td>微信名</td>
                                <td>{{ $user->uname }}</td>
                            </tr>
                            <tr>
                                <td>等级</td>
                                <td>@if ($user->level == 1)
                                        黄金会员
                                    @elseif ($user->level == 2)
                                        铂金会员
                                    @elseif ($user->level == 3)
                                        钻石会员
                                    @else
                                        非会员
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>手机号码</td>
                                <td>{{ $user->phone }}</td>
                            </tr>
                            <tr>
                                <td>头像</td>
                                <td><img src="{{ $user->avatar }}" widtd="80" height="80" /></td>
                            <tr>
                                <td>余额(元)</td>
                                <td>{{ $user->money / 100 }}</td>
                            </tr>
                            <td>积分</td>
                            <td>{{ $user->score }}</td>
                            </tr>
                            <tr><td colspan="2" align="center">孩子信息</td></tr>
                            @if (!empty($user->child_id))
                            <tr>
                                <td>姓名</td>
                                <td>{{ $child->name }}</td>
                            </tr>
                            <tr>
                                <td>性别</td>
                                <td>{{ $child->sex == 1? '男' : $child->sex == 2?'女':'未知' }}</td>
                            </tr>
                            <tr>
                                <td>学校</td>
                                <td>{{ $child->school }}</td>
                            </tr>
                            <tr>
                                <td>生日</td>
                                <td>{{ $child->birth_date }}</td>
                            </tr>
                            @endif
                            </tbody>
                            <tfoot>
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

<!-- /.modal -->
<!-- /.content-wrapper -->
<footer class="main-footer">
    <div class="pull-right hidden-xs">
    </div>
    <strong>Copyright &copy; 2014-2017</strong> All rights
    reserved.
</footer>

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
<link href="/css/admin/tdemes/explorer/tdeme.css" media="all" rel="stylesheet" type="text/css"/>
<script src="/js/plugins/sortable.js" type="text/javascript"></script>
<script src="/js/fileinput.min.js" type="text/javascript"></script>
<script src="/js/zh.js" type="text/javascript"></script>
<script src="/css/admin/tdemes/explorer/tdeme.js" type="text/javascript"></script>
