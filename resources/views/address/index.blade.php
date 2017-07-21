@extends('layouts.app')

@section('title', '地址管理')

@section('content')
    <div class="address-list">
        <ul class="address-list-ul">
            @foreach( $addresses as $address)
            <li>
                <div class="address-list-content">
                    <div class="address-list-li-title"><div>{{ $address->name }}</div> <div>{{ $address->phone }}</div></div>
                    <div class="address-list-location">{{ $address->location }}</div>
                </div>
                <div class="address-list-default">
                    <input type="hidden" class="address_id" value="{{ $address->address_id }}" />
                    <div class="address-list-select-act" style="margin-top:0;" data-id="{{ $address->address_id }}" >
                        <div class="address-list-select {{ empty($address->is_default) ? '' : 'active' }}"></div><div>设为默认地址</div>
                    </div>
                    <div class="address-del" data-id="{{ $address->address_id }}" style="float:right;width:20%;text-align:right;">
                        删除
                    </div>
                </div>
                <br clear="all" />
            </li>
            @endforeach
        </ul>
    </div>
    <div class="address-add-btn" style="background:#c1c1c1;font-size:0.8rem;color: #000;font-weight: bold;">添加地址</div>
    <script type="text/javascript">
        $(function() {
            $(".address-add-btn").on("click", function () {
                location.href = "/address/create";
            });
            $(".page").css("overflow-y", "scroll");
            $(".address-list-select-act").on("click", function (evt) {
                evt.stopPropagation();
                var flag = 0;
                if ($(this).find(".address-list-select").is('.active')) {
                    $(this).find(".address-list-select").removeClass("active");
                } else {
                    $(".address-list-default").find(".address-list-select").removeClass("active");
                    $(this).find(".address-list-select").addClass("active");
                    flag = 1;
                }
                $.ajax( {
                    data: { 'address_id' : $(this).attr('data-id'), 'flag': flag },
                    dataType: 'json',
                    url: '/address/setdefault',
                    type: 'post',
                    success: function(data) {
                        if (data == '') {

                        }
                    }
                })
            });
            $(".address-list-ul").find("li").on("click", function() {
                var address_id = $(this).find(".address_id").val();
                @if ($from_order)
                    location.href = "/orderpay?orderno={{ $orderno }}&address_id="+address_id;
                @else
                    if (address_id)
                        location.href = "/address/"+ address_id +"/edit" ;
                @endif
            });
            $(".address-del").on("click", function (evt) {
                evt.stopPropagation();

                if (parseInt($(this).attr("data-id")) != '') {
                    var th = $(this);
                    $.ajax({
                        type:'get',
                        url: '/address/del',
                        data: {'address_id':parseInt($(this).attr("data-id"))},
                        dataType:'json',
                        success: function (data) {
                            if (data.rs == 1) {
                                th.parent().parent().remove();
                            }
                        }
                    })
                }
            });
        })
    </script>
@endsection