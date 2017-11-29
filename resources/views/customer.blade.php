@extends('layouts.comm')

@section('title', '客服信息')
@section('card-active', 'active')
@section('content')
    <div class="customer">
        <a href="/customer/qrcode?type=1"><div>T100</div></a>
        <a href="/customer/qrcode?type=2"><div>江南布衣 jnby by JNBY</div></a>
        <div>小云朵 Moimoln</div>
        <a href="/customer/qrcode?type=4"><div>太平鸟 Mini Peace</div></a>
    </div>
    <style type="text/css">
        .customer div {
            line-height: 2.5rem;
            padding:0 10%;
            margin-bottom: 0.5rem;
            background-color: #fff;
        }
    </style>
@endsection('content')