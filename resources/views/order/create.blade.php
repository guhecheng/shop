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
                    <div>选择地址</div>
                    @endif
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

    <script>
        $.ajaxSettings = $.extend($.ajaxSettings, {
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(".goods-item").on("click", function() {
            location.href = "/goods?goodsid=" + $(this).attr("attr-id");
        });
        $(".car-act-area").on('click', function() {

        });
        $(".car-select").on("click", function () {
            if ($("#act-type").val() == 1) {
                var money = '';
                if ($(this).attr("attr-value") == 1) {
                    $(this).find("div").removeClass("active");
                    $(this).attr("attr-value", 0);
                    var par = $(this).parent();
                    var money = $.trim(par.find(".car-money").text()) * $.trim(par.find(".car-item-count").val());
                    if ($(".car-act-select-pic").hasClass('isactive')) {
                        $(".car-act-select-pic").removeClass("isactive");
                    }
                    $("#total_money").text(parseFloat($.trim($("#total_money").text())) - parseFloat(money));
                } else {
                    $(this).find("div").addClass("active");
                    $(this).attr("attr-value", 1);
                    if ($(".active").length == $(".car-item").length) {
                        $(".car-act-select-pic").addClass("isactive");
                    }
                    var par = $(this).parent();
                    var money = parseFloat($.trim(par.find(".car-money").text()) * $.trim(par.find(".car-item-count").val()));
                    $("#total_money").text(parseFloat($.trim($("#total_money").text())) + parseFloat(money));
                }
            } else {
                if ($(this).attr("attr-value") == 1) {
                    $(this).find("div").removeClass("active");
                    $(this).attr("attr-value", 0);
                    var par = $(this).parent();
                    if ($(".car-act-del-select-pic").hasClass('isactive')) {
                        $(".car-act-del-select-pic").removeClass("isactive");
                    }
                } else {
                    $(this).find("div").addClass("active");
                    $(this).attr("attr-value", 1);
                    if ($(".active").length == $(".car-item").length) {
                        $(".car-act-del-select-pic").addClass("isactive");
                    }
                }
            }
        })
        $(".car-act-area").on("click", function() {
            if ($(".car-act-select-pic").hasClass('isactive')) {
                $(".car-act-select-pic").removeClass("isactive");
                $(".active").removeClass("active");
                $("#total_money").text("0.00");
            } else {
                $(".car-act-select-pic").addClass("isactive");
                $(".car-select-type").addClass("active");
                var money = 0;
                $(".car-money").each(function(item) {
                    var that = $(this);
                    $(".car-item-count").each(function(index) {
                        if (item == index)
                            money += $.trim(that.text()) * parseInt($(this).val());
                    });
                });
                $("#total_money").text(money);
            }
        });
        $(".car-act-del-area").on("click", function() {
            if ($(".car-act-del-select-pic").hasClass('isactive')) {
                $(".car-act-del-select-pic").removeClass("isactive");
                $(".active").removeClass("active");
            } else {
                $(".car-act-del-select-pic").addClass("isactive");
                $(".car-select-type").addClass("active");
            }
        });
        $(".reduce_num").on('click', function() {
            var par = $(this).parent();
            var count = par.find('.car-item-count').val();
            par.find('.car-item-count').val(count > 1 ? count - 1 : 1);
        });
        $(".add_num").on('click', function() {
            var par = $(this).parent();
            var count = par.find('.car-item-count').val();
            par.find('.car-item-count').val(parseInt(count) + 1);
        });
        $(".car-edit").on("click", function() {
            $("#act-type").val(2);
            $(".car-act").hide();
            $(".car-select").find("div").removeClass("active");
            $(".car-select").attr("attr-value", 0);
            $(".car-act-del").show();
        });
        $(".car-sure").on("click", function() {
            $("#act-type").val(1);
            $(".car-act").show();
            $(".car-select").attr("attr-value", 0);
            $(".car-select").find("div").removeClass("active");
            $(".car-act-del").hide();
        });
        $(".car-del").on("click", function() {
            if (!confirm('确认删除?')) return;
            var car_ids = [];
            $(".active").each(function(item) {
                car_ids[item] = $(this).parent().parent().attr("attr-id");
            });
            $.ajax({
                url:'/car/delcar',
                type:'post',
                data: {'car_ids': car_ids},
                dataType: 'json',
                success: function(data) {
                    //location.reload();
                }
            })
        });
        $(".car-buy").on("click", function() {
            var car_ids = '';
            $(".active").each(function(item) {
                car_ids += $(this).parent().parent().attr("attr-id") + ',';
            });
            location.href = '/order/create?car_ids=' + car_ids;
        });
    </script>
@endsection
