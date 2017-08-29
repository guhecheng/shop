@extends('layouts.comm')

@section('title', '童马儿童在线商城')
@section('index-active', 'active')
@section('content')
    <div class="index">
        @if (!$brands->isEmpty())
            @foreach ($brands as $brand)
                <a href="/type?brand_id={{ $brand->id }}">
                <div class="brand-item" style="background-image:url('{{ $brand->brand_img }}')"></div>
                </a>
            @endforeach
        @endif
    </div>
    @include('layouts.footer')
    <style type="text/css">
        .brand-item {
            width:100%; height: 5rem; margin-bottom: 0.5rem;
            background-repeat:no-repeat;
            background-size: 100% 100%;
        }
        .index { margin-bottom: 3rem; overflow-y: auto; }
    </style>
@endsection
