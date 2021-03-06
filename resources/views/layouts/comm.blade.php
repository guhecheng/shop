<!doctype html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    <title>@yield('title')</title>
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="/css/home.css">
    <link rel="stylesheet" href="/css/common.css">
    <link rel="stylesheet" href="/layer_mobile/need/layer.css">
    <script src="/js/jquery.js"></script>
    <script src="/layer_mobile/layer.js"></script>
</head>
<body>
@yield('content')

</body>
</html>