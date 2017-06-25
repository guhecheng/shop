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
                <div class="swiper-slide goods-pic" style="background-image:url('{{ explode(',', $ad->goodspic)[0] }}')">
                    <div class="index-pic-num">{{ $key+1 }}/{{$count}}</div>
                    <div class="index-goods-name">{{ $ad->goodsname }}</div>
                </div>
                @endforeach
            </div>
        </div>
        <div>
            <div class="buttons-tab" id="index-tab">
                <a href="#tab1" class="tab-link active button" attr-value="">推荐</a>
                @foreach ($types as $key=>$type)
                <a href="#tab{{ $key+2 }}" class="tab-link button" attr-value="{{ $type->typeid }}">{{ $type->typename }}</a>
                @endforeach
            </div>
            <div class="swiper-container">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
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
                    <div class="swiper-slide" attr-value="{{ $type->typeid }}"></div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @include('layouts.footer')
    <style type="text/css">
        .banner { position: relative; overflow: auto; }
        .banner li { list-style: none;
            height: 8rem;
            background-size:100% 100%;
            background-repeat:no-repeat;
            position: relative;
        }
        .banner ul li { float: left; }
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
        .buttons-tab {
            background-color: #F2F2F2;
        }
        .swiper-slide,.swiper-container,.swiper-wrapper {  width: 100%; }
    </style>
    <script>
        $(function() {
            $('.banner').unslider({});
        });

        $(document).on("click", ".goods-item", function() {
            location.href = "/goods?goodsid=" + $(this).attr("attr-id");
        });
        var picSwiper = new Swiper('.pic-container', {
            loop : true,
            autoplay: 3000,
        });
        var mySwiper = new Swiper('.swiper-container',{
            fade:{crossFade:true},
            onSlideChangeStart: function(swiper){
                console.log(swiper);
                $.ajax({
                    url: '/goods/getgoods',
                    data: {'typeid': 1},
                    dataType: 'json',
                    type: 'get',
                    success: function(data) {
                        console.log(data);
                        if (data.goods) {
                            for (var i in data.goods) {

                            }
                        }
                    }
                })
            },
            onSlideChangeEnd: function(){
            }
        });
    </script>
@endsection
