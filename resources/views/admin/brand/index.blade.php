@include('/admin/header')
@yield('title', '卡券列表页')
@include('/admin/menu')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            品牌列表
        </h1>
        <button type="button" class="btn btn-primary add-btn">添加品牌</button>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">品牌列表</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table id="example2" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>品牌名</th>
                                <th>图片</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody class="type_tbody" id="type_tbody">
                            @if (!$brands->isEmpty())
                            @foreach($brands as $brand)
                                <tr data-id="">
                                    <td>{{ $brand->brand_name }}</td>
                                    <td><img src="{{ $brand->brand_img }}" width="80" height="80"/></td>
                                    <td>
                                        <button type="button" class="btn btn-primary modify-btn" attr-id="{{ $brand->id }}">修改</button>
                                        <button type="button" class="btn btn-primary del-btn" attr-id="{{ $brand->id }}">删除</button>
                                        <a href="/admin/property?typeid={{ $brand->id }}"><button type="button" class="btn btn-primary" attr-id="{{ $brand->id }}">属性操作</button></a>
                                    </td>
                                </tr>
                            @endforeach
                            @endif
                            </tbody>
                            <tfoot>
                            <tr>
                                <td rowspan="3">
                                    {{ $brands->links() }}
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
                <h4 class="modal-title">添加品牌</h4>
            </div>
            <div class="modal-body">
                <form action="/admin/brand/add" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="cardname" class="col-sm-2 control-label">品牌名</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="brand_name" id="brand_name" placeholder="请输入品牌名">
                    </div>
                    <br clear="all" />
                </div>
                <div class="form-group">
                    <label for="cardname" class="col-sm-2 control-label">品牌图片</label>
                    <div class="col-sm-10">
                        <input type="file" name="img" id="img" />
                        <img src="" id="image_show" width="200px" height="100px">
                    </div>
                    <br clear="all" />
                </div>
                <div class="form-group">
                    <label for="cardname" class="col-sm-2 control-label">普通用户折扣</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="common_discount" id="common_discount" placeholder="请输入折扣">
                    </div>
                    <br clear="all" />
                </div>
                <div class="form-group">
                    <label for="cardname" class="col-sm-2 control-label">普通会员折扣</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="ordinary_discount" id="ordinary_discount" placeholder="请输入折扣">
                    </div>
                    <br clear="all" />
                </div>
                <div class="form-group">
                    <label for="cardname" class="col-sm-2 control-label">黄金会员折扣</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="golden_discount" id="golden_discount" placeholder="请输入折扣">
                    </div>
                    <br clear="all" />
                </div>
                <div class="form-group">
                    <label for="cardname" class="col-sm-2 control-label">铂金会员折扣</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="platinum_discount" id="platinum_discount" placeholder="请输入折扣">
                    </div>
                    <br clear="all" />
                </div>
                <div class="form-group">
                    <label for="cardname" class="col-sm-2 control-label">钻石会员折扣</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="diamond_discount" id="diamond_discount" placeholder="请输入折扣">
                    </div>
                    <br clear="all" />
                </div>
                {{ csrf_field() }}
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
                <h4 class="modal-title">添加品牌</h4>
            </div>
            <div class="modal-body">
                <form action="/admin/brand/add" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="cardname" class="col-sm-2 control-label">品牌名</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="brand_name" id="brand_name" placeholder="请输入品牌名">
                        </div>
                        <br clear="all" />
                    </div>
                    <div class="form-group">
                        <label for="cardname" class="col-sm-2 control-label">品牌图片</label>
                        <div class="col-sm-10">
                            <input type="file" name="img" id="img" />
                            <img src="" id="image_show" width="200px" height="100px">
                        </div>
                        <br clear="all" />
                    </div>
                    <div class="form-group">
                        <label for="cardname" class="col-sm-2 control-label">普通用户折扣</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="common_discount" id="common_discount" placeholder="请输入折扣">
                        </div>
                        <br clear="all" />
                    </div>
                    <div class="form-group">
                        <label for="cardname" class="col-sm-2 control-label">普通会员折扣</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="ordinary_discount" id="ordinary_discount" placeholder="请输入折扣">
                        </div>
                        <br clear="all" />
                    </div>
                    <div class="form-group">
                        <label for="cardname" class="col-sm-2 control-label">黄金会员折扣</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="golden_discount" id="golden_discount" placeholder="请输入折扣">
                        </div>
                        <br clear="all" />
                    </div>
                    <div class="form-group">
                        <label for="cardname" class="col-sm-2 control-label">铂金会员折扣</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="platinum_discount" id="platinum_discount" placeholder="请输入折扣">
                        </div>
                        <br clear="all" />
                    </div>
                    <div class="form-group">
                        <label for="cardname" class="col-sm-2 control-label">钻石会员折扣</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="diamond_discount" id="diamond_discount" placeholder="请输入折扣">
                        </div>
                        <br clear="all" />
                    </div>
                {{ csrf_field() }}
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
<script src="/js/jquery.dragsort-0.5.2.min.js"></script>

<script type="text/javascript">
    function getFileUrl(sourceId) {
        var url;
        if (navigator.userAgent.indexOf("MSIE")>=1) { // IE
            url = document.getElementById(sourceId).value;
        } else if(navigator.userAgent.indexOf("Firefox")>0) { // Firefox
            url = window.URL.createObjectURL(document.getElementById(sourceId).files.item(0));
        } else if(navigator.userAgent.indexOf("Chrome")>0) { // Chrome
            url = window.URL.createObjectURL(document.getElementById(sourceId).files.item(0));
        }
        return url;
    }

    /**
     * 将本地图片 显示到浏览器上
     */
    function preImg(sourceId, targetId) {
        var url = getFileUrl(sourceId);
        var imgPre = document.getElementById(targetId);
        imgPre.src = url;
    }
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(function() {
        $("#img").on("change", function () {
            preImg("img", "image_show");
        });
        $(".add-btn").on("click", function() {
            $("#add-modal").modal('show');
        });
        $(document).on("click", ".del-btn", function () {

        });
        $(document).on("click", ".modify-btn", function () {
            var attr_id = $(this).attr("attr-id");
            var par = $(this).parent().parent();
            $("#edit_typename").val($.trim(par.find("td:eq(0)").text()));
            $("#mod-modal").modal('show');
            $("#typeid").val(attr_id);
            $("#item").val(par.index());
        });

        $(document).on("click", ".del-btn", function() {
            if (!confirm('确定删除?')) return ;
            var brand_id = $(this).attr("attr-id");
            if (brand_id == '' || typeof brand_id == 'undefined') return false;
            var that = $(this);
            $.ajax({
                url:'/admin/brand/del',
                data: {'brand_id': brand_id},
                type:"get",
                dataType:'json',
                success: function(data) {
                    if (data.rs == 1)
                        that.parent().parent().remove();
                }
            })
        });

        $("#type_tbody").dragsort({ dragSelector: "tr", dragSelectorExclude: "button", dragBetween: true,
            dragEnd: function() {
                var orders = type_ids = '';
                $("#type_tbody").find("tr").each(function(item,value) {
                    type_ids += $(this).attr("data-id") + ",";
                    orders += (item+1) + ",";
                });
                $.ajax({
                    url: '/admin/type/changeorder',
                    data: {'type_ids':type_ids, 'orders':orders},
                    datatype:'json',
                    type:"post",
                    success:function (data) {

                    }
                });
            }});

    });
    $(".add_type").on("click", function() {
        var typename = $("#typename").val();
        if (typename == '' ) {
            alert('类型名不能为空');
            return false;
        }
        $.ajax({
            url:'/admin/type/add',
            type:'post',
            data: { 'typename': typename},
            dataType: 'json',
            success: function(data) {
                if (data.rs == 1) {
                    var html = "<tr><td>"+typename+"</td>";
                    html += "<td>";
                    html += ' <button type="button" class="btn btn-primary modify-btn" attr-id="'+data.typeid+'">修改</button>';
                    html += '<button type="button" class="btn btn-primary del-btn" attr-id="'+data.typeid+'">删除</button>';
                    html += '<a href="/admin/property?typeid='+data.typeid+'"><button type="button" class="btn btn-primary" attr-id="'+data.typeid+'">属性操作</button></a>';
                    html += "</td></tr>";
                    $(".type_tbody").append(html);
                    $("#add-modal").modal('hide');
                } else {
                    alert("修改失败");
                    return false;
                }
            }
        })
    });
    $(".update_btn").on("click", function() {
        var typename = $("#edit_typename").val();
        var typeid = $("#typeid").val();
        if (typename == '' ) {
            alert('类型名不能为空');
            return false;
        }
        $.ajax({
            url:'/admin/type/modify',
            type:'post',
            data: { 'typename': typename, 'typeid': typeid},
            dataType: 'json',
            success: function(data) {
                if (data.rs == 1) {
                    var item = parseInt($("#item").val());
                    $("tr:eq("+item+")").find("td:eq(0)").text(typename);
                    ("#mod-modal").modal('hide');
                } else {
                    alert("修改失败");
                    return false;
                }
            }
        })
    });
</script>