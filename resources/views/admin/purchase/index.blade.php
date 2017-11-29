@include('/admin/header')
@include('/admin/menu')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            代购列表
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div style="margin-top:10px; margin-left:10px;">
                        <input type="text" id="user_name" class="form-control" placeholder="用户ID或者商品名" style="width:200px;float:left;" value=""/>
                        <button id="search_btn" class="btn btn-primary">查找</button>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table id="example2" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>用户ID</th>
                                <th>商品名</th>
                                <th>手机号码</th>
                                <th>商品图片</th>
                                <th>是否支付</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($purchases as $purchase)
                            <tr>
                                <td>{{ $purchase->uid }}</td>
                                <td>{{ $purchase->goods_name }}</td>
                                <td>{{ $purchase->phone }}</td>
                                <td>
                                    @if ($purchase->goods_pic)
                                    <img src="{{ $purchase->goods_pic }}" width="60" height="60"/>
                                    @endif
                                </td>
                                <td>
                                    @if ($purchase->is_pay == 3)
                                        已退款
                                    @else
                                        @if ($purchase->is_back)
                                            已允许退款
                                        @else
                                            {{ $purchase->is_pay == 0 ? '否': ($purchase->is_pay == 1 ? '押金已付':'已下单') }}
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if ($purchase->is_pay == 1 && $purchase->is_back == 0)
                                        @if ($purchase->goods_id)
                                        <a href="/admin/purchase/modify?purchase_id={{ $purchase->id }}"><button type="button" class="btn btn-primary">修改</button></a>
                                        @endif
                                    <a href="/admin/purchase/add?purchase_id={{ $purchase->id }}"><button type="button" class="btn btn-primary">增加链接</button></a>
                                    @endif
                                    @if ($purchase->is_create == 0 && $purchase->is_pay == 1 && $purchase->is_back == 0)
                                        <button type="button" class="btn btn-primary no-goods-btn" attr-id="{{ $purchase->id }}">允许退款</button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="7">
                                    {{ $purchases->links() }}
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
        $("#search_btn").on("click",function () {
            var user_name = $.trim($("#user_name").val());
            if (user_name == '')
                location.href = '/admin/purchase';
            location.href = '/admin/purchase?user_name=' + user_name;
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

    $(".no-goods-btn").on("click", function () {
        var attr_id = $(this).attr('attr-id');
        $.ajax({
            url: '/admin/purchase/sureback',
            data:{ 'purchase_id' : attr_id },
            dataType:'get',
            success: function (data) {
                console.log(data);
                if (data.rs == 1) {
                    alert('修改成功');
                    location.reload();
                } else
                    alert('修改失败');
            }
        })
    });
</script>