@extends('layouts.comm')
@section('title', '我的优惠券')
@section('my-active', 'active')
@section('content')
    <div class="coupon-list">
        <div class="coupon-desc">
            <div class="coupon-detail">
            您在会员等级升级后，将得到对应的会员分享券，分享券可以给您的朋友家人，被分享的用户可以凭券享受一次与您会员等级相同的优惠。
            </div>
            <p>黄金会员 - 1张等级福利分享券</p>
            <p>铂金会员 - 1张等级福利分享券</p>
            <p>钻石会员 - 1张等级福利分享券</p>
        </div>
        @if (!empty($coupons))
        @foreach ($coupons as $coupon)
        <div class="coupons-item" style="background-image:url('/images/coupon.png')" data-id="" data-type="">
            <div class="coupons-left">
                <div class="coupons-discount-price">
                    <span>@if ($coupon->type == 0)
                            黄金福利券
                          @elseif ($coupon->type == 1)
                            铂金福利券
                          @elseif ($coupon->type == 2)
                            钻石福利券
                          @endif
                    </span><span></span>
                </div>
            </div>
            <div class="coupons-right">
                <div class="coupons-right-1">
                    <div style="font-size:0.8rem;">使用该券可享受@if ($coupon->type == 0)黄金@elseif ($coupon->type == 1)铂金@elseif($coupon->type == 2)钻石@endif会员同等折扣1次</div>
                </div>
                <div class="coupons-right-2">
                    <div>使用期限: </div>
                    <div class="coupons-date">{{ date("Y-m-d", strtotime($coupon->start_at)) }} - {{ date("Y-m-d", strtotime($coupon->end_at)) }}</div>
                </div>
                @if (empty($coupon->recv_uname) || $coupon->end_at >= date("Y-m-d H:i:s"))
                <a href="/user/levelcoupon?id={{ $coupon->id }}">
                @endif
                <div class="copuon-right-2">
                    @if ($coupon->recv_uname)
                    <div>已分享给用户{{ $coupon->recv_uname }}领取</div>
                    @endif
                    <div  id="share_coupon" class="<?php if ($coupon->is_recv==1 || $coupon->end_at<=date("Y-m-d H:i:s") ):?>no_use<?php endif; ?>">
                        转赠
                    </div>
                </div>
                @if (empty($coupon->recv_uname) || $coupon->end_at < date("Y-m-d H:i:s"))
                <a href="/user/levelcoupon?id={{ $coupon->id }}">
                @endif
            </div>
        </div>
        @endforeach
        @endif
    </div>
    @include('layouts.footer')
    <style type="text/css">
        .no_use { background: #000; }
        .coupon-desc { width:100%;padding: 0.5rem;background:#fff;margin-bottom: 0.6rem; }
        .coupon-desc p { text-align: center; }
        .coupon-detail {
            margin-bottom: 0.5rem; }
        #share_coupon { width: 40%; height: 1.5rem;
            line-height:1.5rem;  text-align: center;background: red; border-radius:0.4rem;
        float:right; color: #fff;margin-right:0.2rem; }
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
@endsection