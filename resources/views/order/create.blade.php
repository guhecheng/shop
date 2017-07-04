@extends('layouts.app')

@section('title', '我的购物车')

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
                    <?php $total = $count = 0; ?>
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
                            </div>
                            <br clear="all" />
                        </div>
                    @endforeach
                </div>
                <div class="order-express">
                    <div>运费</div>
                    <div>10元@if($user->level<1)(全场满1000元包邮)@endif</div>
                </div>
                @if ($user->level >= 1)
                <div class="order-express">
                    <div>会员卡运费减免</div>
                    <div>-￥10元</div>
                </div>
                @if ($order->discount)
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
                                @if ($order->discount_price)
                                    <span style="color:red;">￥-{{ $order->discount_price }}</span>
                                @endif
                            </div>
                    </div>
                @endif
                @endif
                @if ($user->score)
                    <div>
                        <div>积分折扣</div>
                        <div>{{ $user->score }}积分抵扣{{ $user->score / 100 }}元</div>
                    </div>
                @endif
            </div>
            {{  }}
            <div></div>
            <div class="order-act">
                <div class="order-count">
                    共计<?php echo $count; ?>件商品
                </div>
                <div class="order-total">
                    <div>合计: ￥<span id="total_money">{{ $order->price }}
                        </span>元</div>
                </div>
                <div class="order-buy">付款</div>
            </div>
    </div>
    <style type="text/css">
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
    </style>
    <script>
        $(function() {
            {{--@if ($address)
                $(".order-address").height($(".order-address-info").height());
            @else
                $(".order-address").height($(".order-address-no-default").height());
            @endif--}}
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
        $(".order-buy").on('click', function () {
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
         })
    </script>
@endsection
