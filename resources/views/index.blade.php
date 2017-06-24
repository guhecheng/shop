@extends('layouts.app')

@section('title', '童马儿童在线商城')

@section('content')
    <link rel="stylesheet" href="//g.alicdn.com/msui/sm/0.6.2/css/sm-extend.min.css">
    <script type='text/javascript' src='//g.alicdn.com/msui/sm/0.6.2/js/sm-extend.min.js' charset='utf-8'></script>
    @include('footer')
    <div class="content index">
        <div>
            <div class="swiper-container" data-space-between='10'>
                <div class="swiper-wrapper">
                    <div class="swiper-slide"><img src="//gqianniu.alicdn.com/bao/uploaded/i4//tfscom/i1/TB1n3rZHFXXXXX9XFXXXXXXXXXX_!!0-item_pic.jpg_320x320q60.jpg" alt=""></div>
                    <div class="swiper-slide"><img src="//gqianniu.alicdn.com/bao/uploaded/i4//tfscom/i4/TB10rkPGVXXXXXGapXXXXXXXXXX_!!0-item_pic.jpg_320x320q60.jpg" alt=""></div>
                    <div class="swiper-slide"><img src="//gqianniu.alicdn.com/bao/uploaded/i4//tfscom/i1/TB1kQI3HpXXXXbSXFXXXXXXXXXX_!!0-item_pic.jpg_320x320q60.jpg" alt=""></div>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
        <div>
            <div class="buttons-tab">
                <a href="#tab1" class="tab-link active button">推荐</a>
                @foreach ($types as $key=>$type)
                <a href="#tab{{ $key+2 }}" class="tab-link button" attr-value="{{ $type->typeid }}">{{ $type->typename }}</a>
                @endforeach
            </div>
            <div class="goods">
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
            </div>
        </div>
    </div>

    <script>
        $(".goods-item").on("click", function() {
            location.href = "/goods?goodsid=" + $(this).attr("attr-id");
        });
    </script>
@endsection
