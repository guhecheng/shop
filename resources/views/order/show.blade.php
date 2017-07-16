@extends('layouts.app')

@section('title', '订单详情')

@section('content')
    <div class="order-create">
        <div>
            <div>

                @if ($order->status == 1)
                    <div class="order-state">
                        <div class="order-state-img" style="background-image:url('/images/wait_pay.png')"></div>
                        <div>订单未支付</div>
                    </div>
                @elseif ($order->status == 2)
                    <div class="order-state">
                        <div class="order-state-img" style="background-image:url('/images/has_pay.png')"></div>
                        <div>支付成功，等待发货</div>
                    </div>
                @elseif ($order->status == 3)
                    <div class="express-state" style="padding:0.2rem 5%; ">
                        <div class="express" style="margin: 0; width: 50%; margin-right:10%;  ">
                            <div class="express-company" style="display: block;">
                                <div style="float:left;">已发货</div>
                                <div style="float:right;line-height:1rem;font-weight: 400">{{ $order->express_company }}</div>
                                <br clear="all" />
                            </div>
                            <div style="display: block; line-height: 0.8rem;">快递单号: {{ $order->express_no }}</div>
                            <!--<div id="express_copy" style="display: block;">复制</div>-->
                        </div>
                        <div style="background-image:url('/images/has_send.png');" class="express-image"></div>
                        <br clear="all" />
                    </div>
                @elseif ($order->status == 6)
                    <div class="order-state">
                        <div class="order-state-img" style="background-image:url('/images/no_goods.png')"></div>
                        <div>订单未及时支付，已过期</div>
                    </div>
                @else
                @endif
            </div>
        </div>
        <div class="order-address">
            <div class="order-address-info">
                <div class="order-address-info-title"><div>{{ $order->recv_name }}</div><div>电话: {{ $order->phone }}</div></div>
                <div class="order-address-info-location">{{ $order->location }}</div>
            </div>
            <br clear="all" />
        </div>
        <div class="order-list">
            <?php $total = $count = 0; ?>
            @foreach ($goods as $item)
                <div class="order-item">
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
                    </div>
                    <br clear="all" />
                </div>
            @endforeach
        </div>
        <div class="order-express">
            <div>运费</div>
            <div>10元(全场满1000元包邮)</div>
        </div>
        @if (empty($order->express_price))
        <div class="order-express">
            <div>运费减免</div>
            <div>-￥10元</div>
        </div>
        @endif
        @if (!empty($order->discount_price))
        <div class="order-discount">
            <div>会员折扣</div>
            <div style="float:right;">
            @if ($order->discount == 90)
                金牌会员: 9折
            @elseif ($order->discount == 85)
                铂金会员: 8.5折
            @elseif ($order->discount == 80)
                钻石会员: 8折
            @else
            @endif
                <div style="color:red;float:right;">￥-{{ $order->discount_price }}元</div>
            </div>
        </div>
        @endif
        @if (!empty($order->exchange_score))
            <div class="order-discount">
                <div>积分折扣</div>
                <div style="float:right;">
                    {{ $order->exchange_score }}积分折扣{{ $order->exchange_score / 100 }}元
                </div>
            </div>
        @endif
    </div>

    <div class="order-show-act">
        <div class="order-show-area">
            <div class="order-show-count">
                共计<?php echo $count; ?>件商品
            </div>
            <div class="order-show-total">
                <div>合计: <span id="total_money">￥{{ $order->price / 100}}</span>元</div>
            </div>
            <br clear="all" />
        </div>
        @if (intval($order->price / 100))
        <div class="order-get-score">
            <div style="float: left;">获得积分</div>
            <div style="float: right;">{{ $order->price / 100 }}分</div>
            <br clear="all" />
        </div>
        @endif
        <div class="order-show-time">
            <div>订单号: {{ $order->order_no }}</div>
            <div>下单时间: {{ $order->create_time }}</div>
            @if ($order->pay_time)
                <div>付款时间: {{ $order->pay_time }}</div>
            @endif
            @if ($order->cancel_time)
                <div>取消时间: {{ $order->cancel_time }}</div>
            @endif
        </div>
        <br clear="all" />
    </div>
    @if ($order->status == 1)
        <div class="order-to-buy">立即付款</div>
    @endif
    @if ($order->status == 3)
    <div class="order-sure-save">确认收货</div>
    @endif
        </div>

        <style type="text/css">
            .express-state {
                background:#fff;
            }
            .order-state {
                background:#fff;
                width:100%;
                height: 4rem;
                padding: 0 10%;
            }
            .order-state div { float:left; margin-top: 1rem;}
            .order-state div:nth-child(2) { line-height: 2rem;
                font-weight:bold; font-size: 0.7rem;}
            .order-state-img {
                width: 2rem;
                height:2rem;
                background-repeat:no-repeat;
                background-size: 100% 100%;
                margin-right: 1.2rem;
                margin-left: 2.5rem;
            }
            .order-address-no-default {
                float:left;
                width: 80%;
                font-size: 0.8rem;
                padding: 0.6rem 10%;
            }
            .order-address-info {
                width: 100%;
                padding: 0 5%;
            }
            .order-address {
                margin-top:0.2rem;
                min-height: 2.5rem;
            }
            .order-act {  display: block; position: relative; margin-top: 0.5rem;}
            .order-show-total { float:right; }
            .order-show-count { float:left; }
            .order-show-area {
                width:100%;}
            .order-show-act {  background: #fff; margin-top:0.5rem; width: 100%; padding:0 5%;}
            .order-show-time { margin-top: 1rem;
                margin-bottom:1rem;}
            .order-show-time div { line-height: 1rem; }
            .order-to-buy, .order-sure-save{ position:fixed; bottom:0; width: 100%;
                line-height:2rem; font-size:1rem;text-align: center; background:#fff; }
            #total_money { color: red; }
            .express-image { background-size:100%; width:2rem;
                height:2rem; margin-top:0.4rem;}
            .express  { float:left;}
            .express-image { float:right; }
            #express_copy { width:2.2rem; height:1rem;border:solid 1px #C1C1C1;border-radius:0.8rem;
                text-align: center;}
            .order-item-content {
                width: 74%;
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
                            location.href = "/ordershow?orderno={{ $order->order_no }}";
                        } else {
                            //$("#pay_chance").val(0);
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

            $(".order-to-buy").on('click', function () {
                $.ajax({
                    type:'post',
                    data: {'orderno':{{ $order->order_no }} },
                    dataType:'json',
                    url: '/order/repay',
                    async: false,
                    success: function (data) {
                        if (data.rs == 0) {
                            alert(data.errmsg);
                            return false;
                        } else {
                            callpay(data);
                        }
                    }
                });
            });
            $(".order-sure-save").on("click", function() {
                $.ajax({
                    type:'post',
                    data: {'orderno':{{ $order->order_no }}, 'status': 4 },
                    dataType:'json',
                        url: '/order/changeorder',
                        async: false,
                        success: function (data) {
                        if (data.rs == 0) {
                            alert(data.errmsg);
                            return false;
                        } else {
                            location.reload();
                        }
                    }
                });
            });
            /*$("#express_copy").on("click", function() {

            });*/
        </script>
@endsection
