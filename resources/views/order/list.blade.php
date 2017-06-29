@extends('layouts.comm')

@section('title', '我的订单')

@section('content')
    <script src="/js/swiper.min.js"></script>
    <link rel="stylesheet" href="/css/swiper.min.css" />
    <div class="order-list">
        <div class="swiper-container">
            <div class="buttons-tab" id="index-tab">
                <a href="#tab1" class="tab-link active button" attr-value="">全部</a>
                <a href="#tab1" class="tab-link active button" attr-value="">待付款</a>
                <a href="#tab1" class="tab-link active button" attr-value="">已付款</a>
                <a href="#tab1" class="tab-link active button" attr-value="">已发货</a>
            </div>
            <div class="swiper-wrapper">
                <div class="swiper-slide goods-slide" attr-is-add="0">
                    <div class="goods-item" attr-id="{{ $item->goodsid }}">
                        <div class="goods-item-icon" style="background-image:url({{ $item->goodsicon }})"></div>
                        <div class="goods-item-content">
                            <div class="goods-name">{{ $item->goodsname }}</div>
                            <div></div>
                            <div class="goods-price">￥{{ $item->price / 100 }} 元</div>
                        </div>
                        <br clear="all" />
                    </div>
                    <br clear="all" />
                </div>
                <div class="swiper-slide goods-slide" attr-value="1" attr-is-add="0"></div>
                <div class="swiper-slide goods-slide" attr-value="2" attr-is-add="0"></div>
                <div class="swiper-slide goods-slide" attr-value="3" attr-is-add="0"></div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        mySwiper = new Swiper('.swiper-container',{
            speed: 500,
            slideToClickedSlide: true,
            onSlideChangeStart: function(swiper){
                console.log(swiper.activeIndex);
                var index = swiper.activeIndex;
                $(".tab-link").removeClass("active");
                $(".tab-link:eq("+index+")").addClass("active");
                getdata(index);
            },
        });
    </script>
@endsection