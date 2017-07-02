@extends('layouts.app')

@section('title', '订单详情')

@section('content')
    <div class="order-create">
        <div>
            <div>

                @if ($order->status <= 1)
                    <div class="order-state">
                        <div class="order-state-img" style="background-image:url('/images/wait_pay.png')"></div>
                        <div>订单未支付</div>
                    </div>
                @elseif ($order->status == 2 || $order->status == 3)
                    <div class="order-state">
                        <div class="order-state-img" style="background-image:url('/images/has_pay.png')"></div>
                        <div>支付成功，等待发货</div>
                    </div>
                @elseif ($order->status == 4 )
                    <div class="express-state" style="padding:0.2rem 5%; ">
                        <div class="express" style="margin: 0; width: 50%; margin-right:10%;  ">
                            <div class="express-company" style="display: block;">
                                <div style="float:left;">已发货</div>
                                <div style="float:right;line-height:1rem;font-weight: 400">{{ $order->express_company }}</div>
                                <br clear="all" />
                            </div>
                            <div style="display: block; line-height: 0.8rem;">快递单号: {{ $order->express_no }}</div>
                            <div id="express_copy" style="display: block;">复制</div>
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
            <div>10元(全场满500元包邮)</div>
        </div>
        <div class="order-discount">
            <div>会员折扣</div>
            @if ($user->level >= 1)
                <div>
                    @if ($user->level == 1)
                        金牌会员: 9折
                    @elseif ($user->level == 2)
                        铂金会员: 8.5折
                    @elseif ($user->level == 3)
                        钻石会员: 8折
                    @else
                    @endif
                </div>
            @else
                <div></div>
            @endif
        </div>
    </div>

    <div class="order-show-act">
        <div class="order-show-area">
            <div class="order-show-count">
                共计<?php echo $count; ?>件商品
            </div>
            <div class="order-show-total">
                <div>合计: <span id="total_money">￥{{ $order->price }}</span>元</div>
            </div>
            <br clear="all" />
        </div>
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
    @if ($order->status <= 1)
        <div class="order-to-buy">立即付款</div>
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
            .order-to-buy { position:fixed; bottom:0; width: 100%;
                line-height:1.5rem; font-size:0.7rem;text-align: center; background:#fff; }
            #total_money { color: red; }
            .express-image { background-size:100%; width:2rem;
                height:2rem; margin-top:0.4rem;}
            .express  { float:left;}
            .express-image { float:right; }
            #express_copy { width:2.2rem; height:1rem;border:solid 1px #C1C1C1;border-radius:0.8rem;
                text-align: center;}
        </style>
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
                    url: '/order/pay',
                    success: function (data) {

                    }
                });
            });
            $("#express_copy").on("click", function() {

            });
        </script>
@endsection