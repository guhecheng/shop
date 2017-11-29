@extends('layouts.comm')

@section('title', '客服二维码')
@section('card-active', 'active')
@section('content')
    <div class="qrcode_area">
        <img src="{{ $qrcode_img }}" class="qrcode" />
    </div>
    <style type="text/css">
        .qrcode { width: 15rem;margin: 8rem auto 0; height: 15rem; display: block; }
    </style>
@endsection('content')