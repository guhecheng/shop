@include('/admin/header')
@yield('title', '卡券列表页')
@include('/admin/menu')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            商品列表
        </h1>
        <a href="/admin/goods/add"><button type="button" class="btn btn-primary add-btn">添加</button></a>
        <input type="text" name="goods_name" id="goods_name" placeholder="输入商品名称" value="{{ $goods_name }}"/>
        <button id="search_btn">搜索</button>
        <button id="search_all">查看所有</button>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">商品列表</h3>
                        <div id="select_brand">
                            @if (!empty($brand))
                                <select id="brand" name="brand">
                                    <option value="0">所有品牌</option>
                                    @foreach ($brand as $item)
                                        <option value="{{ $item->id }}" {{ $brand_id == $item->id ? 'selected':'' }}>
                                            {{ $item->brand_name }}
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table id="example2" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th><input type="checkbox" name="select_goods" id="batch_select_goods" data-type="0" /></th>
                                <th>商品名</th>
                                <th>图标</th>
                                <th>基本价</th>
                                <th>是否推荐</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($goods as $good)
                                <tr>
                                    <td><input type="checkbox" name="select_goods" class="select_goods" value="{{ $good->goodsid }}"/></td>
                                    <td>{{ $good->goodsname }}</td>
                                    <td><img src="{{ $good->goodsicon }}" width="100" height="100"/></td>
                                    <td>{{ $good->price / 100 }}</td>
                                    <td>{{ empty($good->is_hot) ? '否' : '是' }}</td>
                                    <td>
                                        <a href="/admin/goods/edit?goods_id={{ $good->goodsid }}"><button type="button" class="btn btn-primary modify-btn" attr-id="{{ $good->goodsid}}">修改</button></a>
                                        <button type="button" class="btn btn-primary del-btn" attr-id="{{ $good->goodsid }}">删除</button>
                                        <button type="button" class="btn btn-primary mod-sale" attr-id="{{ $good->goodsid }}" attr-value="{{ empty($good->is_sale) ? 0 : 1}}">{{ empty($good->is_sale) ? '上架' : '下架' }}</button>
                                        <button type="button" class="btn btn-primary mod-a" attr-id="{{ $good->goodsid }}" attr-value="{{ empty($good->is_ad) ? 1 : 0}}">{{ empty($good->is_ad) ? '上广告' : '下广告' }}</button>
                                        <button type="button" class="btn btn-primary mod-hot" attr-id="{{ $good->goodsid }}" attr-value="{{ empty($good->is_hot) ? 1 : 0}}">{{ empty($good->is_hot) ? '推荐' : '取消推荐' }}</button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="6">
                                    <div>
                                    <button id="batch_on">批量上架</button>
                                    <button id="batch_off">批量下架</button>
                                    </div>
                                    <div>
                                    {{ $goods->appends(['brand_id' => $brand_id])->links() }}
                                    </div>
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

<!-- /.content-wrapper -->
<footer class="main-footer">
    <div class="pull-right hidden-xs">
    </div>
    <strong>Copyright &copy; 2014-2017</strong> All rights
    reserved.
</footer>

<div class="control-sidebar-bg"></div>
</div>
<style type="text/css">
    .select_goods, #batch_select_goods { width:18px; height:18px; }
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
    $("#search_btn").on("click", function () {
        var goods_name = $("#goods_name").val();
        if (goods_name == '') return ;
        location.href = '/admin/goods?goods_name=' + goods_name;
    });
    $("#search_all").on("click", function () {
        location.href = '/admin/goods';
    });
    $("#batch_select_goods").on("click", function () {
        var data_type = $(this).attr('data-type');
        $(".select_goods").prop("checked", data_type == 0 ? true : false);
        $(this).attr('data-type', 1 - parseInt(data_type));
    });
    $("#batch_on").on('click', function () {
        var goods_ids = '';
        $(".select_goods").each(function () {
            console.log($(this).prop('checked'));
            if ($(this).prop('checked')) goods_ids += $(this).val() + ',';
        });
        if (goods_ids == '') return false;
        batch_act_goods(goods_ids, 1);
    });
    $("#batch_off").on('click', function () {
        var goods_ids = '';
        $(".select_goods").each(function () {
            console.log($(this).prop('checked'));
            if ($(this).prop('checked')) goods_ids += $(this).val() + ',';
        });
        if (goods_ids == '') return false;
        batch_act_goods(goods_ids, 0);
    });
    function batch_act_goods(goods_ids, act_type) {
        $.ajax({
            url:'/admin/goods/batchAct',
            data: { 'goods_ids' : goods_ids, 'act_type' : act_type },
            dataType: 'json',
            type:'post',
            success: function (data) {
                if (data.rs == 1) {
                    alert(data.errmsg);
                    return false;
                }
                alert('操作成功');
                location.reload();
            }
        });
    }
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

        $(".del-btn").on("click", function() {
            if (!confirm('确定删除?')) return ;
            var goodsid = $(this).attr("attr-id");
            if (goodsid == '' || typeof goodsid == 'undefined') return false;
            var that = $(this);

            $.ajax({
                url:'/admin/goods/delete',
                data: {'goodsid': goodsid},
                type:"get",
                dataType:'json',
                success: function(data) {
                    if (data.rs == 1)
                        that.parent().parent().remove();
                }
            })
        });
    });
    $(".mod-sale").on("click", function() {
        var goodsid = $(this).attr("attr-id");
        var status = $(this).attr("attr-value");
        if (goodsid == '' || typeof goodsid == 'undefined') return false;
        var that = $(this);
        $.ajax({
            url:'/admin/goods/changesale',
            data: {'goodsid': goodsid, 'status' : status},
            type:"get",
            dataType:'json',
            success: function(data) {
                if (data.rs == 1)
                    that.attr('attr-value', 1 - parseInt(status)).text(status == 0 ? '下架' : '上架');
            }
        })
    });
    $(".mod-hot").on("click", function() {
        var goodsid = $(this).attr("attr-id");
        var status = $(this).attr("attr-value");
        if (goodsid == '' || typeof goodsid == 'undefined') return false;
        var that = $(this);
        $.ajax({
            url:'/admin/goods/changehot',
            data: {'goodsid': goodsid, 'status' : status},
            type:"get",
            dataType:'json',
            success: function(data) {
                if (data.rs == 1)
                    that.attr('attr-value', 1 - parseInt(status)).text(status == 0 ? '推荐' : '取消推荐');
            }
        })
    });
    $(".mod-a").on("click", function() {
        var goodsid = $(this).attr("attr-id");
        var status = $(this).attr("attr-value");
        if (goodsid == '' || typeof goodsid == 'undefined') return false;
        var that = $(this);
        $.ajax({
            url:'/admin/goods/changead',
            data: {'goodsid': goodsid, 'status' : status},
            type:"get",
            dataType:'json',
            success: function(data) {
                if (data.rs == 1)
                    that.attr('attr-value', 1 - parseInt(status)).text(status == 0 ? '上广告' : '下广告');
            }
        })
    });
    $("#brand").on("change", function () {
            location.href = "/admin/goods?brand_id=" + $(this).val();
    });
</script>