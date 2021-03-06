@extends('layouts.comm')

@section('title', '商品详情')

@section('content')
    <script src="/js/swiper.min.js"></script>
    <link rel="stylesheet" href="/css/swiper.min.css" />
    <div class="content goods">
        <div class="swiper-container pic-container">
            <div class="swiper-wrapper">
                @foreach(explode(',', rtrim($goods->goodspic, ',') ) as $key=>$value)
                    <div class="swiper-slide" style="background-image:url('{{ $value }}')">
                        <div class="index-pic-num">{{ $key+1 }}/{{count(explode(',', rtrim($goods->goodspic, ',')))}}</div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="goods-content">
            <div class="goods-header">
                <div class="goods-content-header">
                    <div class="goods-content-name">{{ $goods->goodsname }}</div>
                    <div class="goods-content-price">
                        <div>
                            <span>￥ {{ $goods->price / 100 }}</span>
                            @if (!empty($goods->act_price))
                            <span style="margin-left:0.2rem;color:#000;font-size: 0.6rem;">原价: {{ $goods->act_price /100 }}</span>
                            @endif
                        </div>
                        <div>库存 {{ $count }}</div>
                    </div>
                    @if (0)
                    @if ($goods->ordinary_discount && $goods->golden_discount && $goods->platinum_discount && $goods->diamond_discount)
                    <div class="goods-content-discount">
                        @if ($goods->ordinary_discount && $goods->ordinary_discount < 100)
                        <div>普通会员<span style="color:red;"> {{ $goods->ordinary_discount / 10 }}折</span></div>
                        @endif
                        @if ($goods->golden_discount && $goods->golden_discount < 100)
                        <div>黄金会员<span style="color:red;"> {{ $goods->golden_discount / 10 }}折</span></div>
                        @endif
                        @if ($goods->platinum_discount && $goods->platinum_discount < 100)
                        <div>铂金会员<span style="color:red;"> {{ $goods->platinum_discount / 10 }}折</span></div>
                        @endif
                        @if ($goods->diamond_discount && $goods->diamond_discount < 100)
                        <div>钻石会员<span style="color:red;"> {{ $goods->diamond_discount / 10 }}折</span></div>
                        @endif
                    </div>
                        @endif
                    @endif
                    <div class="goods-content-detail">
                        <div>满1000包邮|<span style="color:red;">充值会员全场包邮</span></div>
                        <div></div>

                        <br clear="both" />
                    </div>
                </div>
                <div class="goods-select">
                    <div>购买</div>
                    <div style=""></div>
                </div>
                @if ($goods->is_discount || $goods->score_award)
                <div class="goods-activity">
                    @if ($goods->score_award)
                    <div class="goods-activity-item1">
                        <div>积分活动</div>
                        <div>{{ $goods->score_award }}倍积分</div>
                    </div>
                    @endif
                    @if ($goods->is_discount)
                    <div class="goods-activity-item2">会员卡折扣</div>
                    @endif
                </div>
                @endif
            </div>
            @if (!empty($property))
            <div class="goods-property-list">
                <ul>
                    @foreach($property as $value)
                        <li class="goods-property-li"><div>{{ $value->key_name }}:</div><div>{{ $value->value_name }}</div></li>
                    @endforeach
                </ul>
            </div>
            @endif
            <div class="goods-detail">
                <div class="goods-detail-title">
                    <span>商品详情</span>
                </div>
                <div>
                    {!! $goods->goodsdesc !!}
                </div>
            </div>
        </div>
        <div class="goods-act" style="border-top: solid 1px #C1C1C1;">
            <div class="lookcar">查看购物车</div>
            <div class="addcar" style="border-left:solid 1px #C1C1C1;border-right:solid 1px #C1C1C1">加入购物车</div>
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
        .goods-content-price div:first-child {
            float: left;
        }
        .goods-content-price div:last-child {
            float: right; }
        .goods-content-price:after, .goods-content-discount:after {
            display:block;clear:both;content:"";visibility:hidden;height:0
        }
        .goods-detail { margin-bottom: 3.2rem;}
        .goods_num {
            margin-bottom:1rem;}
        .goods-property-list { background: #fff;padding-left:5%;}
        .goods-property-list:after,.goods-activity:after,.goods-select:after {
             display:block;clear:both;content:"";visibility:hidden;height:0
         }
        .goods-activity {
            line-height:1.8rem;
            padding:0 5%;
            background: #fff;
            border-top: solid 1px #C1C1C1;
        }
        .goods-activity-item2 {
            text-align: right;
        }
        .goods-select {
            border-bottom: 0;
        }
        .goods-property-li div{
            font-weight: 400; line-height: 1.8rem;}
        .goods-activity-item1, .goods-activity-item2 {
            float:left;
            width: 50%;
        }
        .goods-activity-item1 div{ float:left; width: 50%; }
        .goods-property-li div:first-child {
            float:left;
            width: 50%;
            text-align: left;
            font-weight: bold;
        }
        .goods-property-li div:last-child {
            float:left;
        }
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
            height: 12rem;
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
        .goods-content-detail div { float: left; line-height: 2rem; }
    </style>
    <script>
        $(function() {
            $(".sku-property").height($(window).height());
            $(document).on("touchstart", ".goods-property-key-value", function() {
                $(this).parent().find(".active").removeClass("active");
                $(this).addClass("active");
            });
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
            buy();
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
                console.log($(this));
                attr[$(this).find(".keys").val()] = $(this).find(".values").val();
            });
            console.log(attr);
            $.ajax({
                url: '/car/add',
                type: 'post',
                data: {'attr': attr,
                    'goodsid': $("#goodsid").val(),
                    'num': $("#goods_num").val()},
                dataType: 'json',
                success: function(data) {
                    if (data.rs == 1) {
                        layer.open({
                            content: '添加购物车成功'
                            ,btn: '确定'
                        });
                    } else {
                        layer.open({
                            content: data.msg
                            ,btn: '确定'
                        });
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
                        layer.open({
                            content: '信息不完整'
                            ,btn: '确定'
                        });
                        return false;
                    }
                    location.href = '/order/create?num=' + $("#goods_num").val() + "&goodsid=" + $("#goodsid").val() + "&skuid=" + data.sku_id;
                }
            });
        }
    </script>
@endsection
