@extends('layouts.app')

@section('title', '购买')

@section('content')
    <div class="content order-create">
            <div>
                <div class="order-address">
                    @if ($address)
                    <div class="order-address-info">
                        <div class="order-address-info-title"><div>{{ $address->name }}</div><div>电话: {{ $address->phone }}</div></div>
                        <div class="order-address-info-location">{{ $address->address }} {{ $address->location }}</div>
                    </div>
                    @else
                        <div class="order-address-no-default">选择地址</div>
                    @endif
                        <input type="hidden" value="{{ isset($address->address_id) ? $address->address_id : '' }}" name="address_id" id="address_id" />
                        <div class="order-address-select"><span class="icon icon-right"></span></div>
                </div>
                <div class="order-list">
                    <?php
                        $total = $count = $discount_price = 0;
                        switch ($user->level) {
                            case 1: $discount = 0.1; break;
                            case 2: $discount = 0.15; break;
                            case 3: $discount = 0.2; break;
                        }
                    ?>
                    @foreach ($goods as $item)
                        <div class="order-item" attr-id="">
                            <div class="order-item-icon" style="background-image:url({{ $item->goodsicon }})"></div>
                            <div class="order-item-content">
                                <div class="order-item-name">{{ $item->goodsname }}</div>
                                <div class="order-property">
                                    <div class="order-price">￥<span class="order-money">{{ $item->price / 100 }}</span> 元</div>
                                </div>
                                <div class="order-item-num">
                                    <div>{{ $item->property }}</div>
                                    <div>数量: <span class="order-item-count">{{ $item->count }}</span></div>
                                </div>
                                <?php $total += $item->price / 100 * $item->count; $count += $item->count;?>
                                <?php
                                    if ($item->is_discount) {
                                        $discount_price += $item->price / 100 * $discount;
                                    }
                                ?>
                            </div>
                            <br clear="all" />
                        </div>
                    @endforeach
                </div>
                <div class="order-express">
                    <div>运费</div>
                    <div>10元@if($user->level<1)(全场满1000元包邮)@endif</div>
                </div>
                <?php $total += $total >= 1000 ? 0 : ($user->level >= 1 ? 0 : 10); ?>
                @if ($user->level >= 1)
                <div class="order-express">
                    <div>会员卡运费减免</div>
                    <div>-￥10元</div>
                </div>
                @if ($discount_price)
                    <div class="order-discount">
                        <div>会员卡折扣</div>
                            <div>(
                                @if ($user->level == 1)
                                金牌会员: 9折
                                @elseif ($user->level == 2)
                                铂金会员: 8.5折
                                @else
                                钻石会员: 8折
                                @endif
                                )
                                <span style="color:red;">￥-{{ $discount_price }}</span>
                            </div>
                    </div>
                    <?php $total -= $discount_price; ?>
                @endif
                @endif
                @if ($user->score)
                    <div class="order-score-discount">
                        <div>积分折扣</div>
                        <div>{{ $user->score }}积分抵扣{{ $user->score / 100 }}元</div>
                    </div>
                    <?php $total -= $user->score / 100; ?>
                @endif
            </div>
            <div></div>
            <div class="order-act">
                <div class="order-count">
                    共计<?php echo $count; ?>件商品
                </div>
                <div class="order-total">
                    <div>合计: ￥<span id="total_money">{{ $total }}
                        </span>元</div>
                </div>
                <div class="order-buy">付款</div>
            </div>
    </div>
    <div class="bg"></div>
    <div class="order-select-type">
        <div class="order-select-title"><div>请选择支付方式</div><div class="order-pay-close"></div></div>
        @if ($user->money/100 >= $total)
            <div class="card-pay"><div></div><span>会员卡支付</span><span>(余额: {{ $user->money / 100 }})</span></div>
        @endif
        <div class="wx-pay"><div></div><span>微信支付</span></div>
        <div class="order-select-blank"></div>
        <div class="order-sure-pay">确定支付</div>
    </div>
    <style type="text/css">
        .bg {
            width: 100%;
            height:100%;
            position: absolute;
            z-index: 100;
            background: #c0c0c0;
            opacity: 0.4;
        }
        .order-sure-pay {
            text-align: center;
            line-height:2rem;
            font-weight:bold;
            background:#F2F2F2;
            font-size: 0.8rem;
        }
        .order-select-type {
            position: fixed;
            bottom:0;
            width: 100%;
            z-index: 200;
        }
        .order-select-title,.card-pay,.wx-pay{
            background: #fff;
            line-height:2rem;
            padding:0 5%;
            border-bottom:solid 1px #c6c6c6;
        }
        .order-select-title div:first-child {
            float:left;
        }
        .order-select-title:after,  .card-pay:after, .wx-pay:after {
            display:block;clear:both;content:"";visibility:hidden;height:0
        }
        .card-pay div{
            background:url('/images/card.png') no-repeat;
            height: 1.2rem;
            width: 1.6rem;
            background-size: 100%;
            float: left;
            margin-right:2%;
            margin-top:0.4rem;
        }
        .wx-pay div{
            background:url('/images/wx.png') no-repeat;
            height: 1.2rem;
            width: 1.6rem;
            background-size: 100% 100%;
            float: left;
            margin-right:2%;
            margin-top:0.4rem;
        }
        .order-select-blank {
            height:3rem;
            width:100%;
            background:#fff;
        }
        .order-select-title {
            font-weight: bold;
        }
        .order-pay-close {
            background: url('/images/close.png') no-repeat;
            background-size: 100%;
            width: 1.2rem;
            height: 1.2rem;
            margin-top: 0.4rem;
            float:right;
        }
        .order-address:after {
            display:block;clear:both;content:"";visibility:hidden;height:0
        }
        .order-address-no-default {
            float:left;
            width: 80%;
            font-size: 0.8rem;
            padding: 0.6rem 10%;
        }
        #total_money { color: red; }
        .order-score-discount {
            background: #fff;
            width: 100%;
            padding: 0 5%;
            line-height: 1.8rem;
            height: 1.8rem;
            border-bottom: solid 1px #C1C1C1;
        }
        .order-score-discount div {
            float:left;
        }
        .order-score-discount div:last-child {
            float:right;
        }
        .bg,.order-select-type {
            display: none;}
        .active{
            background: #3879D9;
        }
    </style>
    <script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <script type="text/javascript">
        //调用微信JS api 支付
        function jsApiCall(data)
        {
            WeixinJSBridge.invoke(
                'getBrandWCPayRequest',data,
                function(res){
                    if(res.err_msg == "get_brand_wcpay_request:ok" ) {
                        alert('支付成功');
                        location.href = "/order/show?orderno={{ $orderno }}";
                    } else {
                        $("#pay_chance").val(0);
                    }
                }
            );
        }

        function callpay(param)
        {
            if (typeof WeixinJSBridge == "undefined"){
                if( document.addEventListener ){
                    document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
                }else if (document.attachEvent){
                    document.attachEvent('WeixinJSBridgeReady', jsApiCall);
                    document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
                }
            }else{
                jsApiCall(param);
            }
        }
    </script>
    <script>
        $(function() {
            {{--@if ($address)
                $(".order-address").height($(".order-address-info").height());
            @else
                $(".order-address").height($(".order-address-no-default").height());
            @endif--}}
        });
        $(".order-buy").on('click', function () {
            $(".bg,.order-select-type").show();
        });
        $.ajaxSettings = $.extend($.ajaxSettings, {
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(".goods-item").on("click", function() {
            location.href = "/goods?goodsid=" + $(this).attr("attr-id");
        });
        $(".order-address-select").on("click", function() {
            location.href = "/address?from_order=1&orderno={{ $orderno }}";
        });
        $(".order-pay-close").on("click",function () {
            $(".bg,.order-select-type").hide();
        });
        $(".card-pay").on("click", function () {
            $(this).addClass("active");
            $(".wx-pay").removeClass("active");
            $("#pay_type").val(1);
        });
        $(".wx-pay").on("click", function () {
            $(this).addClass("active");
            $(".card-pay").removeClass("active");
            $("#pay_type").val(0);
        });
        /*$(".order-buy").on('click', function () {
            var address_id = $("#address_id").val();
            if (address_id == '') {
                alert('地址没有填写');
                return false;
            }
            $.ajax({
                type:'post',
                data: {'address_id': address_id,
                       'orderno' : {{ $orderno }}
                        },
                dataType:'json',
                url: '/order/add',
                success: function (data) {

                }
            });
         })*/
         $(".order-sure-pay").on("click", function () {
            if ($(".wx-pay").hasClass("active")) {
                $.ajax({
                    url:'/order/pay',
                    data:{'order_no':'{{ $orderno }}', 'money':1},
                    dataType:'json',
                    type:'post',
                    success:function(data) {
                        if (data.rs == 0) {
                            alert(data.errmsg);
                            return false;
                        } else {
                            jsApiCall(data);
                        }
                    }
                });
            }
         });
    </script>
@endsection
