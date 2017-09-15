@extends('layouts.comm')
@section('title', '我的信息')
@section('my-active', 'active')
@section('content')
    <div class="user_index">
        @if ($user)
        <div class="photo">
            <a href="/info">
            <div class="icon" style="background-image:url('{{ $user->avatar }}')"></div>
            </a>
            <div>{{ $user->uname }}</div>
            @if ($user->level == 2)
            <div class="user-level" style="background-image:url('/images/golden_member.png')"></div>
            @elseif ($user->level == 3)
            <div class="user-level" style="background-image:url('/images/platinum_ member.png')"></div>
            @elseif ($user->level == 4)
            <div class="user-level" style="background-image:url('/images/diamond_member.png')"></div>
            @elseif ($user->level == 1)
            <div class="user-level" style="background-image:url('/images/ordinary_member.png')"></div>
            @endif
        </div>
        <div class="user_info">
            <a href="/score">
                <div class="user_info_score">
                    <div>{{ $user->score }}</div>
                    <div>积分</div>
                    <div>Points</div>
                </div>
            </a>
            <a href="/money">
                <div class="user_info_money">
                    <div>￥{{ $user->money / 100 }}元</div>
                    <div>账户余额</div>
                    <div>Balance</div>
                </div>
            </a>
            <div class="user_info_center"></div>
            <br clear="all" />
        </div>
        <a href="/order"><div class="order"><div>全部订单</div><div>All Orders</div></div></a>
        <div class="order-state-list">
            <a href="/order?status=1">
                <div class="order-state-list-item">
                    <div class="order-state-list-wait-pay"></div>
                    <div>待付款</div>
                </div>
            </a>
            <a href="/order?status=2">
                <div class="order-state-list-item">
                    <div class="order-state-list-wait-send"></div>
                    <div>待发货</div>
                </div>
            </a>
            <a href="/order?status=3">
                <div class="order-state-list-item">
                    <div class="order-state-list-wait-save"></div>
                    <div>待收货</div>
                </div>
            </a>
            <br clear="both" />
        </div>
        <a href="/address"><div class="address"><div>地址管理</div><div>Address Management</div></div></a>
        <a href="/coupon"><div class="coupon"><div>我的券包</div><div>My Coupon</div></div></a>
        <a href="#"><div class="address"><div>我的足迹</div><div>My Activities</div></div></a>
        <a href="/customer"><div class="address"><div>咨询客服</div><div>Customer Service</div></div></a>
        <!--<a href="/aboutme"><div class="aboutme"><div>关于我们</div><div>About Us</div></div></a>-->
        <a href="http://mp.weixin.qq.com/s?__biz=MzIxMDgzMzY3MA==&mid=100000222&idx=1&sn=0e1a2c28e3bf7c68f137ca08bca2049a&chksm=175fd9b7202850a170c065204f0f0033593641a89cbd68cef53f345032d91c551bb51a9cb98a#rd"><div class="aboutme"><div>关于我们</div><div>About Us</div></div></a>
        <br clear="all" />
        @endif
    </div>
    @include('layouts.footer')
    <style type="text/css">
        .user_index { margin-bottom:2rem; }
        .user_index .icon {
            width: 3rem;
            height: 3rem;
            background-repeat:no-repeat;
            border-radius:3rem;
            border: solid 1px #838588;
            margin: 0 auto;
            background-size: 100% 100%;
        }
        .user-level {
            width: 2.2rem;
            height: 2rem;
            -webkit-background-size: 100% 100%;
            background-size: 100% 100%;
            background-repeat:no-repeat;
            margin: 0 auto;
        }
        .order div:first-child { float:left; }
        .order div:last-child { float:right; }
    </style>
@endsection