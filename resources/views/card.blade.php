@extends('layouts.comm')

@section('title', '会员卡')
@section('card-active', 'active')
@section('content')

    <div class="content card">
        <div class="card_pic" style="background-image:url('{{ empty($card->card_img)? '' : $card->card_img }}')">
            <div class="card_no">卡号:{{ $card_no }}</div>
            <div class="level">
                @if ($level == 1)
                    普通会员
                @elseif ($level == 2)
                    黄金会员
                @elseif ($level == 3)
                    铂金会员
                @elseif ($level == 4)
                    钻石会员
                @endif
            </div>
        </div>
        <div class="card_click">
            <div class="content-block">
                <p id="card_recharge">充值</p>
            </div>
        </div>
        <div class="card_desc">
            <div class="card_desc_title">
                <div class="line"></div><div>会员卡说明</div><div class="line"></div>
            </div>
            <div class="card_desc_content">
                <p>1, 请在结账前出示此卡;</p>
                <p>2, 此卡可享受会员优惠待遇;</p>
                <p>3, 此卡不得够买产品，不得与其他优惠同时使用;</p>
            </div>
        </div>
    </div>
    <div class="popup card-money-select">

    </div>
    <div class="card-select-area">
        <div class="title">
            选择充值金额
            <div class="cancel"></div>
        </div>
        <div class="card-money">
            <div class="card-money-area">
                <!--<div data-value="0.01" class="select-money">0.01元</div>
                <div data-value="0.02" class="select-money">0.02元</div>-->
                <div data-value="2000" class="select-money">2000元<div class="golden_member"></div></div>
                <div data-value="3000" class="select-money">3000元</div>
                <div data-value="4000" class="select-money">4000元</div>
                <div data-value="5000" class="select-money">5000元<div class="platinum_member"></div></div>
                <div data-value="8000" class="select-money">8000元</div>
                <div data-value="10000" class="select-money">10000元<div class="diamond_member"></div></div>
                <div data-value="20000" class="select-money">20000元</div>
                <div data-value="50000" class="select-money">50000元</div>
            </div>
            <div id="select_coupon">使用充值券</div>
        </div>
        <div class="sure_recharge">确认充值</div>
    </div>
    <input type="hidden" id="pay_chance" value="0" />
    <input type="hidden" id="coupon_id" value="0" />
    <input type="hidden" id="money" value="0" />

    @include('layouts.footer')
    <div class="order-coupons-area">
        <div class="order-coupons-select"></div>
        <div class="order-coupons-sure-btn">确 认</div>
    </div>
    <style type="text/css">
        .coupons-item-active{
            border-top:solid 2px yellow;
            border-bottom:solid 2px yellow;
        }
        .order-coupons-area {
            width: 100%;
            height:100%;
            position: absolute;
            z-index: 13000;
            background: #fff;
            display: none;
            top:0;
            left: 0;
        }
        .order-coupons-select {
            background-image:url('/images/coupon.png');
            width: 100%;
            background-repeat:no-repeat;
            background-size: 100% 100%;
            margin-bottom: 0.5rem;
        }
        .coupons-left {
            float:left;
            width:40%;
            color: #fff;
            padding-top: 1rem;
            padding-bottom: 0.5rem;
            height: 7rem;
        }
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
        .golden_member {
            position: absolute;
            width:2.2rem;
            height: 2.3rem;
            top:-0.5rem;
            left: 0.3rem;
            background-image:url('/images/golden_member.png');
            background-repeat: no-repeat;
            background-size: 100% 100%;
        }
        #select_coupon { width:80%; margin: 0 10%;line-height:2rem;
            text-align: center; border:solid 1px #c1c1c1; color:red;}
        .diamond_member {
            position: absolute;
            width:2.2rem;
            height: 2.3rem;
            top:-0.5rem;
            left: 0.3rem;
            background-image:url('/images/diamond_member.png');
            background-repeat: no-repeat;
            background-size: 100% 100%;
        }
        .platinum_member {
            position: absolute;
            width:2.2rem;
            height: 2.3rem;
            top:-0.5rem;
            left: 0.3rem;
            background-image:url('/images/platinum_ member.png');
            background-repeat: no-repeat;
            background-size: 100% 100%;
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
        .card_desc_title:after, .card-money-area:after,.coupons-item:after,.order-coupons-area:after {
            display:block;clear:both;content:"";visibility:hidden;height:0
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
        .coupons-discount-price,.coupons-goods-price {
            margin-left: 12%;
        }
        .coupons-discount-price span:first-child { font-size:1rem; }
        .coupons-discount-price span:last-child {
            font-size: 2rem;
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
                        window.location.href = "/card/forward";
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
    <script type="text/javascript">
        //获取共享地址
        /*function editAddress(address)
        {
            WeixinJSBridge.invoke(
                'editAddress', address, function(res){
                    var value1 = res.proviceFirstStageName;
                    var value2 = res.addressCitySecondStageName;
                    var value3 = res.addressCountiesThirdStageName;
                    var value4 = res.addressDetailInfo;
                    var tel = res.telNumber;

                    alert(value1 + value2 + value3 + value4 + ":" + tel);
                }
            );
        }

        window.onload = function(){
            if (typeof WeixinJSBridge == "undefined"){
                if( document.addEventListener ){
                    document.addEventListener('WeixinJSBridgeReady', editAddress, false);
                }else if (document.attachEvent){
                    document.attachEvent('WeixinJSBridgeReady', editAddress);
                    document.attachEvent('onWeixinJSBridgeReady', editAddress);
                }
            }else{
                editAddress();
            }
        };*/

    </script>
    <script type="text/javascript">
        $.ajaxSettings = $.extend($.ajaxSettings, {
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(function () {
            $(".card-money-select").height($(window).height());
            $(".order-coupons-area").css('height', $(window).height() + 'px');
        });
        $("#card_recharge").on("click", function() {
            $(".card-money-select, .card-select-area").show();
        });
        $(".select-money").on("click", function() {
            $(".select-money").removeClass("active");
            $(this).addClass("active");
            $("#coupon_id").val(0);
            $("#money").val(0);
        })
        $(".sure_recharge").on("click", function() {
            if ($("#pay_chance").val() >= 1) return;
            if ($(".card-money-area").find(".active").length) {
                var money = $(".card-money-area").find(".active").attr("data-value");
                if (money > 0) {
                    $("#pay_chance").val(1);
                    $.ajax({
                        type:'post',
                        data: { 'money' : ''+money, 'coupon_id': $("#coupon_id").val()},
                        dataType:'json',
                        url:'/card/pay',
                        success: function(data) {
                            if (data.rs == 0) {
                                alert(data.errmsg);
                                $("#pay_chance").val(0);
                                return false;
                            }
                            callpay(data);
                        }
                    });
                }
            }
        });
        $(".order-pay-close").on("click", function () {

        });
        $(".order-sure-pay").on("click", function () {

        });
        $(".cancel").on("click", function () {
            $(".card-money-area").removeClass("active");
            $("#pay_chance").val(0);
            $(".card-money-select, .card-select-area").hide();
        });
        $("#select_coupon").on("click", function () {
            console.log($(".select-money").hasClass("active"));
            if ($(".select-money").hasClass("active")) {
                var money = $(".active").attr('data-value');
            } else
                return false;
            console.log(money);
            $.ajax({
                url: '/card/getcoupons',
                type: 'post',
                data: {'money': money},
                dataType: 'json',
                success: function (data) {
                    console.log(data);
                    if (data.coupons == '') {
                        layer.open({
                            content: '此充值无优惠券'
                            ,btn: '确定'
                        });
                    } else {
                        var html = '';
                        $(".order-coupons-select").empty();
                        var coupon_id = $("#coupon_id").val();
                        for (var i in data.coupons) {
                            var coupon = data.coupons[i];
                            console.log(coupon);
                            html += '<div class="coupons-item ';
                            if (coupon_id != 0 && coupon.coupon_id == coupon_id) {
                                html += 'coupons-item-active';
                            }
                            html += '" data-id="'+coupon.id+'">' +
                                '<div class="coupons-left">' +
                                '                    <div class="coupons-discount-price"><span>￥</span><span class="coupons-price">'+coupon.discount_price/100+'</span></div>';
                            if (coupon.goods_price > 0)
                                html +='                    <div class="coupons-goods-price">满￥' + coupon.goods_price/100 +'元可用</div>';
                            html += '                </div>' +
                                '                <div class="coupons-right">' +
                                '                    <div class="coupons-right-1">' +
                                '                        <div>使用范围:</div>' +
                                '                        <div>童马商城余额充值</div>' +
                                '                    </div>' +
                                '                    <div class="coupons-right-2">' +
                                '                        <div>使用期限: </div>';
                            if (coupon.is_sub == 1)
                                html += '                        <div class="coupons-date">无限期</div>';
                            else
                                html += '                        <div class="coupons-date">'+coupon.start_date+'-'+coupon.end_date+'</div>';

                            html += '                    </div>' +
                                '                </div>' +
                                '</div>';
                        }
                        $(".order-coupons-select").append(html);
                        $(".order-coupons-area").show();

                    }
                }
            })
        });
        $(document).on("touchstart", ".coupons-item", function () {
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
        $(".order-coupons-sure-btn").on("click", function () {
            var price = $.trim($(".coupons-item-active").find(".coupons-price").text());
            var coupon_id = $(".coupons-item-active").attr('data-id');
            if (price == '') {
                $(".order-coupons-area").hide();
                $("#coupon_id").val(0);
                $("#money").val(0);
                return false;
            }
            $(".order-coupons-area").hide();
            $("#coupon_id").val($(".coupons-item-active").attr('data-id'));
            $("#money").val(price);
        });
    </script>
@endsection('content')