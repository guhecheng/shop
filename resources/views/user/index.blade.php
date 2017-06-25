@extends('layouts.comm')
@section('my-active', 'active')
@section('content')
    <div class="user_index">
        <div class="photo">
            <div class="icon" style="background-image:url('{{ $user->icon }}')"></div>
            <div>{{ $user->uname }}</div>
        </div>
        <div class=""></div>
        <div class="order">全部订单</div>
        <div class=""></div>
        <div class="address">地址管理</div>
        <div class="aboutme">关于我们</div>
        <br clear="all" />
    </div>
    @include('layouts.footer')
@endsection