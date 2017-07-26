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
                            <tr>
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
                        @if (!empty($user->last_login_time))
                        <h5>用户休眠时间: {{ number_format((time()-strtotime($user->last_login_time)) / 3600 / 24) }}
                            <?php
                                $last_login_time = strotime($user->last_login_time);
                                $day = intval((time() - $last_login_time) / 3600 / 24);
                                $hour = (time() - $last_login_time) / 3600 % 24;
                                ?>
                            <?php echo $day; ?>天<?php echo $hour; ?>时
                        </h5>
                        @endif
                        <table  class="table table-bordered table-hover">
                            <tr>
                                <td align="center">备注</td>
                            </tr>
                            <tr>
                                <td align="center"><textarea name="remark" id="remark" style="width:60%;height:100px;">{{ $user->remark }}</textarea></td>
                            </tr>
                        </table>
                        <div style="text-align: center;line-height: 40px;">消费记录</div>
                        @if (!$orders->isEmpty())
                        <table id="example3" class="table table-bordered table-hover">
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
                            </tr>
                            </thead>
                            <tbody>
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
                            </tr>
                            @if ($order_no != $order->order_no)
                            <?php $order_no = $order->order_no; ?>
                            @endif
                            @endforeach
                            </tbody>
                            <tfoot>
                            </tfoot>
                        </table>
                        @endif
                        <div style="text-align: center;line-height: 40px;">积分记录</div>
                        @if (!$scores->isEmpty())
                        <table id="example3" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>积分</th>
                                <th>积分类型</th>
                                <th>兑换时间</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($scores as $score)
                            <tr>
                                <td>{{ $score->type == 2 ? '-':'' }}{{  $score->score }}分</td>
                                <td><?php echo $score->type==0? '购买商品' : $score->type==1? '会员卡充值' : '兑换商品'; ?></td>
                                <td>{{ $score->create_time }}</td>
                            </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            </tfoot>
                        </table>
                        @endif
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
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $("#remark").on("blur", function () {
        if ($.trim($(this).val()) == '') return false;
        $.ajax({
            type:'post',
            url:'/admin/user/addremark',
            data: {'userid':{{ $userid }},'remark': $.trim($(this).val())},
            dataType:'json',
            success:function (data) {

            }
        });
    });
</script>
