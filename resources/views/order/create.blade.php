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
                        <div class="order-item" attr-id="{{ $item->cartid }}">
                            <div class="order-item-icon" style="background-image:url({{ $item->goodsicon }})"></div>
                            <div class="order-item-content">
                                <div class="order-item-name">{{ $item->goodsname }}</div>
                                <div class="order-property">
                                    <div class="order-price">￥<span class="order-money">{{ $item->price / 100 }}</span> 元</div>
                                </div>
                                <div class="order-item-num">
                                    <div>{{ $item->property }}</div>
                                    <div>数量: <span class="order-item-count">{{ $item->goodscount }}</span></div>
                                </div>
                                <?php $total += $item->price / 100 * $item->goodscount; $count += $item->goodscount;?>
                            </div>
                            <br clear="all" />
                            <input type="hidden" name="goodsid[]" class="goodsid" value="{{ $item->goodsid }}" />
                            <input type="hidden" name="cartid[]" class="cartid" value="{{ $item->cartid }}" />
                            <input type="hidden" name="skuid[]" class="skuid" value="{{ $item->skuid }}" />
                            <input type="hidden" name="num[]" class="num" value="{{ $item->goodscount }}" />
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
            <div class="order-act">
                <div class="order-count">
                    共计<?php echo $count; ?>件商品
                </div>
                <div class="order-total">
                    <div>合计: ￥<span id="total_money">
                            @if ($user->level == 1)
                                <?php echo $total * 0.9; ?>
                            @elseif ($user->level == 2)
                                <?php echo $total * 0.85; ?>
                            @elseif ($user->level == 3)
                                <?php echo $total * 0.8; ?>
                            @else
                            @endif
                        </span>元</div>
                </div>
                <div class="order-buy">付款</div>
            </div>
    </div>
    <style type="text/css">
        .order-address-no-default {
            float:left;
            width: 80%;
            font-size: 0.8rem;
            padding: 0.6rem 10%;
        }
    </style>
    <script>
        $(function() {
            @if ($address)
                $(".order-address").height($(".order-address-info").height());
            @else
                $(".order-address").height($(".order-address-no-default").height());
            @endif
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
            location.href = "/address?from_order=1";
        });
        $(".order-buy").on('click', function () {
            var address_id = $("#address_id").val();
            if (address_id == '') {
                alert('地址没有填写');
                return false;
            }
            var cartid = [], goodsid = [], skuid = [], num = [];
            $(".skuid").each(function(item) {
                skuid[item] = $(this).val();
            });
            $(".goodsid").each(function(item) {
                goodsid[item] = $(this).val();
            });
            $(".cartid").each(function(item) {
                cartid[item] = $(this).val();
            });
            $(".num").each(function(item) {
                num[item] = $(this).val();
            });
            console.log(skuid);
            $.ajax({
                type:'post',
                data: {'address_id': address_id,
                       'goodsid' : goodsid,
                        'cartid': cartid,
                        'num': num,
                        'skuid': skuid
                        },
                dataType:'json',
                url: '/order/add',
                success: function (data) {

                }
            });
         })
    </script>
@endsection
