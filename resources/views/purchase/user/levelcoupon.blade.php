@extends('layouts.comm')
@section('title', '我的优惠券')
@section('my-active', 'active')
@section('content')
    @if ($flag)
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
    @else
    <div class="coupon-list">
        <div class="coupon-desc">
            <div class="coupon-detail">
                点开右上角选择分享到朋友
            </div>
        </div>
        @if (!empty($user))
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
        @endif
    </div>
    @endif
    <style type="text/css">
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
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
    <script type="text/javascript" charset="utf-8">
        wx.config(<?php echo $js->config(array('onMenuShareAppMessage')) ?>);
        wx.ready(function(){
            // config信息验证后会执行ready方法，所有接口调用都必须在config接口获得结果之后，config是一个客户端的异步操作，所以如果需要在页面加载时就调用相关接口，则须把相关接口放在ready函数中调用来确保正确执行。对于用户触发时才调用的接口，则可以直接调用，不需要放在ready函数中。
            wx.onMenuShareAppMessage({
                title: '优惠券', // 分享标题
                desc: '<?php if ($user->type == 0)
                                echo '黄金福利券';
                             elseif ($user->type == 1)
                                 echo '铂金福利券';
                             elseif ($user->type == 2)
                                 echo '钻石福利券';
                        ?>', // 分享描述
                link: '', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
                imgUrl: 'http://www.jingyuxuexiao.com/images/aboutme_logo.png', // 分享图标
                type: '', // 分享类型,music、video或link，不填默认为link
                dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                success: function () {
                    // 用户确认分享后执行的回调函数
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });
        });
        @if ($flag)
        $("#recv-coupon").on("click", function () {
            $.ajax({
                url:'/user/recvcoupon',
                data: { 'openid': {{ $openid }}, 'coupon_id': {{ $id }}, 'uname': '{{ $uname }}'  },
                dataType:'json',
                type: 'get',
                success: function (data) {
                    if (data.rs == 0) {
                        layer.open({
                            content: data.errmsg
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
        @endif
    </script>
@endsection