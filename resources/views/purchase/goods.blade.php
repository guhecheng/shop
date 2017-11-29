@extends('layouts.comm')

@section('title', '童马儿童代购商城')
@section('content')
    <div class="purchase_goods">
        <div class="purchase_header"></div>
        <div class="type_index">
            <div class="swiper-container type-container">
                <div class="swiper-wrapper type-wrapper" style="background:#fff;">
                    <div class="swiper-slide type-slide active" attr-is-add="0" attr-id="1" ><div>我的代购商品</div></div>
                    <div class="swiper-slide type-slide" attr-is-add="0" attr-id="2">
                        <div>历史代购记录</div></div>
                </div>
            </div>
            <div class="swiper-container goods-container">
                <div class="swiper-wrapper">
                    <div class="swiper-slide goods-slide" attr-is-add="0">
                        @foreach ($goods as $item)
                        <div class="goods-item" attr-id="{{ $item->id }}">
                            <div class="goods-item-icon" style="background-image:url({{ $item->goodsicon }})"></div>
                            <div class="goods-item-content">
                                <div class="goods-name">{{ $item->goodsname }}</div>
                                <div class="goods-price">￥{{ $item->price / 100 }} 元</div>
                            </div>
                            <br clear="all" />
                        </div>
                        @endforeach
                        <br clear="all" />
                    </div>
                    <div class="swiper-slide goods-slide" attr-value="1" attr-is-add="0"></div>
                </div>
            </div>
            <div class="purchase_add_link"></div>
        </div>
    </div>
    <style type="text/css">
        .goods-container {
            overflow: scroll; }
        .purchase_add_link {
            background:url('/images/purchase_add_link.png') no-repeat;
            -webkit-background-size: 100% 100%;
            background-size: 100% 100%;
            width: 100%;
            height: 5rem;
            position: fixed;
            bottom:0;
            left:0;
            z-index:100;
        }
        .purchase_header {
            background:url('/images/purchase_header.png') no-repeat;
            -webkit-background-size: 100% 100%;
            background-size: 100% 100%;
            width: 100%;
            height: 7rem;
        }
        .goods-slide { margin-bottom:2.4rem; }
        .goods-name {
            margin-bottom:0.2rem;
            text-align: left;
            font-size: 0.8rem;
            min-height: 1.5rem;
            font-weight: 700;
            line-height: 1rem;
            max-height: 3.5rem;
        }
        .goods-price {
            margin-top:0.2rem;
            text-align: left;
        }
        .goods-item {
            width: 50%;
            padding:1rem 5%;
            float:left;
        }
        .goods-item-icon {
            width: 100%;
            padding: 0 5%;
            margin: 0.5rem 0;
            height: 8rem;
            border: solid 1px #c0c0c0;
        }
        .type-wrapper .active {
            color: red;
        }
        .type-brand {
            background-repeat:no-repeat;
            background-size: 100% 100%;
            width: 100%;
            height: 5rem;
        }
        .type-slide {
            line-height:2.6rem;
            text-align: center;
            font-size: 1rem;
            background: #fff;
        }
        .buttons-tab a {
            display: block;
            float: left;
        width: 50%;
        line-height: 2rem;
        text-align: center;
        }
        a.active {
            border-bottom: solid 1px red;
        }
        .goods-pic {
            background-repeat:no-repeat; background-size: 100%;
            height: 9rem;}
        .index-goods-name { z-index:100;background: #838079; opacity: 0.7;
            width: 100%;
            color: #000;
            font-weight: bold;
            line-height:1.5rem;
            padding-left: 2%;
            overflow: hidden;
            margin-top: 7.5rem;
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
        .tab-link {
            line-height: 2.4rem;}
        .buttons-tab {
            background-color: #F2F2F2;
        }
        .type-slide { width:50% !important; }
        p {
            text-align: left;}
        .purchase-item-back, .purchase-item-content { float:left; }
        .purchase-item { background:#fff; padding:0.1rem 5%; margin-bottom:0.4rem;}
        .purchase-item-content { width:80%; }
        .purchase-item-back { width:20%; text-align:center;background:#646464;padding:0.2rem 0.2rem; color:#fff; font-size:0.8rem; margin-top:0.6rem; }
        .purchase-item:after { display:block;clear:both;content:"";visibility:hidden;height:0;  }
        .goods-slide { margin-bottom: 5.4rem; }
    </style>
    <script src="/js/swiper.min.js"></script>
    <link rel="stylesheet" href="/css/swiper.min.css" />
    <script type="text/javascript">
        $.ajaxSettings = $.extend($.ajaxSettings, {
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var typeSwiper = new Swiper('.type-container', {
            slidePerView: 2,
        });
        $(document).on("click", ".goods-item", function() {
            location.href = "/purchase/detail?purchase_id=" + $(this).attr("attr-id");
        });
        $(document).on("click", ".goods-pic", function() {
            location.href = "/purchase/detail?purchase_id=" + $(this).attr("attr-id");
        });
        mySwiper = new Swiper('.goods-container',{
            onSlideChangeStart: function(swiper){
                console.log(swiper.activeIndex);
                var index = swiper.activeIndex;
                $(".type-slide").removeClass("active");
                $(".type-slide:eq("+index+")").addClass("active");
                getdata(index);
                typeSwiper.slideTo(index);
            },
        });
        $(".tab-link").on("click", function () {
            $(".tab-link").removeClass("active");
            $(this).addClass("active");
            var index = $(this).index()
            mySwiper.slideTo(index);
        });
        $(".type-slide").on("click", function () {
            $(".type-slide").removeClass("active");
            $(this).addClass("active");
            var index = $(this).index()
            mySwiper.slideTo(index);
        });
        function getdata(index) {
            if ($(".goods-slide:eq("+index+")").attr("attr-is-add") == 1) return;
            var type = $(".type-slide:eq("+index+")").attr("attr-id");
            $.ajax({
                url: '/purchase/goods',
                data: {'purchase_type': type},
                dataType: 'json',
                type: 'get',
                success: function(data) {
                    console.log(type);
                if (data.goods) {
                    var html = '';
                    if (type == 1) {
                        for (var i in data.goods) {
                            var goods = data.goods[i];
                            html +=  '<div class="goods-item" attr-id="'+goods.id+'">';
                            html += '<div class="goods-item-icon" style="background-image:url('+goods.goodsicon+')"></div>';
                            html += '<div class="goods-item-content">';
                            html += '<div class="goods-name">'+goods.goodsname+'</div>';
                            html += '<div class="goods-price">￥ '+goods.price / 100+' 元</div>';
                            /*if (goods.act_price == 0) {
                                html += '<div class="goods-price">￥ '+goods.price / 100+' 元</div>';
                            } else {
                                html += '<div>原价 : '+goods.price/100+' 元</div>';
                                html += '<div class="goods-price">￥ '+goods.act_price / 100+' 元<div class="is_act_price">特价</div></div>';
                            }*/
                            html += '</div><br clear="all" /></div>';
                        }
                    } else if (type == 2) {
                        console.log(data.goods);
                        for (var i in data.goods) {
                            var goods = data.goods[i];

                            html +=  '<div class="purchase-item" attr-id="'+goods.id+'">';
                            html += '<div class="purchase-item-content">';
                            html += '<div><span>商品描述: </span> <span>'+goods.goods_name+'</span></div>';
                            html += '<div><span>提交时间: </span> <span>'+goods.create_time+'</span></div>';
                            html += '<div><span>状态: </span>';
                            if (goods.is_pay == 2)
                                html += ' <span class="purchase_state">已成功下单</span></div>';
                            else if (goods.is_back == 1)
                                html += ' <span class="purchase_state">已提交</span></div>';
                            else if (goods.is_pay == 3)
                                html += ' <span class="purchase_state">已申请退款</span></div>';
                            else if (goods.is_pay == 1)
                                html += ' <span class="purchase_state">已提交</span></div>';

                            html += '</div>';
                            if (goods.is_pay == 1 && goods.is_back == 1)
                                html += '<div class="purchase-item-back" data-id='+goods.id+'>无货退款申请</div>';
                            html += '</div></div>';
                        }
                    }

                    $(".goods-slide:eq("+index+")").attr('attr-is-add', 1).append(html);
                }
            }
        });
        }
        $(".purchase_add_link").on("click", function () {
            layer.open({
                content: '<p>1.VIP指定代购需支付500元定金，定金可由账户余额或微信支付，待商品确认付款时，只需要支付商品尾款即可下单成功。</p>' +
                '<p>2.客服确认生成链接后会通知到您，该链接会在14天后过期并将您支付的定金自动转入到账户余额，请注意及时付款哦！</p>' +
                '<p>3.更多详情，请咨询客服人员。</p>'
                ,btn: ['同意', '取消']
                ,yes: function(index){
                    location.href = "/purchase/create";
                    layer.close(index);
                }
            });
        });
        $(document).on("click", ".purchase-item-back", function () {
            var purchase_id = $(this).attr('data-id');
            if (purchase_id == '')
                return false;
            var th = $(this);
            $.ajax({
                url:'/purchase/returnpay',
                data:{'purchase_id':purchase_id},
                dataType:'json',
                type:'get',
                success: function (data) {
                    if (data.rs == 0) {
                        layer.open({
                            content: data.errmsg,
                        })
                        return false;
                    } else {
                        th.parent().find(".purchase_state").text("已申请退款");
                        th.remove();
                    }
                }
            })
        });
    </script>
@endsection