@extends('layouts.comm')
@section('my-active', 'active')
@section('content')
    <div class="user_index">
        @if ($user)
        <div class="photo">
            <div class="icon" style="background-image:url('{{ $user->avatar }}')"></div>
            <div>{{ $user->uname }}</div>
        </div>
        <div class="user_info">
            <a href="/score">
                <div class="user_info_score">
                    <div>{{ $user->score }}</div>
                    <div>积分</div>
                </div>
            </a>
            <a href="/money">
                <div class="user_info_money">
                    <div>￥{{ $user->money }}</div>
                    <div>账户余额</div>
                </div>
            </a>
            <div class="user_info_center"></div>
            <br clear="all" />
        </div>
        <a href="/order"><div class="order">全部订单</div></a>
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
        <a href="/address"><div class="address">地址管理</div></a>
        <a href="/aboutme"><div class="aboutme">关于我们</div></a>
        <br clear="all" />
        @endif
    </div>
    @include('layouts.footer')
    <style type="text/css">
        .user_index .icon {
            width: 3rem;
            height: 3rem;
            background-repeat:no-repeat;
            border-radius:3rem;
            border: solid 1px #838588;
            margin: 0 auto;
            background-size: 100% 100%;
        }
    </style>
@endsection