@extends('layouts.comm')

@section('title', '我的订单')

@section('content')
    <script src="/js/swiper.min.js"></script>
    <link rel="stylesheet" href="/css/swiper.min.css" />
    <div class="order-list">
        <div class="swiper-container">
            <div class="buttons-tab" id="index-tab">
                <a href="#tab1" class="tab-link {{ empty($status) ? 'active' : '' }} button" attr-value="">全部</a>
                <a href="#tab1" class="tab-link {{ ($status == 1) ? 'active' : '' }} button" attr-value="">待付款</a>
                <a href="#tab1" class="tab-link {{ ($status == 2) ? 'active' : '' }} button" attr-value="">已付款</a>
                <a href="#tab1" class="tab-link {{ ($status == 3) ? 'active' : '' }} button" attr-value="">已发货</a>
            </div>
            <div class="swiper-wrapper">
                @for($i = 0; $i < 4; $i++)
                <div class="swiper-slide goods-slide" attr-is-add="0">
                    @if ($i == 0 && empty($status) && !($orders->isEmpty()))
                    @foreach ($orders as $item)
                    <div class="order-item" attr-id="{{ $item->order_no }}">
                        <div class="order-item-header">
                            <div>{{ $item->order_no }}</div>
                            <div class="order-item-status">
                                @if ($item->status == 1)
                                <span style="color:red;">未付款</span>
                                @elseif ($item->status == 2)
                                待发货
                                @elseif ($item->status == 3)
                                已发货
                                @elseif ($item->status == 4)
                                已完成
                                @else
                                <span style="color:#C1C1C1">已失效</span>
                                @endif
                            </div>
                        </div>
                        <?php $count = 0; ?>
                        @foreach($item->data as $value)
                        <div class="goods-item" attr-id="{{ $value->goodsid }}">
                            <div class="goods-item-icon" style="background-image:url({{ $value->goodsicon }})"></div>
                            <div class="goods-item-content">
                                <div class="goods-name" style="height:1.6rem; line-height:1.6rem; font-size:1rem;">{{ $value->goodsname }}</div>
                                <div class="goods-price" style="height:1rem;font-size:0.8rem;">￥{{ $value->price / 100 }} 元</div>
                                <div class="order-goods-property" style="height: 0.8rem;font-size:0.8rem;padding-top:0.2rem;">
                                    <div style="color:#888888;">{{ $value->property }}</div>
                                    <div>{{ $value->count }}</div>
                                    <?php $count += $value->count; ?>
                                </div>
                            </div>
                            <br clear="all" />
                        </div>
                        @endforeach
                        <div class="price">
                            <div>共计<?php echo $count; ?> 件商品</div>
                            <div>合计<span style="color:red;">￥{{ $item->price / 100 }}元</span>(运费{{ $item->express_price }})</div>
                        </div>
                    </div>
                    @endforeach
                    <br clear="all" />
                    @endif
                </div>
                @endfor
            </div>
        </div>
    </div>
    <style type="text/css">
        #index-tab a {
            display: block;
            float: left;
            line-height: 2rem;
            text-align: center;
            width: 25%;
        }
        #index-tab a.active {
            border-bottom:solid 1px red;
        }
        .order-item {
            margin-bottom:0.4rem;
            padding: 0;
        }
        .order-item-header, .goods-item, .price {
            padding: 0.2rem 5%;
            width: 100%;
        }
        .goods-item {
            padding: 0.4rem 5%;
        }
        .order-item-header {
            line-height: 1.5rem;
        }
        .order-item-header:after,.order-goods-property:after,.price:after{
            clear:both;
            display:block;
            visibility:hidden;
            height:0;
            line-height:0;
            content:'';

        }
        .order-item-header div:first-child {
            float:left;
        }
        .order-item-header div:last-child {
            float:right;
        }
        .goods-item {
            padding-top:0.4rem;
            padding-bottom:0.4rem;
        }
        .order-item-header,.goods-item { border-bottom: solid 1px #C1C1C1; }
        .order-goods-property { line-height: 1rem; }
        .order-goods-property div:first-child { float:left; width: 70%;
            overflow: hidden;}
        .order-goods-property div:last-child { float:right; width: 30%; text-align:right;}
        .price div:first-child { float:left; }
        .price div:last-child { float: right; }
        .price { line-height: 2rem; font-size:0.8rem;}
        .goods-item-content { width: 80%;}
        .goods-name { font-size: 0.8rem;  }
        .goods-item-icon {
            margin-top:0.16rem;
            width:3.6rem;
            height: 3.6rem;}
        .goods-item-content div {
            height:1.4rem;
            line-height: 1.4rem;
        }
    </style>
    <script type="text/javascript">
        $(function() {
            $(document).on("click", ".order-item", function () {
                //if ($.trim($(this).find(".order-item-status").text()) == '已失效') return;
                location.href = "/ordershow?orderno=" + $(this).attr("attr-id");
            });
        });
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        mySwiper = new Swiper('.swiper-container',{
            initialSlide : {{ empty($status) ? 0 : $status }},
            slideToClickedSlide: true,
            autoHeight: true,
            onSlideChangeStart: function(swiper){
                console.log(swiper.activeIndex);
                var index = swiper.activeIndex;
                $(".tab-link").removeClass("active");
                $(".tab-link:eq("+index+")").addClass("active");
                getdata(index);
            },
        });
        $(".tab-link").on("click", function () {
            $(".tab-link").removeClass("active");
            $(this).addClass("active");
            var index = $(this).index()
            mySwiper.slideTo(index);
        });
        function getdata(index) {
            if ($(".goods-slide:eq("+index+")").attr("attr-is-add") == 1) return;
            $.ajax({
                url: '/order/ajaxGetGoods',
                data:{'status': index == 0 ? -1 : index},
                dataType:'json',
                type:"get",
                success:function (data) {
                    if (data.orders) {
                        var html = '';
                        for (var i in data.orders) {
                            var order = data.orders[i];
                            var count = 0;
                            html += '<div class="order-item" attr-id="' + order.order_no + '">';
                            html += '<div class="order-item-header">';
                            html += '<div>' + order.order_no + '</div>';
                            html += '<div>';
                            if (order.status == 1)
                                html += '未支付';
                            else if (order.status == 2)
                                html += '未发货';
                            else if (order.status == 3)
                                html += '待收货';
                            else if (order.status == 4)
                                html += '已收货';

                            html += '</div></div>';
                            for (var j in order.data) {
                                var item = order.data[j];
                                html += '<div class="goods-item" attr-id="' + item.goodsid + '">';
                                html += '<div class="goods-item-icon" style="background-image:url(' + item.goodsicon + ')"></div>';
                                html += '<div class="goods-item-content">';
                                html += '<div class="goods-name">' + item.goodsname + '</div>';
                                html += '<div class="goods-price">￥' + (item.price / 100) + ' 元</div>';
                                html += '<div class="order-goods-property">';
                                html += '<div>' + item.property + '</div>';
                                html += '<div>' + item.count + '</div>';
                                count += item.count;
                                html += '</div></div><br clear="all" /></div>';
                            }
                            html += '<div class="price"><div>共计' + count + ' 件商品</div>';
                            html += '<div>合计<span style="color:red;">￥' + order.price / 100 + '元</span>(运费' + order.express_price + ')</div>';
                            html += '</div>';
                            html += '</div>';
                        }
                        $(".goods-slide:eq(" + index + ")").attr('attr-is-add', 1).append(html);
                    }
                }
            });
        }
    </script>
@endsection