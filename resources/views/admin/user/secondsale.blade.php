@include('/admin/header')
@yield('title', '卡券列表页')
@include('/admin/menu')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            奢品寄售列表
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        @if (!empty($records))
                        <table id="example2" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>用户ID</th>
                                <th>姓名</th>
                                <th>价格</th>
                                <th>联系电话</th>
                                <th>详细内容</th>
                                <th>查看详情</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($records as $item)
                                <tr>
                                    <td>{{ $item->uid }}</td>
                                    <td>{{ $item->goods_name }}</td>
                                    <td>{{ $item->goods_price / 100 }}</td>
                                    <td>{{ $item->concat_phone }}</td>
                                    <td>{{ $item->detail }}</td>
                                    <td>
                                        <input type="hidden" class="image" value="{{ rtrim($item->goods_image, ',') }}" />
                                        <button class="look_detail" data-id="{{ $item->id }}">查看</button></td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="7">
                                    {{ $records->links() }}
                                </td>
                            </tr>
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


<div class="modal fade" id="show-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">展示图片</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="cardname" class="col-sm-2 control-label">图片区域</label>
                    <div class="col-sm-10" id="img_show">
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
        $(".look_detail").on("click", function () {
            var images = $(this).parent().find(".image").val();
            if (images == '') return false;
            $("#show-modal").modal(true);
            var html = '';
            var image_url = images.split(',');
            for (var i = 0; i < image_url.length; i++) {
                html += '<img width="100" height="100" src="' + image_url[i] + '">"';
            }
            $("#img_show").empty().append(html);
        });
    });
</script>