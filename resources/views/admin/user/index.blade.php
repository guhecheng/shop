@include('/admin/header')
@yield('title', '卡券列表页')
@include('/admin/menu')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            会员列表
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div style="margin-top:10px; margin-left:10px;">
                        <input type="text" id="user_name" class="form-control" placeholder="用户名或者ID" style="width:200px;float:left;" value="{{ $user_name }}"/>&nbsp;
                        <?php $levels = empty($level_ids) ? [] : explode(',', $level_ids); ?>
                        <button type="button" class="btn {{ in_array(0, $levels) ? 'btn-primary' : 'btn-default' }} btn-level">普通用户 <span>@foreach ($count as $cnt) @if ($cnt->level==0) {{ $cnt->cnt }} @endif @endforeach</span></button>
                        <button type="button" class="btn {{ in_array(1, $levels) ? 'btn-primary' : 'btn-default' }} btn-level">普通会员 <span>@foreach ($count as $cnt) @if ($cnt->level==1) {{ $cnt->cnt }} @endif @endforeach</span></button>
                        <button type="button" class="btn {{ in_array(2, $levels) ? 'btn-primary' : 'btn-default' }} btn-level">黄金会员 <span>@foreach ($count as $cnt) @if ($cnt->level==2) {{ $cnt->cnt }} @endif @endforeach</span></button>
                        <button type="button" class="btn {{ in_array(3, $levels) ? 'btn-primary' : 'btn-default' }} btn-level">铂金会员 <span>@foreach ($count as $cnt) @if ($cnt->level==3) {{ $cnt->cnt }} @endif @endforeach</span></button>
                        <button type="button" class="btn {{ in_array(4, $levels) ? 'btn-primary' : 'btn-default' }} btn-level">钻石会员 <span>@foreach ($count as $cnt) @if ($cnt->level==4) {{ $cnt->cnt }} @endif @endforeach</span></button>

                        <button id="search_btn" class="btn btn-primary">查找</button>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table id="example2" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>用户ID</th>
                                <th>微信名</th>
                                <th>等级</th>
                                <th>手机号码</th>
                                <th>头像</th>
                                <th>余额(元)</th>
                                <th>积分</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->userid }}</td>
                                    <td>{{ $user->uname }}</td>
                                    <td>@if ($user->level == 1)
                                            普通会员
                                        @elseif ($user->level == 2)
                                            黄金会员
                                        @elseif ($user->level == 3)
                                            铂金会员
                                        @elseif ($user->level == 4)
                                            钻石会员
                                        @else
                                            非会员
                                        @endif
                                    </td>
                                    <td>{{ $user->phone }}</td>
                                    <td><img src="{{ $user->avatar }}" width="60" height="60"/></td>
                                    <td>{{ $user->money / 100 }}</td>
                                    <td>{{ $user->score }}</td>
                                    <td>
{{--
                                        <button type="button" class="btn btn-primary modify-btn" attr-id="{{ $user->userid}}">修改</button>
--}}
                                        <a href="/admin/user/info?userid={{ $user->userid }}"><button type="button" class="btn btn-primary">查看</button></a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="7">
                                        {{ $users->appends(['level_ids' => $level_ids])->links() }}
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
<style type="text/css">
    .btn-level span { color: red; }
</style>
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
    $("#level").on("change", function () {
        location.href = '/admin/user?level=' + $(this).val();
    });
    $(".btn-level").on('click', function () {
        if ($(this).hasClass('btn-primary')) {
            $(this).removeClass('btn-primary');
        } else
            $(this).addClass('btn-primary');
    });
    $(function() {
        $(".add-btn").on("click", function() {
            $("#add-modal").modal('show');
        });
        $("#search_btn").on("click",function () {
            var user_name = $.trim($("#user_name").val());
            var level_ids = '';
            $(".btn-level").each(function () {
                if ($(this).hasClass("btn-primary")) {
                    level_ids += ($(this).index() - 1) + ',';
                }
            });
            if (user_name == '')
                location.href = '/admin/user?level_ids=' + level_ids;
            location.href = '/admin/user?user_name=' + user_name + '&level_ids=' + level_ids;
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