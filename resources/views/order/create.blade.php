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
            $total = $count = $wx_total = 0;
            ?>
            <?php $brand_ids = []; ?>
            <?php $coupons = []; ?>
            @foreach ($goods as $item)
            <?php
            switch ($user->level) {
                case 1: $goods_price = empty($item->ordinary_discount) ? $item->price / 100: $item->price * $item->ordinary_discount / 10000;
                        $wx_price = empty($item->brands->ordinary_discount) ? $item->price / 100 : $item->price * $item->brands->ordinary_discount / 10000;
                        break;
                case 2: $goods_price = empty($item->golden_discount) ? $item->price / 100: $item->price * $item->golden_discount / 10000;
                        $wx_price = empty($item->brands->golden_discount) ? $item->price / 100 : $item->price * $item->brands->golden_discount / 10000;
                    break;
                case 3: $goods_price = empty($item->platinum_discount) ? $item->price / 100: $item->price * $item->platinum_discount / 10000;
                    $wx_price = empty($item->brands->platinum_discount ) ? $item->price / 100 : $item->price * $item->brands->platinum_discount / 10000;
                    break;
                case 3: $goods_price = empty($item->diamond_discount) ? $item->price / 100: $item->price * $item->diamond_discount / 10000;
                    $wx_price = empty($item->brands->diamond_discount) ? $item->price / 100 : $item->price * $item->brands->diamond_discount / 10000;
                    break;
                default :
                    $goods_price = empty($item->brands->common_discount) ? $item->price / 100: $item->price * $item->brands->common_discount / 10000;
                    $wx_price = empty($item->brands->common_discount) ? $item->price / 100 : $item->price * $item->brands->common_discount / 10000;
                    break;
            }
            ?>
            <?php
                if (empty($coupons[$item->brand_id]))
                    $coupons[$item->brand_id] = $goods_price * $item->count;
                else
                    $coupons[$item->brand_id] += $goods_price * $item->count;
            ?>
            <?php $brand_ids[] = $item->brand_id; ?>
            <div class="order-item" attr-id="">
                <div class="order-item-icon" style="background-image:url({{ $item->goodsicon }})"></div>
                <div class="order-item-content">
                    <div class="order-item-name">{{ $item->goodsname }}</div>
                    <div class="order-property">
                        <div class="order-price">
                            @if ($goods_price == $item->price / 100)
                            <span class="order-money">￥<?php echo $item->price / 100; ?>元</span>
                            @else
                                <span class="order-money" style="text-decoration:line-through">￥<?php echo $item->price / 100; ?>元</span>
                                ￥<span class="order-money"><?php echo $goods_price; ?></span>元
                            @endif
                        </div>
                    </div>
                    <div class="order-item-num">
                        <div>{{ $item->property }}</div>
                        <div>数量: <span class="order-item-count">{{ $item->count }}</span></div>
                    </div>
                    <?php
                        $total += $goods_price * $item->count;
                        $wx_total += $wx_price * $item->count;
                        $count += $item->count;
                    ?>
                </div>
                <br clear="all" />
            </div>
            @endforeach
            <?php $old_total = $total; ?>
        </div>
        <div class="order-express">
            <div>运费</div>
            <div>10元@if($user->level<1)(全场满1000元包邮)@endif</div>
        </div>
        <input type="hidden" id="coupon_id" value="" />
        <input type="hidden" id="coupon_price" value="0" />
        <input type="hidden" id="old_wx_price" value="{{ $wx_total }}" />
        <input type="hidden" id="score" value="0"/>
        @if ($user->level >= 1 || $wx_total >= 1000)
        <input type="hidden"  id="express_price" value="0" />
        <div class="order-express">
            <div style="width: 50%;">会员卡运费减免</div>
            <div style="float: right;text-align: right;width:50%;">-￥10元</div>
        </div>
        @else
        <input type="hidden"  id="express_price" value="10" />
        <?php
        $total += 10;
        $wx_total += 10;
        ?>
        @endif
        <div class="order-coupon">
            <div>优惠券折扣</div>
            <div id="select_coupon">选择优惠券 <span class="icon icon-right"></span></div>
            <div id="hide_coupon"></div>
        </div>
        @if ($user->score)
        <div class="order-score-discount">
            <div>积分折扣</div>
            <div><span style="margin-right:0.2rem;" class="order-score-show"></span>
                <span class="icon icon-right"></span>
            </div>
        </div>
        @endif
    </div>
    <div></div>
    <input type="hidden" id="real_total" value="{{ $total }}" />
    <input type="hidden" id="real_wx_total" value="{{ $wx_total }}" />
    <div class="order-act">
        <div class="order-count">
            共计<?php echo $count; ?>件商品
        </div>
        <div class="order-total">
            <div>合计: ￥<span id="total_money" data-value="{{ $total }}">{{ $total }}</span>元</div>
            <input type="hidden" id="price" value="{{ $total }}" />
            <input type="hidden" id="wx_real_price" value="{{ $wx_total }}" />
        </div>
        <div class="order-buy">付款</div>
    </div>
</div>
<div class="bg"></div>
<div class="order-select-type">
    <div class="order-select-title"><div>请选择支付方式</div><div class="order-pay-close"></div></div>
    <div class="card-pay"><div></div><span>会员卡支付</span><span id="card_price">(余额: {{ $user->money / 100 }})</span></div>
    <div class="card-no-pay" style="background: #c0c0c0;display: none;"><div></div><span>会员卡支付</span><span>(余额不足)</span></div>
    <div class="wx-pay"><div></div><span>微信支付</span><span id="wx_pay_money" style="margin-left:2rem;"></span></div>
    <div class="order-select-blank"></div>
    <div class="order-sure-pay">确定支付</div>
</div>
@if ($user->score)
<div class="order-score-discount-area">
    <div class="order-score-select-title"><div>选择积分折扣</div><div class="order-score-close"></div></div>
    <div class="order-score-select">
        <div class="order-score-select-type"></div>
        <div class="order-select-score">
            <input type="hidden" id="user_score" value="{{ $user->score }}" />
            <span id="exchange_score"></span>积分抵扣<span data-value="" id="exchange_score_money"></span>元
        </div>
    </div>
    <div class="order-score-sure-btn">确 认</div>
</div>
@endif
<div class="order-coupons-area">
    <div class="order-coupons-select"></div>
    <div class="order-coupons-sure-btn">确 认</div>
</div>
<style type="text/css">
    .order-total div {
        float:right;
        padding-right: 10%;
    }
    .order-coupons-close {
        background: url('/images/close.png') no-repeat;
        background-size: 100%;
        width: 1.4rem;
        height: 1.4rem;
        position: absolute;
        top: 0.2rem;
        right:1%;
    }
    .order-coupons-sure-btn {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        background: #c0c0c0;
        text-align: center;
        line-height: 2rem;
        font-size: 1rem;
    }
    .order-coupons-select {
        background-image:url('/images/coupon.png');
        width: 100%;
        background-repeat:no-repeat;
        background-size: 100% 100%;
        margin-bottom: 0.5rem;
    }
    .order-coupons-title {
        margin-top: 0.5rem;
        margin-bottom: 0.5rem;
        position: relative;
    }
    .order-coupons-title div:first-child {
        text-align: center;
        width: 100%;
        line-height: 2rem;
        font-size: 1rem;
    }
    .order-coupons-area {
        width: 100%;
        height:100%;
        position: absolute;
        z-index: 100;
        background: #fff;
    }
    #hide_coupon, .order-coupons-area { display: none; }
    .order-score-sure-btn {
        width:100%;
        text-align: center;
        background-color: #C1C1C1;
        line-height:1.8rem;
        font-size: 1rem;
        font-weight:bold;
        margin-top:1rem;
    }
    .order-score-discount-area {
        background: #fff;
        position: fixed;
        z-index: 200;
        bottom:0;
        left: 0;
        width:100%;
        display: none;
    }
    .order-score-select-title {
        text-align: center;
        position: relative;
        font-size: 0.8rem;
        line-height: 1.5rem;
        font-weight:bold;
        padding-top:0.6rem;
    }
    .order-score-select-type {
        width:1rem;
        height:1rem;
        background:url('/images/address_no_selected.png') no-repeat;
        background-size: 100%;
        float: left;
        margin-right:20%;
    }

    .order-score-select {
        padding: 0.4rem 10%;
        width: 100%;
    }
    .order-score-close {
        background: url('/images/close.png') no-repeat;
        background-size: 100%;
        width: 1.4rem;
        height: 1.4rem;
        position: absolute;
        top:0;
        right:1%;
    }
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
    .order-select-title,.card-pay,.wx-pay,.card-no-pay  {
        background: #fff;
        line-height:2rem;
        padding:0 5%;
        border-bottom:solid 1px #c6c6c6;
    }
    .order-select-title div:first-child {
        float:left;
    }
    .order-select-title:after,  .card-pay:after, .wx-pay:after, .card-no-pay:after,.order-coupon:after {
        display:block;clear:both;content:"";visibility:hidden;height:0
    }
    .card-pay div,.card-no-pay div{
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
    .order-score-discount div:first-child {
        float:left;
        width: 50%;
    }
    .order-score-discount div:last-child {
        float:right;
        width:50%;
        text-align: right;
    }
    .bg,.order-select-type {
        display: none;}
    .active{
        background: #3879D9;
    }
    .is_active {
        background-image: url('/images/address_selected.png');
    }
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
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
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
        padding-bottom: 0.5rem;
        height: 6rem;
    }
    .coupons-right > div {
        margin-left: 8%;
    }
    .coupons-right-1, .coupons-right-2 { margin-bottom:0.6rem; }
    #select_coupon { float:right; }
    .coupons-item-active{
        border-top:solid 2px yellow;
        border-bottom:solid 2px yellow;
    }
    .coupons-date { font-size:0.6rem; }
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
                    location.href = "/ordershow?orderno={{ $orderno }}";
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
    $.ajaxSettings = $.extend($.ajaxSettings, {
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(function() {
        {{--@if ($address)
            $(".order-address").height($(".order-address-info").height());
            @else
            $(".order-address").height($(".order-address-no-default").height());
            @endif--}}
    });
    $(document).on("click", ".coupons-item", function () {
        if ($(this).hasClass("coupons-item-active")) {
            $(this).removeClass('coupons-item-active');
        } else {
            $(".coupons-item").removeClass('coupons-item-active');
            $(this).addClass('coupons-item-active');
        }
    });
    $(".order-coupons-close").on("click", function () {
        $(".order-coupons-area").hide();
        $(".coupons-item").removeClass("coupons-item-active");
    });
    $("#select_coupon").on("click", function () {
        $.ajax({
            url: '/order/getcoupons',
            type: 'post',
            data: {'brand_ids': '<?php echo implode(",", array_keys($coupons)); ?>', 'price': '<?php echo implode(',', array_values($coupons)); ?>'},
            dataType: 'json',
            success: function (data) {
                console.log(data);
                if (data.coupons == '') {
                    $("#select_coupon").hide();
                    $("#hide_coupon").append("暂无优惠券可用").show();
                } else {
                    var html = '';
                    $(".order-coupons-select").empty();
                    var coupon_id = $("#coupon_id").val();
                    for (var i in data.coupons) {
                        var coupon = data.coupons[i];
                        console.log(coupon);
                        html += '<div class="coupons-item ';
                            if (coupon_id != 0 && coupon.coupon_id == coupon_id) {
                                html += 'coupons-item-active"';
                            }
                        html += '" data-id="'+coupon.coupon_id+'">' +
                            '<div class="coupons-left">' +
                            '                    <div class="coupons-discount-price"><span>￥</span><span class="coupons-price">'+coupon.discount_price/100+'</span></div>' +
                            '                    <div class="coupons-goods-price">满￥' + coupon.goods_price/100 +'元可用</div>' +
                            '                </div>' +
                            '                <div class="coupons-right">' +
                            '                    <div class="coupons-right-1">' +
                            '                        <div>使用范围:</div>' +
                            '                        <div>仅限'+ coupon.brand_names +'使用</div>' +
                            '                    </div>' +
                            '                    <div class="coupons-right-2">' +
                            '                        <div>使用期限: </div>' +
                            '                        <div class="coupons-date">'+coupon.start_date+'-'+coupon.end_date+'</div>' +
                            '                    </div>' +
                            '                </div>' +
                            '</div>';
                    }
                    $(".order-coupons-select").append(html);
                    $(".order-coupons-area").show();

                }
            }
        })
    });
    $(".order-coupons-sure-btn").on("click", function () {
        var price = $.trim($(".coupons-item-active").find(".coupons-price").text());
        if (price == '') {
            $(".order-coupons-area").hide();
            $("#coupon_id").val(0);
            $("#coupon_price").val(0);
            $("#select_coupon").html('选择优惠券 <span class="icon icon-right"></span>');
            $("#total_money").attr("data-value", $("#real_total").val()).text($("#real_total").val());
            $("#price").val($("#real_total").val());
            $("#wx_real_price").val($("#real_wx_total").val());
            return false;
        }
        $(".order-coupons-area").hide();
        $(".order-score-show").empty();
        $("#score").val('');
        $("#coupon_id").val($(".coupons-item-active").attr('data-id'));
        $("#coupon_price").val(price);
        $("#select_coupon").text("-￥" + price);
        var card_price = (parseFloat($("#real_total").val()) - price).toFixed(2);
        $("#total_money").attr("data-value", card_price).text(card_price);
        $("#price").val(card_price);
        $("#wx_real_price").val(($("#real_wx_total").val() - price).toFixed(2));
        console.log($.trim($(".coupons-item-active").find(".coupons-price").text()));
    });
    $(".order-score-discount").on("click", function () {
        console.log($("#coupon_price").val());
        var score = (parseFloat($("#old_wx_price").val()) - parseFloat($("#coupon_price").val())).toFixed(2);
        if (score >= {{ $user->score / 100 }} ) {
            $("#exchange_score").text({{ $user->score }});
            $("#exchange_score_money").text({{ $user->score / 100 }});
        } else {
            $("#exchange_score").text(score * 100);
            $("#exchange_score_money").text(score);
        }
        $(".bg,.order-score-discount-area").show();
    });
    $(".order-score-sure-btn").on("click", function () {
        if ($(".order-score-select-type").hasClass('is_active')) {
            var score = parseFloat($("#exchange_score").text());
            $("#score").val(score);
            $("#total_money").attr("data-value", $("#price").val() - score / 100).text($("#price").val() - score / 100);
            $("#price").val($("#price").val() - score / 100);
            $(".order-score-show").text(score + '积分抵扣'+ score / 100 +'元');
            $("#wx_real_price").val($("#wx_real_price").val() - score / 100);
        } else {
            console.log($("#score"));
            var coupon_money = parseFloat($("#coupon_price").val());
            var card_price = (parseFloat($("#real_total").val()) - coupon_money).toFixed(2);
            $("#total_money").attr("data-value", card_price).text(card_price);
            $("#price").val(card_price);
            $(".order-score-show").text('');
            $("#wx_real_price").val((parseFloat($("#real_wx_total").val()) - coupon_money).toFixed(2));
            $("#score").val(0);
        }
        $(".bg,.order-score-discount-area").hide();
    });
    $(".order-score-close").on("click", function () {
        $(".bg,.order-score-discount-area").hide();
        var score = $.trim($(".order-select-score").find("span").attr("data-value"));
        var total = $("#total_money").attr("data-value");
        $("#total_money").text(total);
        $(".exchange-score").text(parseInt(total));
        $(".order-score-select-type").removeClass("is_active");
        $("#score").val(0);
        $("#price").val(total);
        $("#exchange-score").val(parseInt(total));
    });
    $(".order-buy").on('click', function () {
        var price= $.trim($("#price").val());
        if (price < 0) {
            alert('金额错误')
            return false;
        }
        if (price == 0) {
            var address_id = $("#address_id").val();
            var price = 0;
            var score = $("#score").val();
            var express_price = $("#express_price").val();
            if (address_id == '') {
                alert('地址没有填写');
                return false;
            }
            if (!confirm('确认付款?'))
                return false;
            $.ajax({
                url:'/order/freepay',
                data:{'address_id':address_id, 'score':score, 'express_price': express_price,
                       'price':price, 'order_no':{{ $orderno }},
                        'coupon_id': $("#coupon_id").val()},
                dataType:'json',
                type:'post',
                success: function (data) {
                    if (data.rs == 1) {
                        location.href = "/ordershow?orderno={{ $orderno }}";
                    } else {
                        alert(data.errmsg);
                        return false;
                    }
                }
            })
        } else {
            console.log(price);
            var user_money = {{$user->money / 100}};
            if (price > user_money) {
                $(".card-no-pay").show();
                $(".card-pay").hide();
            }
            $("#wx_pay_money").text($("#wx_real_price").val());
            $(".bg,.order-select-type").show();
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
    $(".order-score-select").on("click", function () {
        var score = $.trim($(".order-select-score").find("span").attr("data-value"));
        var total = $("#total_money").attr("data-value");
        if ($(".order-score-select-type").hasClass("is_active")) {
            $("#total_money").text(total);
            $(".exchange-score").text(parseInt(total));
            $(".order-score-select-type").removeClass("is_active");
            $("#score").val(0);
            $("#price").val(total);
            $("#exchange-score").val(parseInt(total));
        } else {
            $(".order-score-select-type").addClass("is_active");
            $("#total_money").text(parseFloat(total - score));
            $(".exchange-score").text(parseInt(total - score));
            $("#price").val(total - score);
            $("#exchange-score").val(parseInt(total - score));
            $("#score").val(score * 100);
        }
    });
    $(".order-score-discount").on("click",function () {

    });
    $(".order-sure-pay").on("click", function () {
        var address_id = $("#address_id").val();
        var price = $("#price").val();
        var score = $("#score").val();
        var express_price = $("#express_price").val();
        var wx_price = $("#wx_real_price").val();
        if (price == 0 || wx_price == 0 || address_id == '') {

        }
        if ($(".wx-pay").hasClass("active")) {
            $.ajax({
                url:'/order/pay',
                data:{'address_id':address_id, 'score':score, 'express_price': express_price,
                    'price':price, 'order_no':{{ $orderno }},
                    'coupon_id': $("#coupon_id").val(), 'coupon_discount': $("#discount_price").val()
                    },
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
        } else if ($(".card-pay").hasClass("active")) {
            if (!confirm('确认使用会员卡支付?')) {
                return false;
            }
            $.ajax({
                url:'/order/cardpay',
                data:{'address_id':address_id, 'score':score, 'express_price': express_price,
                    'price': wx_price, 'order_no':{{ $orderno }},
                    'coupon_id': $("#coupon_id").val(), 'coupon_discount': $("#discount_price").val()},
                dataType:'json',
                type:'post',
                success:function(data) {
                if (data.rs == 0) {
                    alert(data.errmsg);
                    return false;
                } else {
                    location.href = "/ordershow?orderno={{ $orderno }}";
                }
            }
        });
        }
    });
</script>
@endsection