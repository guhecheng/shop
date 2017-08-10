@include('/admin/header')
@yield('title', '卡券列表页')
@include('/admin/menu')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            优惠券列表
        </h1>
        <button type="button" class="btn btn-primary add-btn">添加优惠券</button>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">优惠券列表</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table id="example2" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>序号</th>
                                <th>发放时间</th>
                                <th>类型</th>
                                <th>使用范围</th>
                                <th>使用时间</th>
                                <th>使用者</th>
                                <th>发放者</th>
                            </tr>
                            </thead>
                            <tbody class="type_tbody" id="type_tbody">
                            @if (!$coupons->isEmpty())
                                @foreach($coupons as $coupon)
                                    <tr data-id="{{ $coupon->id }}">
                                        <td>{{ $coupon->id }}</td>
                                        <td>{{ $coupon->create_time }}</td>
                                        <td>满{{ $coupon->goods_price }}减{{ $coupon->discount_price }}</td>
                                        <td>
                                            <?php
                                                if (!empty($coupon->brand)) {
                                                    $coupon_brand = explode(',', $coupon->brand . ',');
                                                    foreach ($brands as $brand) {
                                                        foreach ($coupon_brand as $item) {
                                                            if ($item == $brand->id)
                                                                echo $brand->brand_name, "&nbsp;&nbsp;";
                                                        }
                                                    }
                                                }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                                $user_type = explode(',', $coupon->user_type);
                                                foreach ($type as $key=>$item) {
                                                    foreach ($user_type as $value)
                                                        if ($value == $key)
                                                            echo $item, "&nbsp;&nbsp;";
                                                }
                                            ?>
                                        </td>
                                        <td>{{ $coupon->start_date }}至{{ $coupon->end_date }}</td>
                                        <td>{{ $coupon->name }}</td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                            <tfoot>
                            <tr>
                                <td rowspan="6d">
                                    {{ $coupons->links() }}
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
                <form action="/admin/coupon/add" method="post">
                <div class="form-group">
                    <label for="cardname" class="col-sm-2 control-label">类型</label>
                    <div class="col-sm-10">
                        满
                        <input type="text" class="form-control" name="goods_price" id="goods_price" placeholder="请输入金额">
                        减
                        <input type="text" class="form-control" name="discount_price" id="discount_price" placeholder="请输入金额">
                    </div>
                    <br clear="all" />
                </div>
                <div class="form-group">
                    <label for="cardname" class="col-sm-2 control-label">使用范围</label>
                    <div class="col-sm-10">
                        @foreach ($brands as $brand)
                        <input type="checkbox" class="brands" name="brands[]" value="{{ $brand->id }}" />{{ $brand->brand_name }}
                        @endforeach
                    </div>
                    <br clear="all" />
                </div>
                <div class="form-group">
                    <label for="cardname" class="col-sm-2 control-label">使用时间</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="start_date" id="start_date" placeholder="请输入开始时间">
                        至
                        <input type="text" class="form-control" name="end_date" id="end_date" placeholder="请输入结束时间">
                    </div>
                    <br clear="all" />
                </div>
                <div class="form-group">
                    <label for="cardname" class="col-sm-2 control-label">使用范围</label>
                    <div class="col-sm-10">
                        <input type="checkbox" class="user_type" name="user_type[]" value="0">普通用户
                        <input type="checkbox" class="user_type" name="user_type[]" value="1">普通会员
                        <input type="checkbox" class="user_type" name="user_type[]" value="2">黄金会员
                        <input type="checkbox" class="user_type" name="user_type[]" value="3">铂金会员
                        <input type="checkbox" class="user_type" name="user_type[]" value="4">钻石会员
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
                <h4 class="modal-title">编辑种类</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="cardname" class="col-sm-2 control-label">种类名</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="typename" id="edit_typename" placeholder="请输入种类名">
                    </div>
                </div>
                <input type="hidden" id="typeid" value="" name="typeid" />
                <input type="hidden" id="item" value="" name="item" />
                {{ csrf_field() }}
                <br clear="all" />
            </div>
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
<script src="/js/jquery.dragsort-0.5.2.min.js"></script>
<link href="/css/daterangepicker.css" media="all" rel="stylesheet" type="text/css"/>
<link href="/css/datepicker3.css" media="all" rel="stylesheet" type="text/css"/>
<script src="/js/daterangepicker.js" type="text/javascript"></script>
<script src="/js/bootstrap-datepicker.js" type="text/javascript"></script>
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(function() {
        $('#start_date').datepicker({
            autoclose: true
        });
        $('#end_date').datepicker({
            autoclose: true
        });
        $(".add-btn").on("click", function() {
            $("#add-modal").modal('show');
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
            var typeid = $(this).attr("attr-id");
            if (typeid == '' || typeof typeid == 'undefined') return false;
            var that = $(this);

            $.ajax({
                url:'/admin/type/delete',
                data: {'typeid': typeid},
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