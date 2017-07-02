@extends('layouts.comm')

@section('title', '童马儿童在线商城')
@section('index-active', 'active')
@section('content')
    <div class="content index">
        <script src="/js/swiper.min.js"></script>
        <link rel="stylesheet" href="/css/swiper.min.css" />
        <div class="swiper-container pic-container">
            <div class="swiper-wrapper">
                @foreach($ads as $key=>$ad)
                <div class="swiper-slide goods-pic" style="background-image:url('{{ explode(',', $ad->goodspic)[0] }}')" attr-id="{{ $ad->goodsid }}">
                    <div class="index-pic-num">{{ $key+1 }}/{{$count}}</div>
                    <div class="index-goods-name">{{ $ad->goodsname }}</div>
                </div>
                @endforeach
            </div>
        </div>
        <div>
            <div class="swiper-container goods-container">
                <div class="buttons-tab" id="index-tab">
                    <a href="#tab1" class="tab-link active button" attr-value="">推荐</a>
                    @foreach ($types as $key=>$type)
                        <a href="#tab{{ $key+2 }}" class="tab-link button" attr-value="{{ $type->typeid }}">{{ $type->typename }}</a>
                    @endforeach
                </div>
                <div class="swiper-wrapper">
                    <div class="swiper-slide goods-slide" attr-is-add="0">
                        @foreach ($goods as $item)
                        <div class="goods-item" attr-id="{{ $item->goodsid }}">
                            <div class="goods-item-icon" style="background-image:url({{ $item->goodsicon }})"></div>
                            <div class="goods-item-content">
                                <div class="goods-name">{{ $item->goodsname }}</div>
                                <div></div>
                                <div class="goods-price">￥{{ $item->price / 100 }} 元</div>
                            </div>
                            <br clear="all" />
                        </div>
                        @endforeach
                        <br clear="all" />
                    </div>
                    @foreach ($types as $key=>$type)
                    <div class="swiper-slide goods-slide" attr-value="{{ $type->typeid }}" attr-is-add="0"></div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="index-car">
        <div></div>
    </div>
    @include('layouts.footer')
    <style type="text/css">
        .index-car {
            width: 3rem;
            height: 3rem;
            position: absolute;
            bottom: 5rem;
            right: 1rem;
            background-color: red;
            border-radius: 3rem;
        }
        .index-car div {
            background-image: url('/images/car.png');
            background-repeat: no-repeat;
            background-size: 100% 100%;
            width: 2rem;
            height: 2rem;
            margin-left:0.5rem;
            margin-top:0.5rem;
        }
        .buttons-tab a {
            display: block;
            float: left;
            width: {{ 100 / (count($types)+1) }}%;
            line-height: 2rem;
            text-align: center;
        }
        a.active {
            border-bottom: solid 1px red;
        }
        .goods-pic {
            background-repeat:no-repeat; background-size: 100% 100%;}
        .index-goods-name { z-index:100;background: #838079; opacity: 0.5;
            width: 100%;
            color: #fff;
            line-height:1.5rem;
            padding-left: 2%;
            overflow: hidden;
            margin-top: 6.5rem;
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
        .swiper-slide,.swiper-container,.swiper-wrapper {  width: 100%; }
    </style>
    <script>
        $(".index-car").on("click", function () {
            location.href = '/car';
        });
        $(document).on("click", ".goods-item", function() {
            location.href = "/goods?goodsid=" + $(this).attr("attr-id");
        });
        $(document).on("click", ".goods-pic", function() {
            location.href = "/goods?goodsid=" + $(this).attr("attr-id");
        });
        var picSwiper = new Swiper('.pic-container', {
            loop : true,
            autoplay: 3000,
        });
        mySwiper = new Swiper('.goods-container',{
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
                url: '/goods/getgoods',
                data: {'typeid': $(".tab-link:eq("+index+")").attr("attr-value")},
                dataType: 'json',
                type: 'get',
                success: function(data) {
                    if (data.goods) {
                        var html = '';
                        for (var i in data.goods) {
                            var goods = data.goods[i];
                            html +=  '<div class="goods-item" attr-id="'+goods.goodsid+'">';
                            html += '<div class="goods-item-icon" style="background-image:url('+goods.goodsicon+')"></div>';
                            html += '<div class="goods-item-content">';
                            html += '<div class="goods-name">'+goods.goodsname+'</div>';
                            html += '<div></div>';
                            html += '<div class="goods-price">￥ '+goods.price / 100+' 元</div>';
                            html += '</div><br clear="all" /></div>';
                        }
                        $(".goods-slide:eq("+index+")").attr('attr-is-add', 1).append(html);
                    }
                }
            });
        }
    </script>
@endsection
