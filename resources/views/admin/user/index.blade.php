@include('/admin/header')
@yield('title', '卡券列表页')
@include('/admin/menu')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            会员列表
        </h1>
        <button type="button" class="btn btn-primary add-btn">添加</button>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">会员卡列表</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table id="example2" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>微信名</th>
                                <th>等级</th>
                                <th>手机号码</th>
                                <th>头像</th>
                                <th>余额(元)</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->uname }}</td>
                                    <td>@if ($user->level == 1)
                                            黄金会员
                                        @elseif ($user->level == 2)
                                            铂金会员
                                        @elseif ($user->level == 3)
                                            钻石会员
                                        @else
                                        @endif
                                    </td>
                                    <td>{{ $user->phone }}</td>
                                    <td><img src="{{ $user->icon }}" width="60" height="60"/></td>
                                    <td>{{ $user->money / 100 }}</td>
                                    <td>
                                        <button type="button" class="btn btn-primary modify-btn" attr-id="{{ $user->userid}}">修改</button>
                                        <button type="button" class="btn btn-primary del-btn" attr-id="{{ $user->userid }}">删除</button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td rowspan="4">
                                        {{ $users->links() }}
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
                <h4 class="modal-title">添加卡片</h4>
            </div>
            <form action="/admin/card" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="cardname" class="col-sm-2 control-label">卡片名</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="cardname" id="cardname" placeholder="请输入卡片名">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="cardname" class="col-sm-2 control-label">卡片积分</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="cardscore" id="cardscore" placeholder="请输入积分">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="cardname" class="col-sm-2 control-label">卡片图片</label>
                        <div class="col-sm-10">
                            <input id="add_img" type="file" name="add_img" class="file" data-preview-file-type="text" >
                        </div>
                    </div>
                    {{ csrf_field() }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <input type="submit" class="btn btn-primary add_card" value="添加" />
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
                <h4 class="modal-title">编辑卡片</h4>
            </div>
            <form enctype="multipart/form-data" method="post" action="/admin/card/1">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="cardname" class="col-sm-2 control-label">卡片名</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="cardname" id="edit_cardname" placeholder="请输入卡片名">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="cardname" class="col-sm-2 control-label">卡片积分</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="cardscore" id="edit_cardscore" placeholder="请输入积分">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="cardname" class="col-sm-2 control-label">卡片图片</label>
                        <div class="col-sm-10">
                            <input id="update_img" type="file" name="update_img" class="file" data-preview-file-type="text" >
                        </div>
                    </div>
                    {{ method_field('PUT') }}
                    {{ csrf_field() }}
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

<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
    <!-- Create the tabs -->
    <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
        <li><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-home"></i></a></li>
        <li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>
    </ul>
    <!-- Tab panes -->
    <div class="tab-content">
        <!-- Home tab content -->
        <div class="tab-pane" id="control-sidebar-home-tab">
            <h3 class="control-sidebar-heading">Recent Activity</h3>
            <ul class="control-sidebar-menu">
                <li>
                    <a href="javascript:void(0)">
                        <i class="menu-icon fa fa-birthday-cake bg-red"></i>

                        <div class="menu-info">
                            <h4 class="control-sidebar-subheading">Langdon's Birthday</h4>

                            <p>Will be 23 on April 24th</p>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)">
                        <i class="menu-icon fa fa-user bg-yellow"></i>

                        <div class="menu-info">
                            <h4 class="control-sidebar-subheading">Frodo Updated His Profile</h4>

                            <p>New phone +1(800)555-1234</p>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)">
                        <i class="menu-icon fa fa-envelope-o bg-light-blue"></i>

                        <div class="menu-info">
                            <h4 class="control-sidebar-subheading">Nora Joined Mailing List</h4>

                            <p>nora@example.com</p>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)">
                        <i class="menu-icon fa fa-file-code-o bg-green"></i>

                        <div class="menu-info">
                            <h4 class="control-sidebar-subheading">Cron Job 254 Executed</h4>

                            <p>Execution time 5 seconds</p>
                        </div>
                    </a>
                </li>
            </ul>
            <!-- /.control-sidebar-menu -->

            <h3 class="control-sidebar-heading">Tasks Progress</h3>
            <ul class="control-sidebar-menu">
                <li>
                    <a href="javascript:void(0)">
                        <h4 class="control-sidebar-subheading">
                            Custom Template Design
                            <span class="label label-danger pull-right">70%</span>
                        </h4>

                        <div class="progress progress-xxs">
                            <div class="progress-bar progress-bar-danger" style="width: 70%"></div>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)">
                        <h4 class="control-sidebar-subheading">
                            Update Resume
                            <span class="label label-success pull-right">95%</span>
                        </h4>

                        <div class="progress progress-xxs">
                            <div class="progress-bar progress-bar-success" style="width: 95%"></div>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)">
                        <h4 class="control-sidebar-subheading">
                            Laravel Integration
                            <span class="label label-warning pull-right">50%</span>
                        </h4>

                        <div class="progress progress-xxs">
                            <div class="progress-bar progress-bar-warning" style="width: 50%"></div>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)">
                        <h4 class="control-sidebar-subheading">
                            Back End Framework
                            <span class="label label-primary pull-right">68%</span>
                        </h4>

                        <div class="progress progress-xxs">
                            <div class="progress-bar progress-bar-primary" style="width: 68%"></div>
                        </div>
                    </a>
                </li>
            </ul>
            <!-- /.control-sidebar-menu -->

        </div>
        <!-- /.tab-pane -->
        <!-- Stats tab content -->
        <div class="tab-pane" id="control-sidebar-stats-tab">Stats Tab Content</div>
        <!-- /.tab-pane -->
        <!-- Settings tab content -->
        <div class="tab-pane" id="control-sidebar-settings-tab">
            <form method="post">
                <h3 class="control-sidebar-heading">General Settings</h3>

                <div class="form-group">
                    <label class="control-sidebar-subheading">
                        Report panel usage
                        <input type="checkbox" class="pull-right" checked>
                    </label>

                    <p>
                        Some information about this general settings option
                    </p>
                </div>
                <!-- /.form-group -->

                <div class="form-group">
                    <label class="control-sidebar-subheading">
                        Allow mail redirect
                        <input type="checkbox" class="pull-right" checked>
                    </label>

                    <p>
                        Other sets of options are available
                    </p>
                </div>
                <!-- /.form-group -->

                <div class="form-group">
                    <label class="control-sidebar-subheading">
                        Expose author name in posts
                        <input type="checkbox" class="pull-right" checked>
                    </label>

                    <p>
                        Allow the user to show his name in blog posts
                    </p>
                </div>
                <!-- /.form-group -->

                <h3 class="control-sidebar-heading">Chat Settings</h3>

                <div class="form-group">
                    <label class="control-sidebar-subheading">
                        Show me as online
                        <input type="checkbox" class="pull-right" checked>
                    </label>
                </div>
                <!-- /.form-group -->

                <div class="form-group">
                    <label class="control-sidebar-subheading">
                        Turn off notifications
                        <input type="checkbox" class="pull-right">
                    </label>
                </div>
                <!-- /.form-group -->

                <div class="form-group">
                    <label class="control-sidebar-subheading">
                        Delete chat history
                        <a href="javascript:void(0)" class="text-red pull-right"><i class="fa fa-trash-o"></i></a>
                    </label>
                </div>
                <!-- /.form-group -->
            </form>
        </div>
        <!-- /.tab-pane -->
    </div>
</aside>
<!-- /.control-sidebar -->
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
    $(function() {
        $(".add-btn").on("click", function() {
            $("#add-modal").modal('show');
        });
        //initFileInput("add_img", "/admin/card/upload");
        $("#add_img").fileinput({
            language: 'zh', //设置语言
            uploadUrl: '/admin/card/upload', //上传的地址
            allowedFileExtensions : ['jpg', 'png','gif'],//接收的文件后缀
            showUpload: false, //是否显示上传按钮
            showCaption: false,//是否显示标题
            browseClass: "btn btn-primary", //按钮样式
            previewFileIcon: "<i class='glyphicon glyphicon-king'></i>",
        });
        $(".modify-btn").on("click", function () {
            var attr_id = $(this).prop("attr-id");
            var par = $(this).parent().parent();
            $("#edit_cardname").val($.trim(par.find("td:eq(0)").text()));
            $("#edit_cardscore").val($.trim(par.find("td:eq(1)").text()));
            $("#mod-modal").modal('show');
            $("#address_id").val(attr_id);
            $(this).find("form").prop("action", "/admin/card/" + attr_id);
            $("#update_img").fileinput({
                showUpload: false,
                showCaption: false,
                browseClass: "btn btn-primary btn-lg",
                fileType: "any",
                previewFileIcon: "<i class='glyphicon glyphicon-king'></i>",
                overwriteInitial: false,
                initialPreviewAsData: true,
                initialPreview: [
                    "http://lorempixel.com/1920/1080/transport/1"
                ],
                initialPreviewConfig: [
                    {caption: "transport-1.jpg", size: 329892, width: "120px", url: "{$url}", key: 1},
                ]
            });
        });

        $(".del-btn").on("click", function() {
            if (!confirm('确定删除?')) return ;
            var uid = $(this).attr("attr-id");
            if (uid == '' || typeof uid == 'undefined') return false;
            var that = $(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url:'/admin/user/delete',
                data: {'uid': uid},
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