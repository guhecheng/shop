@extends('layouts.comm')

@section('title', '客服信息')
@section('card-active', 'active')
@section('content')
    <div class="customer">
        <div>T100客服</div>
        <div>江南布衣jnby by JNBY客服</div>
        <div>小云朵Moimoln客服</div>
        <div>太平鸟客服</div>
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