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
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">商品列表</h3>
                    </div>
                    <!-- /.box-header -->
                    <form enctype="multipart/form-data" method="post" role="form" action="/admin/goods/create">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="goodsname" class="col-sm-2 control-label">商品名</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="goodsname" name="goodsname" placeholder="请输入名字">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="price" class="col-sm-2 control-label">基本价</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="goodsprice" name="goodsprice" placeholder="请输入价格（元)">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="price" class="col-sm-2 control-label">封面</label>
                            <div class="col-sm-10" id="logo_append_area">
                                <div  class="logo_upload" style="width:80px;height: 80px;border:solid 1px #CCCCCC;position:relative;">
                                    <input type="file" class="img_logo" style="opacity: 0;width:78px;height:78px;">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="price" class="col-sm-2 control-label">介绍图片</label>
                            <div class="col-sm-10" id="img_append_area">
                                <div  class="img_upload">
                                    <input type="file" class="img" style="opacity: 0;width:78px;height:78px;">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="lastname" class="col-sm-2 control-label">是否推荐</label>
                            <div class="col-sm-10">
                                <input type="radio" name="is_hot" value="0" />否
                                <input type="radio" name="is_hot" value="1" />是
                            </div>
                            <br clear="all" />
                        </div>
                        <div class="form-group">
                            <label for="lastname" class="col-sm-2 control-label">选择类目</label>
                            <div class="col-sm-10">
                                <select id="goodstype" name="goodstype">
                                    <option value="0">请选择类目</option>
                                    @foreach ($types as $type)
                                        <option value="{{ $type->typeid }}">{{ $type->typename }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                        <table id="example2" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>属性名</th>
                                <th>属性值</th>
                            </tr>
                            </thead>
                            <tbody id="show_property">

                            </tbody>
                            <tfoot class="add_foot">

                            </tfoot>
                        </table>
                        </div>
                        <div class="form-group">
                            <table id="sku_table" class="table table-bordered table-hover" style="display: none;">
                                <thead>
                                <tr class="table-title">
                                </tr>
                                </thead>
                                <tbody id="show_sku">

                                </tbody>
                            </table>
                        </div>
                        <div class="form-group">
                            @include('UEditor::head')
                            <script id="container" name="content" type="text/plain"></script>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button id="create_sku" class="btn btn-default">添加对应库存</button>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button id="create_sku" class="btn btn-default">添加商品</button>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    {{ csrf_field() }}
                    <input type="hidden" id="logo" name="logo" value="" />
                    <input type="hidden" id="imglist" name="imglist" value="" />
                    <input type="hidden" id="len" name="len" value="" />
                </div>
                </form>
                <!-- /.box -->

            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>

<input type="hidden" id="attr_key_ids" value="" />


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

{{--<link href="/css/fileinput.min.css" media="all" rel="stylesheet" type="text/css"/>
<link href="/css/admin/themes/explorer/theme.css" media="all" rel="stylesheet" type="text/css"/>
<script src="/js/plugins/sortable.js" type="text/javascript"></script>
<script src="/js/fileinput.min.js" type="text/javascript"></script>
<script src="/js/zh.js" type="text/javascript"></script>
<script src="/css/admin/themes/explorer/theme.js" type="text/javascript"></script>--}}
<style type="text/css">
    .img_upload,.logo_upload,.img_desc_upload{
        width:80px;height:80px;border:solid 1px #CCCCCC;
        line-height:80px;text-align: center;cursor: pointer;
        float:left;margin-right:10px;position: relative;cursor: pointer;
    }
    .del_img,.del_logo,.del_desc_img{
        display: none;position: absolute;width:80px;height: 80px;
        background-color: gray;color:red;
        opacity: 0.5;top:0;left:0;line-height: 80px;
        text-align: center;font-size:20px;
    }
    #container {
        width: 700px;
        min-height:400px;
    }
</style>

<script type="text/javascript">
    $(function() {
        var ue = UE.getEditor('container', {
            toolbars: [['fullscreen', 'source', 'undo', 'redo', 'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'superscript', 'subscript', 'removeformat', 'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist',
                'simpleupload', 'insertimage', 'selectall', 'cleardoc']],
            autoHeightEnabled: true,
            autoFloatEnabled: true
        });
        $(".img").change(function () {
            var val = $("#imglist").val();
            var len = val.split(",").length;
            if (len > 4) {
                alert("图片上传不超过5张");
                return false;
            }
            var formData = new FormData();
            formData.append('img', $(this)[0].files[0]);
            var im = $(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '/admin/goods/upload',
                type: 'post',
                data: formData,
                dataType: "json",
                processData: false,
                contentType: false,
                success: function (jret) {
                    if (jret.imgurl == '' || typeof jret.imgurl == 'undefined') return;
                    var data = jret;
                    var url = jret.imgurl;
                    var clone_img = im.parent().clone(true);
                    $("#img_append_area").prepend(clone_img);
                    var del_btn = '<div style="" class="del_img" img_id="' + data['id'] + '">X</div>';
                    var hidden_input = '<input type="hidden" name="images_url" class="images_url" value="' + url + '"/>';
                    im.parent().append(del_btn);
                    im.parent().append(hidden_input);
                    im.parent().css("background", "url('" + url + "') no-repeat")
                        .css("background-size", "80px 80px");
                    $("#imglist").val($("#imglist").val() + url + ",");
                }
            }).done(function (res) {
            }).fail(function (res) {
            });
        });

        $(".img_logo").change(function () {
            var formData = new FormData();
            formData.append('img', $(this)[0].files[0]);
            var im = $(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '/admin/goods/upload',
                type: 'post',
                data: formData,
                dataType: "json",
                processData: false,
                contentType: false,
                success: function (jret) {
                    if (jret.imgurl == '' || typeof jret.imgurl == 'undefined') return;
                    var url = jret.imgurl;
                    var del_btn = '<div style="" class="del_logo">X</div>';
                    im.parent().append(del_btn);
                    im.parent().css("background", "url('" + url + "') no-repeat")
                        .css("background-size", "80px 80px");
                    $("#logo").val(url);
                }
            });
        });

        $("div").delegate("div[class='img_upload']", 'mouseover', function () {
            $(this).find("div[class='del_img']").show();
        }).delegate("div[class='img_upload']", 'mouseout', function () {
            $(this).find("div[class='del_img']").hide();
        });

        $("div").delegate("div[class='logo_upload']", 'mouseover', function () {
            $(this).find("div[class='del_logo']").show();
        }).delegate("div[class='logo_upload']", 'mouseout', function () {
            $(this).find("div[class='del_logo']").hide();
        });

        $("div").delegate("div[class='del_img']", 'click', function () {
            var val = '';
            $(".images_url").each(function() {
                val += $(this).val() + ",";
            });
            $("#imglist").val(v);
        });

        $("div").delegate("div[class='del_logo']", 'click', function () {
            $(this).parent().css("background", "");
            $("#logo").val("");
            $(this).remove();
        });

        $(document).on("click", "#add_property", function(evt) {
            evt.preventDefault();
            var tr = '';
            tr += '<tr>';
            tr += '<td><input type="text" name="add_attr_key[]" /></td>';
            tr += '<td><input type="text" name="add_attr_value[]" /></td>';
            tr += '</tr>';
            $("#show_property").append(tr);
        });
    });
    $("#goodstype").on("change", function () {
        var typeid = $(this).val();
        if (typeid != '') {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type:'get',
                data:{'typeid': typeid},
                dataType:'json',
                url:'/admin/goods/getproperty',
                success: function (data) {
                    var keyid = 0;
                    var keyids = html = '';
                    $("#show_sku,.table-title,#show_property, .add_foot").empty();
                    if (data != '') {
                        for (var i in data.propertys) {
                            var property = data.propertys[i];
                            if (property.key_id == keyid) continue;
                            else keyid = property.key_id;
                            html += "<tr>";
                            html += "<td>" + property.key_name +"</td>";
                            html += '<td>' ;
                            if (property.is_enum == 0) {
                                html += '<input type="text" name="common_attr[' +property.key_id + ' ][]" />';
                            } else {
                                $(".table-title").append("<th>"+property.key_name+"</th>");
                                keyids += keyid + ",";
                                if (property.value_name != '') {
                                    for (var j in data.propertys) {
                                        var attr_value = data.propertys[j];
                                        if (attr_value.key_id != keyid) continue;
                                        html += '<input type="checkbox" name="attr[' + property.key_id + '][]" class="attr_' + property.key_id + '"';
                                        html += ' value="' + attr_value.value_id + '" attr-value="' + attr_value.value_name + '"/>';
                                        html += attr_value.value_name + "&nbsp;&nbsp;";
                                    }
                                }
                            }

                        }
                        html += '</td>';
                        html += "</tr>";
                        $("#show_property").append(html);
                        $(".add_foot").append( '<tr>'+ '<td colspan="2"><button id="add_property" class="btn btn-primary">添加属性</button></td>' +'</tr>');
                    }
                    $("#attr_key_ids").val(keyids);
                }
            });
        }
    });
    $("#create_sku").on("click", function(event) {
        event.preventDefault();
        $(".table-title").append("<th>价格</th><th>数量</th>");
        var ids = $("#attr_key_ids").val();
        console.log(ids);
        id_arr = ids.split(",");
        console.log(id_arr);
        var len = id_arr.length - 1;
        var attr = {};
        for (var i = 0; i < len; i++) {
            attr[id_arr[i]] = [];
            $(".attr_" + id_arr[i]).each(function(index) {
                if ($(this).prop("checked")) {
                    attr[id_arr[i]][index] = [];
                    attr[id_arr[i]][index]['val'] = $(this).val();
                    attr[id_arr[i]][index]['attr'] = $(this).attr("attr-value");
                }
            });
        }
        console.log(attr);
        /*for (var j = 0; j < len; j++) {
            console.log(attr[id_arr[j]]);
        }*/
        var tr = '';
        for (var i in attr[id_arr[0]]) {
            if (len == 1) {
                tr += "<tr>";
                tr += "<td><input type='hidden' name='keys1[]' value='"+id_arr[0]+"' /><input type='hidden' name='values1[]' value='"+attr[id_arr[0]][i]['val']+"' />"+attr[id_arr[0]][i]['attr']+"</td>";
                tr += "<td><input type='text' name='price[]' /></td>";
                tr += "<td><input type='text' name='num[]' /></td>";
                tr +="</tr>";
            } else if (len == 2) {
                for (var j in attr[id_arr[1]]) {
                    tr += "<tr>";
                    tr += "<td><input type='hidden' name='keys1[]' value='"+id_arr[0]+"' /><input type='hidden' name='values1[]' value='"+attr[id_arr[0]][i]['val']+"' />"+attr[id_arr[0]][i]['attr']+"</td>";
                    tr += "<td><input type='hidden' name='keys2[]' value='"+id_arr[1]+"' /><input type='hidden' name='values2[]' value='"+attr[id_arr[1]][j]['val']+"' />"+attr[id_arr[1]][j]['attr']+"</td>";
                    tr += "<td><input type='text' name='price[]' /></td>";
                    tr += "<td><input type='text' name='num[]' /></td>";
                    tr +="</tr>";
                }
            } else if (len == 3) {
                for (var j in attr[id_arr[1]]) {
                    for (var k in attr[id_arr[2]]) {
                        tr += "<tr>";
                        tr += "<td><input type='hidden' name='keys1[]' value='"+id_arr[0]+"' /><input type='hidden' name='values1[]' value='"+attr[id_arr[0]][i]['val']+"' />"+attr[id_arr[0]][i]['attr']+"</td>";
                        tr += "<td><input type='hidden' name='keys2[]' value='"+id_arr[1]+"' /><input type='hidden' name='values2[]' value='"+attr[id_arr[1]][j]['val']+"' />"+attr[id_arr[1]][j]['attr']+"</td>";
                        tr += "<td><input type='hidden' name='keys3[]' value='"+id_arr[2]+"' /><input type='hidden' name='values3[]' value='"+attr[id_arr[2]][k]['val']+"' />"+attr[id_arr[2]][k]['attr']+"</td>";
                        tr += "<td><input type='text' name='price[]' /></td>";
                        tr += "<td><input type='text' name='num[]' /></td>";
                        tr +="</tr>";
                    }
                }
            } else if (len == 4) {
                for (var j in attr[id_arr[1]]) {
                    for (var k in attr[id_arr[2]]) {
                        for (var m in attr[id_arr[3]]) {
                            tr += "<tr>";
                            tr += "<td><input type='hidden' name='keys1[]' value='" + id_arr[0] + "' /><input type='hidden' name='values1[]' value='" + attr[id_arr[0]][i]['val'] + "' />" + attr[id_arr[0]][i]['attr'] + "</td>";
                            tr += "<td><input type='hidden' name='keys2[]' value='" + id_arr[1] + "' /><input type='hidden' name='values2[]' value='" + attr[id_arr[1]][j]['val'] + "' />" + attr[id_arr[1]][j]['attr'] + "</td>";
                            tr += "<td><input type='hidden' name='keys3[]' value='" + id_arr[2] + "' /><input type='hidden' name='values3[]' value='" + attr[id_arr[2]][k]['val'] + "' />" + attr[id_arr[2]][k]['attr'] + "</td>";
                            tr += "<td><input type='hidden' name='keys4[]' value='" + id_arr[3] + "' /><input type='hidden' name='values4[]' value='" + attr[id_arr[3]][m]['val'] + "' />" + attr[id_arr[3]][m]['attr'] + "</td>";
                            tr += "<td><input type='text' name='price[]' /></td>";
                            tr += "<td><input type='text' name='num[]' /></td>";
                            tr += "</tr>";
                        }
                    }
                }
            }
        }
        $("#show_sku").append(tr);
        $("#sku_table").show();
        $("#len").val(len);
    });
</script>