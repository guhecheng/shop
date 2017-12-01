@extends('layouts.comm')
@section('title', '我的券包')
@section('index-active', 'active')
@section('content')
<script src="/js/swiper.min.js"></script>
<link rel="stylesheet" href="/css/swiper.min.css" />
<div class="coupon-list">
    <div class="swiper-container">
        <div class="buttons-tab" id="index-tab">
            <a href="#tab1" class="tab-link active button" attr-value="">待领取</a>
            <a href="#tab1" class="tab-link button" attr-value="">未使用</a>
            <a href="#tab1" class="tab-link button" attr-value="">已使用</a>
            <a href="#tab1" class="tab-link button" attr-value="">已过期</a>
        </div>
        <div class="swiper-wrapper">
            <div class="swiper-slide" attr-is-add="0">
            @if (!empty($coupons))
            @foreach ($coupons as $coupon)
            @if ($coupon->end_date >= date("Y-m-d"))
            <div class="coupons-item" style="background-image:url('/images/coupon.png')" data-id="{{ $coupon->id }}" data-type="{{ $coupon->coupon_type }}">
                <div class="coupons-left">
                    <div class="coupons-discount-price"><span>￥</span><span>{{ $coupon->discount_price/100 }}</span></div>
                    @if ($coupon->goods_price > 0)
                    <div class="coupons-goods-price">满￥{{ $coupon->goods_price/100 }}元可用</div>
                    @endif
                </div>
                <div class="coupons-right">
                    <div class="coupons-right-1">
                        <div>使用范围:</div>
                        @if ($coupon->type == 1)
                        <div>仅限充值使用</div>
                        @else
                        <div>仅限{{ $coupon->brand_names }}使用</div>
                        @endif
                    </div>
                    <div class="coupons-right-2">
                        <div>使用期限: </div>
                        <div class="coupons-date">{{ $coupon->start_date }}-{{ $coupon->end_date }}</div>
                    </div>
                    @if ($coupon->num > 0)
                    <div class="coupons-right-2">剩余数量: {{ $coupon->num }}张</div>
                    @endif
                </div>
            </div>
            @endif
            @endforeach
            @endif
            </div>
            <div class="swiper-slide" attr-is-add="0"></div>
            <div class="swiper-slide" attr-is-add="0"></div>
            <div class="swiper-slide" attr-is-add="0"></div>
        </div>
    </div>
</div>
<style type="text/css">
    .coupons-item {
        width: 100%;
        background-repeat:no-repeat;
        background-size: 100% 100%;
        margin-bottom: 0.5rem;
    }
    .coupons-item:after {
        content:".";
        clear:both;
        display:block;
        height:0;
        overflow:hidden;
        visibility:hidden;
    }
    .coupons-left {
        float:left;
        width:40%;
        color: #fff;
        padding-top: 1.5rem;
        padding-bottom: 1.5rem;
        height: 7rem;
    }
    .coupons-discount-price {
        margin-left: 12%;
    }
    .coupons-discount-price span:first-child { font-size:1rem; }
    .coupons-discount-price span:last-child {
        font-size: 2rem;
    }
    .coupons-goods-price { margin-left:12%; }
    .coupons-right {
        width: 60%;
        float: left;
        padding-top: 0.5rem;
        padding-bottom: 1.5rem;
        height: 8rem;
    }
    .coupons-right > div {
        margin-left: 8%;
    }
    .coupons-right-1, .coupons-right-2 { margin-bottom:0.6rem; }
    .coupons-right-2 div:last-child { font-size:0.8rem; }
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
    .coupons-date { font-size: 0.7rem; }
</style>
<script type="text/javascript">
    $(function() {
        $(document).on("click", ".order-item", function () {
            if ($.trim($(this).find(".order-item-status").text()) == '已失效') return;
            location.href = "/ordershow?orderno=" + $(this).attr("attr-id");
        });
    });
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    mySwiper = new Swiper('.swiper-container',{
        slideToClickedSlide: true,
        onSlideChangeStart: function(swiper){
        var index = swiper.activeIndex;
        $(".tab-link").removeClass("active");
        $(".tab-link:eq("+index+")").addClass("active");
        getdata(index);
    },
    });
    $(".tab-link").on("click", function () {
        $(".tab-link").removeClass("active");
        $(this).addClass("active");
        var index = $(this).index();
        mySwiper.slideTo(index);
    });
    $(document).on('click', '.coupons-item', function () {
        var coupon_id = $(this).attr('data-id');
        if ($(this).attr('data-type') != 2) return false;
        var th = $(this);
        $.ajax({
            url:'/coupon/getcoupon',
            data:{'coupon_id': coupon_id},
            dataType:'json',
            type:'post',
            success: function (data) {
                if (data.rs == 0)
                    layer.open({
                        content: data.errmsg
                        ,btn: '确定'
                    });
                else {
                    layer.open({
                        content: '领取成功'
                        ,btn: '确定'
                    });
                    th.remove();
                }
            }
        })
    });
    function getdata(index) {
        if ($(".swiper-slide:eq("+index+")").attr("attr-is-add") == 1) return;
        $.ajax({
            url: '/coupon',
            data:{'status': parseInt(index)},
            dataType:'json',
            type:"get",
            success:function (data) {
                if (data.coupons) {
                    console.log(data.coupons);
                    $(".swiper-slide:eq("+index+")").attr('attr-is-add', 1).empty();
                    var html = '';
                    for (var i in data.coupons) {
                        var coupon = data.coupons[i];
                        console.log(typeof(coupon.cname));
                        if (typeof(coupon.cname) === 'undefined') {
                            if (coupon.discount_type == 0) {
                                html += '<div class="coupons-item" data-type="' + coupon.coupon_type + '" data-id="' + coupon.id + '"';
                                if (index == 2) {
                                    html += ' style="background-image:url(' + '/images/coupon_has_userd.png' + ')">';
                                } else if (index == 3) {
                                    html += ' style="background-image:url(' + '/images/coupon_invalid.png' + ')">';
                                } else {
                                    html += ' style="background-image:url(' + '/images/coupon.png' + ')">';
                                }
                                html += '<div class="coupons-left">' +
                                    '                    <div class="coupons-discount-price"><span>￥</span><span>' + coupon.discount_price / 100 + '</span></div>';
                                if (coupon.goods_price > 0)
                                    html += '<div class="coupons-goods-price">满￥' + coupon.goods_price / 100 + '元可用</div>';

                                html += '                </div>' +
                                    '                <div class="coupons-right">' +
                                    '                    <div class="coupons-right-1">' +
                                    '                        <div>使用范围:</div>';
                                if (coupon.type == 1) {
                                    html += '                        <div>仅限充值使用</div>';
                                } else {
                                    html += '                        <div>仅限' + coupon.brand_names + '使用</div>';
                                }
                                html += '                    </div>' +
                                    '                    <div class="coupons-right-2">' +
                                    '                        <div>使用期限: </div>' +
                                    '                        <div class="coupons-date">' + (coupon.is_sub == 1 ? '无限期' : coupon.start_date + '-' + coupon.end_date) + '</div>' +
                                    '                    </div>';
                                html += '                </div>' +
                                    '</div>';
                            } else if (coupon.discount_type == 1) {
                                html += '<div class="coupons-item" data-type="' + coupon.coupon_type + '" data-id="' + coupon.id + '"';
                                if (index == 2) {
                                    html += ' style="background-image:url(' + '/images/coupon_has_userd.png' + ')">';
                                } else if (index == 3) {
                                    html += ' style="background-image:url(' + '/images/coupon_invalid.png' + ')">';
                                } else {
                                    html += ' style="background-image:url(' + '/images/coupon.png' + ')">';
                                }
                                html += 
                                    '<div class="coupons-left">' +
                                    '                    <div class="coupons-discount-price"><span class="coupons-price" style="font-size:1.2rem;">' + coupon.coupon_discount / 10 + '折</span></div>';
                                if (coupon.goods_price > 0)
                                    html += '                    <div class="coupons-goods-price">满￥' + coupon.goods_price / 100 + '元可用</div>';
                                html += '                </div>' +
                                    '                <div class="coupons-right">' +
                                    '                    <div class="coupons-right-1">' +
                                    '                        <div>使用范围:</div>' +
                                    '                        <div>仅限' + coupon.brand_names + '使用</div>' +
                                    '                    </div>' +
                                    '                    <div class="coupons-right-2">' +
                                    '                        <div>使用期限: </div>';
                                if (coupon.is_sub == 1)
                                    html += '                        <div class="coupons-date">无限期</div>';
                                else
                                    html += '                        <div class="coupons-date">' + coupon.start_date + '-' + coupon.end_date + '</div>';

                                html += '                    </div>' +
                                    '                </div>' +
                                    '<br clear="all" /></div>';
                            }
                        } else {
                            html += '<div class="coupons-item" ';
                            if (index == 2) {
                                html += ' style="background-image:url(' + '/images/coupon_has_userd.png' + ')">';
                            } else if (index == 3) {
                                html += ' style="background-image:url(' + '/images/coupon_invalid.png' + ')">';
                            } else {
                                html += ' style="background-image:url(' + '/images/coupon.png' + ')">';
                            }
                            var name = coupon.type == 0 ? '普通福利券' : (coupon.type == 1 ? '黄金福利券' : (coupon.type==2 ? '铂金福利券': '钻石福利券'));
                            html += '<div class="coupons-left"><div class="coupons-discount-price">' + name + '</div></div>';
                            html += '                <div class="coupons-right">' +
                                '                    <div class="coupons-right-1">' +
                                '                        <div>使用范围:</div>';
                            if (coupon.type == 0) {
                                html += '                        <div>使用该券可享受普通会员同等折扣1次</div>';
                            } else if (coupon.type == 1) {
                                html += '                        <div>使用该券可享受黄金会员同等折扣1次</div>';
                            } else if (coupon.type == 2) {
                                html += '                        <div>使用该券可享受铂金会员同等折扣1次</div>';
                            } else if (coupon.type == 3)
                                html += '                        <div>使用该券可享受钻石会员同等折扣1次</div>';
                            html += '                    </div>' +
                                '                    <div class="coupons-right-2">' +
                                '                        <div>使用期限: </div>' +
                                '                        <div class="coupons-date">'+ coupon.start_at+'-'+coupon.end_at+'</div>' +
                                '                    </div>';
                            html += '                </div>' +
                                '</div>';
                        }
                    }
                    $(".swiper-slide:eq("+index+")").attr('attr-is-add', 1).append(html);
                }
            }
        });
    }
</script>
@endsection