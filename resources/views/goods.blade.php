@extends('layouts.app')

@section('title', '商品详情')

@section('content')
    <link rel="stylesheet" href="//g.alicdn.com/msui/sm/0.6.2/css/sm-extend.min.css">
    <script type='text/javascript' src='//g.alicdn.com/msui/sm/0.6.2/js/sm-extend.min.js' charset='utf-8'></script>

    <div class="content goods">
        <div class="goods-pic">

        </div>
        <div class="goods-content">
            <div class="goods-header">
                <div class="goods-content-header">
                    <div class="goods-content-name">{{ $goods->goodsname }}</div>
                    <div class="goods-content-price"><span>￥ {{ $goods->price / 100 }}</span></div>
                    <div class="goods-content-detail"></div>
                </div>
                <div class="goods-select">
                    <div>购买</div>
                    <div><span class="icon icon-right"></span></div>
                </div>
            </div>
            <div class="goods-property">
                <div>规格参数</div>
                <table >
                    @foreach($property as $value)
                        <tr><td>{{ $value->key_name }}</td><td>{{ $value->value_name }}</td></tr>
                    @endforeach
                </table>
            </div>
            <div class="goods-detail">
                <div class="goods-detail-title">
                    <span>商品详情</span>
                </div>
                <div>
                    {!! $goods->goodsdesc !!}
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="goodsid" name="goodsid" value="{{ $goods->goodsid }}"/>
    <div class="popup sku-property">

    </div>
    <div class="property-select-area">
        <div class="goods-property-area"></div>
        <div class="goods_num">
            <div class="goods_num_title">选择数量</div>
            <div class="goods_num_change">
                <div class="goods_reduce_num">-</div>
                <div class="goods_input"><input type="text" name="num" id="goods_num" value="1"/></div>
                <div class="goods_add_num">+</div>
                <br clear="all" />
            </div>
            <br clear="all" />
        </div>
        <div class="goods-act">
            <div class="lookcar">查看购物车</div>
            <div class="addcar">加入购物车</div>
            <div class="buy">立即购买</div>
            <br clear="all" />
        </div>
    </div>
    <script>
        $.ajaxSettings = $.extend($.ajaxSettings, {
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(".goods-item").on("click", function() {
            location.href = "/goods";
        });
        $(".goods-select").on("click", function() {
            $.ajax({
                url: '/goods/property',
                type: 'get',
                data: { 'goodsid' : $("#goodsid").val()},
                dataType: 'json',
                success: function(data) {
                    if (data.rs == 1) {
                        var html = '';
                        var key_id = 0;
                        for (var i in data.propertys) {
                            var property = data.propertys[i];
                            if (key_id != property.key_id) {
                                key_id = property.key_id;
                                html += '<div class="goods-property-item">';
                                html += '<div class="goods-property-key">' + property.key_name + '</div>';
                            } else continue;
                            html += '<div class="goods-property-value">';
                            var value_id = 0;
                            for (var j in data.propertys) {
                                if (data.propertys[j].key_id == key_id) {
                                    if (value_id != data.propertys[j].value_id) {
                                        value_id = data.propertys[j].value_id;
                                        html += '<div class="goods-property-key-value">';
                                        html += '<input type="hidden" name="values[]" class="values" value="'+data.propertys[j].value_id+'"/>';
                                        html += '<input type="hidden" name="keys[]" class="keys" value="'+data.propertys[j].key_id+'"/>';
                                        html += data.propertys[j].value_name;
                                        html += '</div>';
                                    }
                                } else
                                    continue;
                            }
                            html += '</div><br clear="all" /></div>';
                        }
                        $(".goods-property-area").append(html);
                    }
                }
            });

            $.popup(".sku-property");
            $(".property-select-area").show();
            $("#goods_num").val(1);
        });
        $(function() {
            $(document).on("click", ".goods-property-key-value", function() {
                $(this).parent().find(".active").removeClass("active");
                $(this).addClass("active");
            });
        });
        $(".goods_add_num").on('click', function () {
            $("#goods_num").val(parseInt($("#goods_num").val()) + 1);
        });
        $(".goods_reduce_num").on("click", function() {
            var num = parseInt($("#goods_num").val());
            $("#goods_num").val( num > 1 ? num -1 : 1);
        });
        $(".addcar").on("click", function() {
            var attr = [];
            $(".active").each(function() {
                attr[$(this).find(".keys").val()] = $(this).find(".values").val();
            });
            $.ajax({
                url: '/car/add',
                type: 'post',
                data: {'attr': attr,
                        'goodsid': $("#goodsid").val(),
                        'num': $("#goods_num").val()},
                dataType: 'json',
                success: function(data) {
                    if (data.rs == 1) {
                        alert('添加购物车成功');
                    } else {
                        alert(data.msg);
                        if (data.num)
                            $("#goods_num").val(data.num);
                    }
                }
            });
        });
        $(".lookcar").on("click", function() {
            location.href = "/car";
        });
    </script>
@endsection
