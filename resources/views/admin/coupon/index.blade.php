@include('/admin/header')
@yield('title', '卡券列表页')
@include('/admin/menu')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            优惠券列表
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <button type="button" class="btn btn-primary add-goods-btn" style="margin-left:10px; margin-top:10px;">添加商品优惠券</button>
                    <button type="button" class="btn btn-primary add-card-btn" style="margin-left:10px; margin-top:10px;">添加充值优惠券</button>
                    <div class="box-header">
                        @if (!empty(session('coupon_info')))
                        <h4 style="color: red;">{{ session('coupon_info') }}</h4>
                        @endif
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
                                <th>使用者</th>
                                <th>使用时间</th>
                                <th>发放者</th>
                            </tr>
                            </thead>
                            <tbody class="type_tbody" id="type_tbody">
                            @if (!$coupons->isEmpty())
                                @foreach($coupons as $coupon)
                                    <tr data-id="{{ $coupon->id }}">
                                        <td>{{ $coupon->id }}</td>
                                        <td>{{ $coupon->create_time }}</td>
                                        <td>满{{ $coupon->goods_price/100 }}减{{ $coupon->discount_price/100 }}</td>
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
                                <td colspan="7">
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
                <h4 class="modal-title">添加优惠券</h4>
            </div>
            <div class="modal-body">
                <form action="/admin/coupon/add" method="post">
                <div class="form-group goods_coupon">
                    <label for="cardname" class="col-sm-2 control-label">类型</label>
                    <div class="col-sm-10">
                        满
                        <input type="text" class="form-control" name="goods_price" id="goods_price" placeholder="请输入金额" style="display: inline;width:150px;">
                        减
                        <input type="text" class="form-control" name="discount_price" id="discount_price" placeholder="请输入金额" style="display: inline;width:150px;">
                    </div>
                    <br clear="all" />
                </div>
                <div class="form-group" id="brand_type">
                    <label for="cardname" class="col-sm-2 control-label">使用范围</label>
                    <div class="col-sm-10">
                        @foreach ($brands as $brand)
                        <input type="checkbox" class="brands" name="brands[]" value="{{ $brand->id }}" />{{ $brand->brand_name }}
                        @endforeach
                    </div>
                    <br clear="all" />
                </div>
                <div class="form-group">
                    <label for="cardname" class="col-sm-2 control-label">购物券类型</label>
                    <div class="col-sm-10">
                        <select id="coupon_type" name="coupon_type">
                            <option value="0">普通优惠券</option>
                            <option value="1">活动优惠券</option>
                            <option value="2">待领优惠券</option>
                            <option value="3">回复优惠券</option>
                        </select>
                        <div id="num_area" style="display: none;float:right;margin-right:100px;">
                            <input type="text" id="add_num" value="" name="add_num" placeholder="输入发券数目"/>
                        </div>
                    </div>
                    <br clear="all" />
                </div>
                <div class="form-group send-content-div" style="display: none;">
                    <label for="content" class="col-sm-2 control-label">回复内容</label>
                    <div class="col-sm-10">
                        <input type="text" name="send_content" value="" placeholder="回复内容" id="send_content" />
                    </div>
                    <br clear="all" />
                </div>
                <div class="form-group">
                    <label for="cardname" class="col-sm-2 control-label">使用时间</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="start_date" id="start_date" placeholder="请输入开始时间" style="display: inline;width:150px;">
                        至
                        <input type="text" class="form-control" name="end_date" id="end_date" placeholder="请输入结束时间" style="display: inline;width:150px;">
                    </div>
                    <br clear="all" />
                </div>
                <input type="hidden" id="type" value="" name="type" />
                <div class="form-group">
                    <label for="cardname" class="col-sm-2 control-label">使用范围</label>
                    <div class="col-sm-10">
                        <div>
                            <input type="checkbox" class="user_type" name="user_type[]" value="0">普通用户
                            <input type="checkbox" class="user_type" name="user_type[]" value="1">普通会员
                            <input type="checkbox" class="user_type" name="user_type[]" value="2">黄金会员
                            <input type="checkbox" class="user_type" name="user_type[]" value="3">铂金会员
                            <input type="checkbox" class="user_type" name="user_type[]" value="4">钻石会员
                        </div>
                        <div>
                            <input type="text" name="user_name" id="user_name" />
                            <button class="btn btn-primary" id="search_btn">搜索</button>
                            <div id="add_user_area">
                                <table>
                                    <tr><td style="width:100px"></td><td style="width:100px;">用户ID</td><td style="width:100px;">用户名</td></tr>
                                </table>
                            </div>
                        </div>
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
<style type="text/css">
    .main-header { z-index: 3 }
    .modal-backdrop {
        z-index: 4;
    }
    .modal { z-index: 8; }
    #add_user_area {
        display: none; }
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
<script src="/js/jquery.dragsort-0.5.2.min.js"></script>
<link href="/css/daterangepicker.css" media="all" rel="stylesheet" type="text/css"/>
<link href="/css/datepicker3.css" media="all" rel="stylesheet" type="text/css"/>
<script src="/js/daterangepicker.js" type="text/javascript"></script>
<script src="/js/bootstrap-datepicker.js" type="text/javascript"></script>
<script type="text/javascript">
    var uids = '';
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
        $(".add-goods-btn").on("click", function() {
            $('#brand_type').show();
            $("#type").val(0);
            $("#goods_price").val('');
            $("#discount_price").val('');
            $("#add-modal").modal('show');
        });
        $(".add-card-btn").on('click', function () {
            $('#brand_type').hide();
            $('#type').val(1);
            $("#goods_price").val('');
            $("#discount_price").val('');
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

    });
    $("#coupon_type").on('click', function () {
        var type = $(this).val();
        console.log(type);
        if (type == 2) {
            $("#num_area").show();
            $(".send-content-div").hide();
            $("#send_content").val('');
        } else if (type == 3) {
            $(".send-content-div").show();
            $("#num_area").hide();
            $("#add_num").val(0);
        } else {
            $("#send_content").val('');
            $(".send-content-div").hide();
            $("#num_area").hide();
            $("#add_num").val(0);
        }
    });
    $("#search_btn").on("click", function (event) {
        event.preventDefault();
        var user_name = $.trim($("#user_name").val());
        if (user_name == '') return false;
        $.ajax({
            url:'/admin/coupon/finduser',
            type:'post',
            data: { 'user_name': user_name },
            dataType: 'json',
            success: function (data) {
                console.log(data);
                if (data.users != '') {
                    $("#add_user_area").show();
                    var html = '';
                    for (var i in data.users) {
                        var user = data.users[i];
                        console.log(uids);
                        if (uids != '') {
                            var ids_arr = uids.split(',');
                            console.log(ids_arr);
                            console.log(user.userid);
                            if (ids_arr.indexOf(""+user.userid+"") >= 0) {
                                continue;
                            }
                        }

                        uids += user.userid + ',';
                        console.log(user);
                        html += '<tr>';
                        html += '<td><input type="checkbox" name="name[]" value="'+user.userid+'" style="width:20px;height:20px;"></td>';
                        html += '<td><span>'+user.userid+'</span></td>';
                        html += '<td><span>'+user.uname+'</span></td>';
                        html += '</tr>';
                    }
                    $("#add_user_area").find("table").append(html);
                }
            }
        })
    });
</script>