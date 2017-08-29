<footer>
    <div class="footer">
        <a href="/">
            <div class="footer-shop @yield('index-active')"><div>童马商城</div><div>TM Mall</div></div>
        </a>
        <a href="/card">
            <div class="footer-card @yield('card-active')"><div>会员卡</div><div>VIP Card</div></div>
        </a>
        <a href="/my">
            <div class="footer-my @yield('my-active')"><div>我的信息</div><div>Information</div></div>
        </a>
    </div>
    <style type="text/css">
        .footer {
            position: fixed;
            bottom: 0;
            width:100%;
            text-align: center;
            font-weight:bold;
            border-top: solid 1px #C1C1C1;
            background-color: #F2F2F2;
            z-index:100;
        }
        .footer-card, .footer-shop, .footer-my {
            float:left;
            width: 33%;
            height: 3rem;
            padding-top: 0.5rem;
        }
        .footer-card div, .footer-shop div, .footer-my div {
            line-height: 1rem;
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