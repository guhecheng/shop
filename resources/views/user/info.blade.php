@extends('layouts.app')
@section('content')
    <div class="content user-info">
        @if (empty($user->is_old))
        <div class="user-info-item">
            <div>我是老会员</div>
            <div class="user-info-link link-old">点击关联<div></div></div>
        </div>
        @endif
        <div class="user-info-icon-item">
            <div>头像</div>
            <div class="user-info-icon-div2">
                <div class="user-info-icon" style="background-image:url({{ $user->avatar }})"></div>
                <div></div>
            </div>
        </div>
        <div class="user-info-item">
            <div>姓名</div>
            <div class="user-info-link" attr-type="myname">{{ empty($user->nickname)?$user->uname:$user->nickname }}<div></div></div>
        </div>
        <div class="user-info-item">
            <div>手机号码</div>
            <div class="user-info-link" attr-type="phone">{{ empty($user->phone)?'添加':$user->phone }}<div></div></div>
        </div>
        <div class="user-info-children">孩子信息</div>
        <div class="user-info-item">
            <div>姓名</div>
            <div class="user-info-link" attr-type="child_name">{{ empty($user->name) ? '添加' : $user->name }}<div></div></div>
        </div>
        <div class="user-info-item">
            <div>生日</div>
            <div class="user-info-date-link" attr-type="child_birth_date">{{ empty($user->birth_date) ? '添加' : $user->birth_date }}<div></div></div>
        </div>
        <div class="user-info-item">
            <div>性别</div>
            <div class="user-info-link" attr-type="child_sex">{{ empty($user->childsex) ? '添加':($user->childsex==1?'男':'女') }}<div></div></div>
        </div>
        <div class="user-info-item">
            <div>学校</div>
            <div class="user-info-link" attr-type="child_school">{{ empty($user->school) ? '添加':$user->school }}<div></div></div>
        </div>
        <div class="user-info-item">
            <div>最喜欢的童装品牌</div>
            <div class="user-info-link" attr-type="child_brand">点击选择<div></div></div>
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
    <div class="commarea">
        <div class="close-commarea"><span class="close-area-btn">关闭</span></div>
        <div class="comm-input">
            <div class="content-title"></div>
            <div class="comm-input-content"><input type="text" name="add-content" class="add-content"/></div>
            <input type="hidden" name="info-type" class="info-type" value="" />
        </div>
        <div class="sure-btn">确定</div>
    </div>
    <style type="text/css">
        .close-commarea { padding-right: 5%; text-align: right; }
        .close-area-btn { font-size: 0.8rem;}
        .linkuser,.commarea {
            position: fixed;
            bottom:0 ;
            width:100%;
            height: auto;
            z-index: 200;
            background: #fff;
            padding-bottom: 2rem;
            display:none;
        }
        .sure-btn {
            line-height:1.8rem;
            width: 50%;
            margin: 0 auto;
            text-align:center;
            border: solid 1px #C1C1C1;
        }
        .content-title { width: 30%; line-height:1.8rem;}

        .comm-input {
            width: 100%;
            padding:1rem 20%;
        }
        .comm-input:after {
            display:block;clear:both;content:"";visibility:hidden;height:0
        }
        .comm-input div {
            float:left;
        }
        .comm-input-content {
            width: 60%;
        }
        .add-content { width: 100%; padding:0 5%; border:solid 1px #C1C1C1;
            line-height:1.8rem;}
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
            background-size: 100%;
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
            display: none; top:0; background: #000; opacity: 0.1; width: 100%; height: 100%;}
        .close-linkuser {text-align:right;
            line-height:2rem;padding-right:1rem;font-size:0.8rem;}
    </style>
    <script type="text/javascript">
        $(function() {
            $(".bg").height($(window).height());
            /*$("#my-input").calendar({
                value: ['2015-12-05']
            });*/
        });
        $.ajaxSettings = $.extend($.ajaxSettings, {
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(".link-old").on("click", function() {
            $(".bg").show();
            $(".linkuser").show();
        });
        $(".close-btn").on("click", function() {
            $(".bg").hide();
            $(".linkuser").hide();
            $("#phone").val("");
            $("#pass").val("");
        });
        $(".relate").on("click", function() {
            var phone = $("#phone").val();
            var pass = $("#pass").val();
            if (phone == '' || pass == '') {
                alert('手机号或密码不能为空');
                return false;
            }
            $.ajax({
                url : '/relate',
                type:'post',
                data: {'phone':phone, 'pass':pass},
                dataType:'json',
                success: function (data) {
                    if (data.rs == 1) {
                        alert('关联成功');
                        location.reload();
                    } else {
                        alert('信息填写错误');
                        return false;
                    }
                }
            })
        });
        $(".user-info-link").on("click", function(item, index) {
            console.log($(this).attr("attr-type"));
            $(".commarea,.bg").show();
            var par = $(this).parent();
            $(".info-type").val($(this).attr("attr-type"));
            $(".content-title").text($.trim($(this).parent().find("div").first().text()));
            var content = $.trim($(this).text());
            $(".add-content").val(content == '添加' ? '' : content);
        });
        $(".sure-btn").on('click', function() {
            var index = $('.info-type').val();
            console.log(index);
            var content = $(".add-content").val();
            if (content == '') {
                alert('不能为空');
                return false;
            }
            if (index == 1 && !(/^1[34578]\d{9}$/.test(content))) {
                alert('手机号码错误');
                return false;
            }
            $.ajax({
                type:'post',
                url: '/modinfo',
                data: {'index':index, 'content': content},
                dataType:'json',
                success: function(data) {
                    if (data.rs == 1) {
                        location.reload();
                        //$(".commarea,.bg").hide();
                    } else {
                        alert('修改失败');
                        return false;
                    }
                }
            });
        });
        $(".close-area-btn").on("click", function() {
            $(".commarea,.bg").hide();
        });
    </script>
@endsection