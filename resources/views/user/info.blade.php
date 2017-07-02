@extends('layouts.app')
@section('content')
    <div class="content user-info">
        <div class="user-info-item">
            <div>我是老会员</div>
            <div class="user-info-link link-old">点击关联<div></div></div>
        </div>
        <div class="user-info-icon-item">
            <div>头像</div>
            <div class="user-info-icon-div2"><div class="user-info-icon"></div><div></div></div>
        </div>
        <div class="user-info-item">
            <div>姓名</div>
            <div class="user-info-link">点击关联<div></div></div>
        </div>
        <div class="user-info-item">
            <div>手机号码</div>
            <div class="user-info-link">点击关联<div></div></div>
        </div>
        <div class="user-info-children">孩子信息</div>
        <div class="user-info-item">
            <div>姓名</div>
            <div class="user-info-link">点击关联<div></div></div>
        </div>
        <div class="user-info-item">
            <div>生日</div>
            <div class="user-info-link">点击关联<div></div></div>
        </div>
        <div class="user-info-item">
            <div>性别</div>
            <div class="user-info-date-link">点击关联<div></div></div>
        </div>
        <div class="user-info-item">
            <div>学校</div>
            <div class="user-info-link">点击关联<div></div></div>
        </div>
        <div class="user-info-item">
            <div>最喜欢的童装品牌</div>
            <div class="user-info-link">点击选择<div></div></div>
        </div>
    </div>
    <div class="bg"></div>
    <div class="linkuser">
        <div class="close-linkuser"><span class="close-btn">关闭</span></div>
        <div class="phone">
            <div>手机号</div>
            <div><input type="text" name="phone" id="phone" /></div>
        </div>
        <div class="pass">
            <div>密码</div>
            <div>
                <input type="password" name="pass" id="pass" />
            </div>
        </div>
        <div class="relate">关联会员</div>
    </div>
    <style type="text/css">
        .linkuser {
            position: fixed;
            bottom:0 ;
            width:100%;
            height: auto;
            z-index: 200;
            background: #fff;
            padding-bottom: 2rem;
        }
        .phone,.pass {
            margin-bottom: 1rem;
            padding: 0 20%;
        }
        .phone div, .pass div {
            float:left;
            line-height: 2rem;
        }
        .phone div:first-child,.pass div:first-child  {
            width: 30%;
            font-size: 0.8rem;
            color: #000;
        }
        .phone div:last-child,.pass div:last-child  {
            width: 70%;
        }
        #phone, #pass {
            width: 100%;
            line-height: 2rem;
            border:solid 1px #C1C1C1;
            padding:0 0.2rem;
            font-size:0.7rem;
        }
        .relate { width: 50%; margin: 0.1rem auto; border:solid 1px #C1C1C1;
            line-height:2rem;
            text-align: center;
            font-size: 0.8rem;
            color: #000;
        }
        .user-info { background: #fff; }
        .user-info-children {
            text-align: center;
            border-bottom: solid 1px #C1C1C1;
            line-height:2rem;
            font-weight:bold;
        }
        .user-info-icon-item, .user-info-item {
            padding: 0.1rem 5%;
            border-bottom: solid 1px #C1C1C1;
        }
        .user-info-icon-item {
            line-height: 3rem;
            font-weight:bold;
        }
        .user-info-icon-item div:first-child {
            float:left;
        }
        .user-info-icon-item div:last-child {
            float:right;
        }
        .user-info-icon {
            width: 2rem;
            height:2rem;
            border-radius: 2rem;
            border:solid 1px #C1C1C1;
            margin-top:0.5rem;
        }
        .user-info-item {
            line-height: 2rem;
        }
        .user-info-item:after,.user-info-icon-item:after,.phone:after,.pass:after {
            display:block;clear:both;content:"";visibility:hidden;height:0
        }
        .user-info-item div:first-child{
            float:left;
            font-weight:bold;
        }
        .user-info-item div:last-child{
            float:right;
        }
        .user-info-link div,.user-info-icon-div2 div:last-child {
            background:url('/images/more.png') no-repeat;
            background-size: 100%;
            width: 1rem;
            height: 1rem;
            float:right;
            margin-top:0.5rem;
        }
        .user-info-icon-div2 div:last-child { margin-top:1rem;}
        .bg { position:absolute;z-index:100;
            display: none; top:0; background: #000000; opacity: 0.8; width: 100%; height: 100%;}
        .close-linkuser {text-align:right;
            line-height:2rem;padding-right:1rem;font-size:0.8rem;}
    </style>
    <script type="text/javascript">
        $(function() {
            $(".bg").height($(window).height());
            $(".user-info-date-link").calendar({
                value: ['2015-12-05']
            });
        });

        $(".link-old").on("click", function() {
            $(".bg").show();
            $(".linkuser").show();
        });
    </script>
@endsection