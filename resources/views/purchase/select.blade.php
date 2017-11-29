@extends('layouts.comm')

@section('title', '童马儿童代购商城')
@section('content')
    <div class="purchase_select">
        <a href="/type?brand_id={{ $brand_id }}"><img src="/images/purchase_comm_shop.png" /></a>
        <img id="purchase_vip" src="/images/purchase_vip_shop.png" />
    </div>
    <style type="text/css">
        .purchase_select img {
            display:block;
            width: 76%;
            margin: 5rem auto;
            height: 8rem;
            border: solid 1px #c1c1c1;
        }
    </style>
    <script type="text/javascript">
        var level = {{ $level->level }}
        $("#purchase_vip").on("touchstart", function () {
            if (level < 3) {
                layer.open({
                    content: '你的会员等级不够'
                    ,btn: '确定'
                });
                return false;
            }
            else
                location.href = "/purchase/goods";
        });
    </script>
@endsection