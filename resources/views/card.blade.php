@extends('layouts.comm')

@section('title', '会员卡')
@section('card-active', 'active')
@section('content')

    <div class="content card">
        <div class="card_pic" style="background-image:url('{{ $card->card_img }}')">
            <div class="card_no">卡号:{{ $card_no }}</div>
            <div class="level">
                @if ($level == 1)
                    黄金会员
                @elseif ($level == 2)
                    铂金会员
                @elseif ($level == 3)
                    钻石会员
                @else
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
                <br clear="all" />
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
                <div data-value="100" class="select-money">100元</div>
                <div data-value="200" class="select-money">200元</div>
                <div data-value="500" class="select-money">500元</div>
                <div data-value="1000" class="select-money">1000元</div>
                <div data-value="2000" class="select-money">2000元</div>
                <div data-value="3000" class="select-money">3000元</div>
            </div>
            <br clear="all" />
        </div>
        <div class="sure_recharge">确认充值</div>
    </div>
    {{--<div class="order-select-type">
        <div class="order-select-title"><!--<div>请选择支付方式</div>--><div class="order-pay-close"></div></div>
        <div class="wx-pay"><div></div><span>微信支付</span></div>
        <div class="order-select-blank"></div>
        <div class="order-sure-pay">确定支付</div>
    </div>--}}
    @include('layouts.footer')
    <style type="text/css">
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
        .order-select-title:after, .wx-pay:after {
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
    </style>
    <script type="text/javascript">
        //调用微信JS api 支付
        function jsApiCall(param)
        {
            WeixinJSBridge.invoke(
                'getBrandWCPayRequest', param,
                function(res){
                    WeixinJSBridge.log(res.err_msg);
                    alert(res.err_code+res.err_desc+res.err_msg);
                }
            );
        }

        function callpay()
        {
            if (typeof WeixinJSBridge == "undefined"){
                if( document.addEventListener ){
                    document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
                }else if (document.attachEvent){
                    document.attachEvent('WeixinJSBridgeReady', jsApiCall);
                    document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
                }
            }else{
                jsApiCall(data);
            }
        }
    </script>
    <script type="text/javascript">
        //获取共享地址
        function editAddress(address)
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
        };

    </script>
    <script type="text/javascript">
        $.ajaxSettings = $.extend($.ajaxSettings, {
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(function () {
            $(".card-money-select").height($(window).height());
        });
        $("#card_recharge").on("click", function() {
            $(".card-money-select, .card-select-area").show();
        });
        $(".select-money").on("click", function() {
            $(".select-money").removeClass("active");
            $(this).addClass("active");
        })
        $(".sure_recharge").on("click", function() {
            console.log($(".card-money-area").find(".active"));
            if ($(".card-money-area").find(".active").length) {
                var money = parseInt($(".card-money-area").find(".active").attr("data-value"));
                if (money >= 0) {
                    $.ajax({
                        type:'json',
                        data: { 'money' : money},
                        dataType:'json',
                        url:'/admin/member/pay',
                        async: false,
                        success: function(data) {
                            if (data.rs == 0) {
                                alert(data.errmsg);
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
    </script>
@endsection('content')