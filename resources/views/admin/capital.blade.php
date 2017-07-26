@include('/admin/header')
@yield('title', '卡券列表页')
@include('/admin/menu')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            交易流水
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">流水列表</h3>
                    </div>
                    <div>
                        <input type="text" class="form-control" id="search_no" placeholder="订单号" value="{{ $search_no }}" />
                        <input type="text" class="form-control" id="search_name" placeholder="用户名"  value="{{ $search_name }}"/>
                        <div style="float:left;">时间:</div>
                        <input type="text" class="form-control" name="start_date" id="start_date" value="{{ $start_date }}" style="display:inline;width:140px;margin-right:10px;float: left;">
                        <input type="text" class="form-control" name="end_date" id="end_date" value="{{ $end_date }}" style="display:inline;width:140px;float: left;">
                        <input type="submit" class="btn btn-primary" id="search_btn" value="查找" />
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table id="example2" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>类型</th>
                                <th>订单号</th>
                                <th>用户名</th>
                                <th>金额(元)</th>
                                <th>时间</th>
                            </tr>
                            </thead>
                            <tbody class="content-body">
                            @foreach($data as $item)
                                <tr>
                                    <td>{{ $item->trans_type ? '订单' : '充值' }}</td>
                                    <td>{{ $item->order_no }}</td>
                                    <td>{{ $item->uname }}</td>
                                    <td>{{ $item->trans_money / 100 }}</td>
                                    <td>{{ $item->create_time }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <td rowspan="4">
                                    {{ $data->links() }}
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>

<link href="/css/fileinput.min.css" media="all" rel="stylesheet" type="text/css"/>
<link href="/css/admin/themes/explorer/theme.css" media="all" rel="stylesheet" type="text/css"/>
<script src="/js/plugins/sortable.js" type="text/javascript"></script>
<script src="/js/fileinput.min.js" type="text/javascript"></script>
<script src="/js/zh.js" type="text/javascript"></script>
<script src="/css/admin/themes/explorer/theme.js" type="text/javascript"></script>
<link href="/css/daterangepicker.css" media="all" rel="stylesheet" type="text/css"/>
<link href="/css/datepicker3.css" media="all" rel="stylesheet" type="text/css"/>
<script src="/js/daterangepicker.js" type="text/javascript"></script>
<script src="/js/bootstrap-datepicker.js" type="text/javascript"></script>
<style type="text/css">
    #search_no, #search_name {
        width: 200px;
        float: left;
        margin-right:20px;
    }
</style>
<script type="text/javascript">
    $(function () {
        $('#start_date').datepicker({
            autoclose: true
        });
        $('#end_date').datepicker({
            autoclose: true
        });
    });
    $("#search_btn").on("click", function () {
        var name = $("#search_name").val();
        var no = $("#search_no").val();
        var start_date = $("#start_date").val();
        var end_date = $("#end_date").val();
   /*     if (name == '' && no == '') {
            return false;
        }*/
        location.href = "/admin/capital?search_no=" + no + "&search_name=" +name+"&start_date=" + start_date + "&end_date=" + end_date;
    });
</script>