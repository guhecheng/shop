<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">

    <title>余额明细</title>

    <script type="text/javascript" src="/js/scroll.js"></script>
    <script type="text/javascript" src="/js/jquery.js"></script>

    <style type="text/css">
        * {
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }

        html {
            -ms-touch-action: none;
        }

        body,ul,li {
            padding: 0;
            margin: 0;
            border: 0;
        }

        body {
            font-size: 12px;
            font-family: ubuntu, helvetica, arial;
        }

        #wrapper {
            width: 100%;
            background: #F2F2F2;
            overflow: auto;
        }

        #scroller {
            -webkit-tap-highlight-color: rgba(0,0,0,0);
            width: 100%;
            -webkit-transform: translateZ(0);
            -moz-transform: translateZ(0);
            -ms-transform: translateZ(0);
            -o-transform: translateZ(0);
            transform: translateZ(0);
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            -webkit-text-size-adjust: none;
            -moz-text-size-adjust: none;
            -ms-text-size-adjust: none;
            -o-text-size-adjust: none;
            text-size-adjust: none;
        }

        #scroller ul {
            list-style: none;
            padding: 0;
            margin: 0;
            width: 100%;
            text-align: left;
        }

        #scroller li {
            padding: 0.1rem 0.8rem;
            margin-bottom:0.4rem;
            border-bottom: 1px solid #ccc;
            border-top: 1px solid #fff;
            background-color: #fff;
            font-size: 1rem;
            height: 4.5rem;
        }
        #scroller .trans_name, .trans_time { float:left;}
        #scroller .trans_money, .trans_no { float:right;}
        #scroller .name { color: #000000; height:2.2rem; line-height:2.2rem;  font-weight: 600}
        #scroller .time { height:2.2rem; line-height:2.2rem;}
    </style>
</head>
<body>
@if (!empty($money))
<div id="wrapper">
    <div id="scroller">
        <ul>
            @foreach ($money as $value)
            <li>
                <div class="name">
                    <div class="trans_name">@if ($value->trans_type == 0) 购物 @elseif ($value->trans_type == 1) 充值 @elseif ($value->trans_type == 2) 后台充值 @endif</div>
                    <div class="trans_money">{{ empty($value->trans_type) ? '-' : '' }}{{  $value->trans_money / 100 }}元</div>
                    <br clear="all" />
                </div>
                <div class="time">
                    <div class="trans_time">{{ $value->create_time }}</div>
                    <div class="trans_no">{{ empty($value->order_no) ? '' : '订单号' . $value->order_no }}</div>
                    <br clear="all" />
                </div>
                <br clear="all" />
            </li>
            @endforeach
        </ul>
        <input type="hidden" id="page" value="0" />
    </div>
</div>
<script>
    window.onscroll = function() {
        console.log('jklfasdf');
        if (getScrollTop() + getClientHeight() == getScrollHeight()) {
            console.log('jfklajfda');
            var page = parseInt($("#page").val());
            flag = false;
            addtrans(page + 1);
        }
    }

    function addtrans( page ) {
        $.ajax({
            url: '/money',
            data: {'page': page},
            dataType:'json',
            type:'get',
            success:function(data) {
                console.log(data.money.data);
                if (data.money.data != '') {
                    $("#page").val(page);
                    for (var i in data.money.data) {
                        money = data.money.data[i];
                        var li = '<li><div class="name"><div class="trans_name">';
                        li += money.trans_type == 1 ? '充值' : (money.trans_type==2 ? '后台充值' : '购物');
                        li += '</div><div class="trans_money">' + (money.trans_type==1 ? '':'-') + (money.trans_money / 100) + '元</div>';
                        li += '<br clear="all" /></div><div class="time">';
                        li += '<div class="trans_time">'+money.create_time+'</div>';
                        li += '<div class="trans_no">' + (money.order_no == 0 ? '' : '订单号'+ money.order_no) +  '</div>';
                        li += '<br clear="all" /></div><br clear="all" /></li>';
                        $("ul").append(li);
                    }
                }
            }
        });
    }
</script>
@endif
</body>
</html>