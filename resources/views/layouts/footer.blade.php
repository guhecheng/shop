<footer>
    <div class="footer">
        <a href="/"><div class="footer-shop @yield('index-active')">商城</div></a>
        <a href="/card"><div class="footer-card @yield('card-active')">会员卡</div></a>
        <a href="/my"><div class="footer-my @yield('my-active')">我的</div></a>
    </div>
    <style type="text/css">
        .footer {
            position: absolute;
            bottom: 0;
            width:100%;
            text-align: center;
            font-weight:bold;
            border-top: solid 1px #C1C1C1;
            background-color: #F2F2F2;
        }
        .footer div {
            line-height:3rem;
            float:left;
            width: 33%;
        }
        .footer-card {
            border-left : solid 1px #C1C1C1;
            border-right: solid 1px #C1C1C1;
        }
        .footer div.active {
            color: red;
        }
    </style>
</footer>