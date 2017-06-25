@extends('layouts.app')

@section('title', '会员卡')
@section('card-active', 'active')
@section('content')

    <div class="content card">
        <div class="card_pic">
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
                <p><a href="#" class="button button-big button-round " id="card_recharge">充值 </a></p>
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
            <div class="cancel">x</div>
        </div>
        <div class="card-money">
            <div class="card-money-area">
                <div attr-value="100" class="select-money">100元</div>
                <div attr-value="200" class="select-money">200元</div>
                <div attr-value="500" class="select-money">500元</div>
                <div attr-value="1000" class="select-money">1000元</div>
                <div attr-value="2000" class="select-money">2000元</div>
                <div attr-value="3000" class="select-money">3000元</div>
            </div>
            <br clear="all" />
        </div>
        <div class="sure_recharge">确认充值</div>
    </div>
    <div class="card-wx-pay">
        <div class="title">
            选择支付方式
            <div class="cancel">x</div>
        </div>
        <div class="pay-select">
            <div></div>
            <span>微信支付</span>
        </div>
        <div id="sure_pay">确认付款</div>
    </div>
    @include('layouts.footer')

    <style>
        .page {
            background-color: #fff;}
        .footer { height:3rem;}
    </style>
    <script type="text/javascript">
        $(document).ready(function () {
           $("#card_recharge").on("click", function() {
                //$(".card-money-select").show();
               $.popup(".card-money-select");
               $(".card-select-area").show();
           });
           $(".select-money").on("click", function() {
               $(".select-money").removeClass("active");
               $(this).addClass("active");
           })
           $(".sure_recharge").on("click", function() {
               console.log($(".card-money-area").find(".active"));
               if ($(".card-money-area").find(".active").length) {
                   var money = parseInt($(".card-money-area").find(".active").attr("attr-value"));
                   if (money >= 0) {
                       $(".card-select-area").hide();
                   }
               }
           });
        });
    </script>
@endsection('content')