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
            <div class="swiper-container type-container">
                <div class="swiper-wrapper type-wrapper" style="background:#fff;border-bottom:solid 1px #c1c1c1">
                    <div class="swiper-slide type-slide active" attr-is-add="0" attr-id="0" style="border-bottom:0">推荐</div>
                    @foreach ($types as $key=>$type)
                    <div class="swiper-slide type-slide" attr-is-add="0" attr-id="{{ $type->typeid }}" style="border-bottom:0">
                        {{ $type->typename }}
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="swiper-container goods-container">
                {{--<div class="buttons-tab" id="index-tab">
                    <a href="#tab1" class="tab-link active button" attr-value="">推荐</a>
                    @foreach ($types as $key=>$type)
                        <a href="#tab{{ $key+2 }}" class="tab-link button" attr-value="{{ $type->typeid }}">{{ $type->typename }}</a>
                    @endforeach
                </div>--}}
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
        .type-wrapper .active {
            color: red;
        }
        .type-slide {
            line-height:2.6rem;
            text-align: center;
            font-size: 1rem;
            background: #fff;
            border-bottom: solid 1px #C1C1C1;
            /* Center slide text vertically */
            display: -webkit-box;
            display: -ms-flexbox;
            display: -webkit-flex;
            display: flex;
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            -webkit-justify-content: center;
            justify-content: center;
            -webkit-box-align: center;
            -ms-flex-align: center;
            -webkit-align-items: center;
            align-items: center;
        }
        .index-car {
            width: 3rem;
            height: 3rem;
            position: absolute;
            bottom: 5rem;
            right: 1rem;
            background-color: red;
            border-radius: 3rem;
            background-image: url('/images/car.png');
            background-repeat: no-repeat;
            background-size: 100% 100%;
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
            background-repeat:no-repeat; background-size: 100% 100%;
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
        .swiper-slide,.swiper-container,.swiper-wrapper {  width: 100%; }
    </style>
    <script>
        var typeSwiper = new Swiper('.type-container', {
            slidePerView: 5,
            breakpoints: {
                1024: {
                    slidesPerView: 6,
                },
                768: {
                    slidesPerView: 5,
                },
                640: {
                    slidesPerView: 4,
                },
                320: {
                    slidesPerView: 3,
                }
            }
        });
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
            $.ajax({
                url: '/goods/getgoods',
                data: {'typeid': $(".type-slide:eq("+index+")").attr("attr-id")},
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
