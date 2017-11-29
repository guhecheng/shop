@extends('layouts.comm')
@section('title', '我的优惠券')
@section('my-active', 'active')
@section('content')
    <div class="coupon-list">
       <div class="user">
           <div class="user-header" style="background-image:url({{ $user->avatar }});background-repeat: no-repeat;background-size: 100%;width:4rem;height:4rem;border-radius:2rem;"></div>
           <div class="user-name">{{ $user->uname }}</div>
           <div class="user-desc">送您一张童马生活福利券</div>
       </div>
        <div class="coupons-item" style="background-image:url('/images/coupon.png')" data-id="" data-type="">
            <div class="coupons-left">
                <div class="coupons-discount-price">
                <span>@if ($user->type == 0)
                        黄金福利券
                    @elseif ($user->type == 1)
                        铂金福利券
                    @elseif ($user->type == 2)
                        钻石福利券
                    @endif
            </span><span></span>
                </div>
            </div>
            <div class="coupons-right">
                <div class="coupons-right-1">
                    <div style="font-size:0.8rem;">使用该券可享受@if ($user->type == 0)黄金@elseif ($user->type == 1)铂金@elseif($user->type == 2)钻石@endif会员同等折扣1次</div>
                </div>
                <div class="coupons-right-2">
                    <div>使用期限: </div>
                    <div class="coupons-date">{{ date("Y-m-d", strtotime($user->start_at)) }} - {{ date("Y-m-d", strtotime($user->end_at)) }}</div>
                </div>
            </div>
        </div>

        <div class="recv-coupon" id="recv-coupon">点击领取</div>
    </div>
    <style type="text/css">
        .recv-coupon { margin:1rem auto;
            text-align: center;
            background: red;
            width: 40%;
            height: 2.4rem;
            line-height: 2.4rem;
            border-radius: 0.4rem;
            color: #fff;
        }
        .user-name { margin-top:0.2rem; margin-bottom: 0.2rem;}
        .user div { margin: 0 auto;
            text-align: center;}
        .user { background: #fff; margin-bottom: 0.8rem;}
        .user-desc { padding-bottom: 0.2rem; }
        .coupon-desc { width:100%;padding: 0.5rem;background:#fff;margin-bottom: 0.6rem; }
        .coupon-desc p { text-align: center; }
        .coupon-detail {
            margin-bottom: 0.5rem; }
        #share_coupon { width: 40%; height: 1.5rem;
            line-height:1.5rem;  text-align: center;background: red; border-radius:0.4rem;
            align:right; color: #fff; }
        .user_index .icon {
            width: 3rem;
            height: 3rem;
            background-repeat:no-repeat;
            border-radius:3rem;
            border: solid 1px #838588;
            margin: 0 auto;
            background-size: 100% 100%;
        }
        .order div:first-child { float:left; }
        .order div:last-child { float:right; }
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
    <script type="text/javascript" charset="utf-8">
        $("#recv-coupon").on("click", function () {
            $.ajax({
                url:'/user/recvcoupon',
                data: { 'openid': {{ $openid }}, 'coupon_id': {{ $id }} },
                dataType:'json',
                type: 'get',
                success: function (data) {
                    if (data.rs == 0) {
                        layer.open({
                            content: '领取失败'
                            ,btn: '确定'
                        });
                        return false;
                    } else {
                        layer.open({
                            content: '领取成功'
                            ,btn: '确定'
                        });
                        location.href = '/';
                    }

                }
            });
        });
    </script>

@endsection