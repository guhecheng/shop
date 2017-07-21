@include('/admin/header')
@yield('title', '卡券列表页')
@include('/admin/menu')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            订单列表
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">订单列表</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <ul class="nav navbar-nav">
                            <li>
                                <a href="/admin/order?status=2" style="{{ $status == 2 ? 'color:red' : '' }}">未发货</a>
                            </li>
                            <li>
                                <a href="/admin/order?status=3" style="{{ $status == 3 ? 'color:red' : '' }}">已发货</a>
                            </li>
                        </ul>
                        <a href="/admin/order/export?status={{ $status }}"><button class="btn btn-primary" style="margin-left: 2rem;">导出</button></a>
                        <table id="example2" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>订单编号</th>
                                <th>物品名</th>
                                <th>物品属性</th>
                                <th>购买数量</th>
                                <th>支付金额</th>
                                <th>收货人</th>
                                <th>联系方式</th>
                                <th>联系地址</th>
                                <th>支付时间</th>
                                <th>支付总额</th>
                                @if ($status == 2)
                                <th>操作</th>
                                @elseif ($status == 3)
                                <th>快递信息</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                            @if (!empty($orders))
                                <?php $order_no = ''; ?>
                                @foreach($orders as $order)
                                    <tr>
                                        @if ($order_no != $order->order_no)
                                        <td rowspan="{{ $order->times }}">
                                            {{ $order->order_no }}
                                        </td>
                                        @endif
                                        <td>
                                            {{ $order->goodsname }}
                                        </td>
                                        <td>{{ $order->property }}</td>
                                        <td>{{ $order->count }}</td>
                                        <td>{{ $order->per_price * $order->count / 100 }}</td>
                                        @if ($order_no != $order->order_no)
                                        <td rowspan="{{ $order->times }}">
                                            {{ $order->recv_name }}
                                        </td>
                                        <td rowspan="{{ $order->times }}">
                                            {{ $order->phone }}
                                        </td>
                                        <td rowspan="{{ $order->times }}">
                                            {{ $order->location }}
                                        </td>
                                        <td rowspan="{{ $order->times }}">
                                            {{ $order->pay_time }}
                                        </td>
                                        @endif
                                        @if ($order_no != $order->order_no)
                                        <td rowspan="{{ $order->times }}">
                                            {{ $order->price / 100}}
                                        </td>
                                        @endif
                                        @if ($order_no != $order->order_no)
                                        <td rowspan="{{ $order->times }}" >
                                            @if ($order->status == 2)
                                                <button class="btn btn-primary send-goods" attr-id="{{ $order->order_no }}">发货</button>
                                            @elseif ($order->status == 3)
                                                {{ $order->express_company }}<br />{{ $order->express_no }}
                                            @endif
                                        </td>
                                        @endif
                                    </tr>
                                    @if ($order_no != $order->order_no)
                                        <?php $order_no = $order->order_no; ?>
                                    @endif
                                @endforeach
                            @endif
                            </tbody>
                            <tfoot>
                            <tr>
                                <td rowspan="12">
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
                <h4 class="modal-title">添加快递</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="cardname" class="col-sm-2 control-label">快递单号</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="express_no" id="express_no" placeholder="请输入快递单号">
                    </div>
                </div>
                <div class="form-group">
                    <label for="cardname" class="col-sm-2 control-label">快递公司</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="express_company" id="express_company" placeholder="请输入快递公司">
                    </div>
                </div>
                <br clear="all" />
            </div>
            <input type="hidden" id="add_order_no" name="add_order_no" />
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <input type="submit" class="btn btn-primary add_btn" value="添加" />
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

<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(function() {
        $(".send-goods").on("click", function() {
            $("#add-modal").modal('show');
            $("#add_order_no").val($(this).attr('attr-id'));
        });
    });
    $(".add_btn").on("click", function() {
        var add_order_no = $("#add_order_no").val();
        var express_no = $('#express_no').val();
        var express_company = $("#express_company").val();
        if (express_company == '' || express_no == '') {
            alert('信息填写不全');
            return false;
        }
        $.ajax({
            type:'post',
            url: '/admin/order/send',
            data: { 'express_company': express_company, 'express_no': express_no, 'order_no':add_order_no},
            dataType: 'json',
            success: function (data) {
                console.log(data);
                if (data.rs == 1) {
                    alert('发货成功');
                    location.reload();
                } else {
                    alert('发货失败,请重新操作');
                    return false;
                }
            }
        })
    });
</script>