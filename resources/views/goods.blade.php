@extends('layouts.comm')

@section('title', '商品详情')

@section('content')
    <script src="/js/swiper.min.js"></script>
    <link rel="stylesheet" href="/css/swiper.min.css" />
    <div class="content goods">
        <div class="swiper-container pic-container">
            <div class="swiper-wrapper">
                @foreach(explode(',', $goods->goodspic) as $key=>$value)
                    <div class="swiper-slide" style="background-image:url('{{ $value }}')">
                        <div class="index-pic-num">{{ $key+1 }}/{{count(explode(',', $goods->goodspic))}}</div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="goods-content">
            <div class="goods-header">
                <div class="goods-content-header">
                    <div class="goods-content-name">{{ $goods->goodsname }}</div>
                    <div class="goods-content-price"><span>￥ {{ $goods->price / 100 }}</span></div>
                    <div class="goods-content-detail">
                        <div>满500包邮</div>
                        <div></div>
                        <div>库存 {{ $count }}</div>
                        <br clear="both" />
                    </div>
                </div>
                <div class="goods-select">
                    <div>购买</div>
                    <div style=""></div>
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
        <div class="goods-act">
            <div class="lookcar">查看购物车</div>
            <div class="addcar">加入购物车</div>
            <div class="buy">立即购买</div>
            <br clear="all" />
        </div>
    </div>
    <input type="hidden" id="goodsid" name="goodsid" value="{{ $goods->goodsid }}"/>
    <input type="hidden" id="act_type" name="act_type" value="" />
    <div class="sku-property"></div>
    <div class="property-select-area">
        <div class="goods-property-icon" style="background-image:url('{{ $goods->goodsicon }}')"></div>
        <div class="goods-property-close"></div>
        <div class="goods-property-area">
        </div>
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
        <div class="goods_sure">确认</div>
        <div class="goods_sure_act">
            <div class="goods_addcar">加入购物车</div>
            <div class="goods_buy">立即购买</div>
        </div>
    </div>
    <style type="text/css">
        .goods_sure_act {
            width: 100%;
            display: none;
            border-top: solid 1px red;
            margin-top: 0.5rem;
        }
        .goods_sure_act div {
            float: left;
            background: #F2F2F2;
            line-height:2.4rem;
            width: 50%;
            text-align: center;
            font-size: 1.2rem;
        }
        .goods_addcar {
            border-right: solid 1px red;
        }
        .goods-property-close {
            background: url('/images/close.png') no-repeat;
            background-size: 100%;
            position: absolute;
            width: 1.5rem;
            height: 1.5rem;
            top: 0.5rem;
            right: 1rem;
        }
        .swiper-slide {
            height: 9rem;
            width: 100%;
            background-size: 100% 100%;
            background-repeat:no-repeat;
            background-position: center center;
            position: relative;
            border-bottom: solid 1px #C1C1C1;
        }
        .index-pic-num {
            background-color: #fff;
            width: 2rem;
            line-height: 1rem;
            position: absolute;
            bottom: 0.8rem;
            right: 5%;
            text-align:center;
            overflow: hidden;
        }
        body {
            position: relative;}
        .goods-content-detail div {
            float: left;
            width: 33%;
        }
    </style>
    <script>
        $(function() {
            $(".sku-property").height($(window).height());
        });
        var picSwiper = new Swiper('.pic-container', {
            loop : true,
            autoplay: 3000,
        });
        $.ajaxSettings = $.extend($.ajaxSettings, {
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(".goods-item").on("click", function() {
            location.href = "/goods";
        });
        $(".goods-select").on("click", function() {
            getsku();
            $(".goods_sure_act ").show();
            $(".goods_sure").hide();
        });
        $(function() {
            $(document).on("click", ".goods-property-key-value", function() {
            	alert('测试号');
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
        $(".goods_addcar").on("click", function() {
            addcar();
        });
        $(".goods_buy").on("click", function() {

        });
        $(".lookcar").on("click", function() {
            location.href = "/car";
        });
        $(".addcar").on("click", function() {
            $("#act_type").val(1);
            getsku();
            $(".goods_sure").show();
            $(".goods_sure_act").hide();
        });
        $(".buy").on("click", function() {
            $("#act_type").val(2);
            getsku();
            $(".goods_sure").show();
            $(".goods_sure_act").hide();
        });
        $(".goods-property-close").on("click", function() {
            $(".goods-property-area").empty();
            $(".sku-property,.property-select-area").hide();
        });
        $(".goods_sure").on("click", function() {
            if ($("#act_type").val() == 1)
                addcar();
            else
                buy();
        });
        function getsku() {
            $.ajax({
                url: '/goods/property',
                type: 'get',
                data: { 'goodsid' : $("#goodsid").val()},
                dataType: 'json',
                async: false,
                success: function(data) {
                    if (data.rs == 1) {
                        var html = '';
                        var key_id = 0;
                        $(".goods-property-area").empty();
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
            $(".sku-property").show();
            $(".property-select-area").show();
            $("#goods_num").val(1);
        }
        function addcar() {
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
        }
        function buy() {
            var attr = [];
            $(".active").each(function() {
                attr[$(this).find(".keys").val()] = $(this).find(".values").val();
            });
            $.ajax({
                url: '/goods/getgoodssku',
                type: 'post',
                data: {'attr': attr,
                    'goodsid': $("#goodsid").val(),
                    'num': $("#goods_num").val()},
                dataType: 'json',
                success: function(data) {
                    if (data.rs == 0) {
                        alert('信息不完整');
                        return false;
                    }
                    location.href = '/order/create?num=' + $("#goods_num").val() + "&goodsid=" + $("#goodsid").val() + "&skuid=" + data.sku_id;
                }
            });
        }
    </script>
@endsection
