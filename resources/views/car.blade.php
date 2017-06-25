@extends('layouts.app')

@section('title', '我的购物车')

@section('content')
    <div class="content car">
        @if (!$cars->isEmpty())
        <div>
            <div class="cars">
                <?php $total = 0; ?>
                @foreach ($cars as $item)
                    <div class="car-item" attr-id="{{ $item->cartid }}">
                        <div class="car-select" attr-value="0">
                            <div class="car-select-type"></div>
                        </div>
                        <div class="car-item-icon" style="background-image:url({{ $item->goodsicon }})"></div>
                        <div class="car-item-content">
                            <div class="car-name">{{ $item->goodsname }}</div>
                            <div class="car-property">
                                <div class="car-price">￥<span class="car-money">{{ $item->price / 100 }}</span> 元</div>
                                <div>{{ $item->property }}</div>
                            </div>
                            <div class="car-num">
                                <div class="reduce_num">-</div>
                                <div><input name="text" value="{{ $item->goodscount }}" class="car-item-count" /></div>
                                <div class="add_num">+</div>
                            </div>
                            <?php $total += $item->price / 100 * $item->goodscount; ?>
                        </div>
                        <br clear="all" />
                    </div>
                @endforeach
            </div>
        </div>
        <div class="car-act">
            <div class="car-act-area">
                <div class="car-act-select-pic"></div>
                <div>全选</div>
            </div>
            <div class="car-count">
                <div>合计: ￥<span id="total_money">0.00</span>元</div>
                <div>满500元包邮</div>
            </div>
            <div class="car-edit">编辑</div>
            <div class="car-buy">结算</div>
        </div>
        <div class="car-act-del">
            <div class="car-act-del-area">
                <div class="car-act-del-select-pic"></div>
                <div>全选</div>
            </div>
            <div class="car-sure">确认</div>
            <div class="car-del">删除</div>
        </div>
        <input type="hidden" id="act-type" value="1" />
        @endif
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
                    location.reload();
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
