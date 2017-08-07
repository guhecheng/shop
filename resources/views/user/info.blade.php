@extends('layouts.app')
@section('content')
    <div class="content user-info">
        @if (empty($user->is_old))
        <div class="user-info-item">
            <div>我是老会员</div>
            <div class="link-old">点击关联<div></div></div>
        </div>
        @endif
        <div class="user-info-icon-item">
            <div>头像</div>
            <div class="user-info-icon-div2">
                <div class="user-info-icon" style="background-image:url({{ $user->avatar }});width: 2rem;height:2rem;margin-top:0.4rem;"></div>
                {{--<div></div>--}}
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
            <input type="text" data-field="date" value="{{ empty($user->birth_date) ? '' : $user->birth_date }}" placeholder="请选择日期" style="float:right;margin-top:0.5rem;text-align: right" id="child_date"/>
            <div id="SJHPicker" style="display:none;"></div>
            <!--<div class="user-info-date-link" attr-type="child_birth_date">{{ empty($user->birth_date) ? '添加' : $user->birth_date }}<div></div></div>-->
        </div>
        <div class="user-info-item">
            <div>性别</div>
            <div class="user-info-link" attr-type="child_sex">{{ empty($user->sex) ? '添加':($user->sex==1?'男':'女') }}<div></div></div>
        </div>
        <div class="user-info-item">
            <div>学校</div>
            <div class="user-info-link" attr-type="child_school">{{ empty($user->school) ? '添加':$user->school }}<div></div></div>
        </div>
        <div style="border-bottom:solid 1px #C1C1C1">
        <div class="user-info-item" style="border:0">
            <div>最喜欢的童装品牌</div>
            <div class="user-info-link" attr-type="child_brand">点击选择<div></div></div>

        </div>
            @if (!empty($user->like_brands))
                <div class="brands_show_area" style="padding:0 5% 0.4rem;">
                    @foreach (explode(",", $user->like_brands) as $item)
                        <span style="margin-right:0.2rem;">{{ $item }}</span>
                    @endforeach
                </div>
            @endif
            <input type="hidden" id="brands" value="{{ $user->like_brands }}" />
        </div>
    </div>
    <div class="bg"></div>
    <div class="linkuser">
        <div class="close-linkuser"><span class="close-btn"></span></div>
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
        <div class="close-commarea"><span class="close-area-btn"></span></div>
        <div class="comm-input">
            <div class="content-title"></div>
            <div class="comm-input-content">
                <input type="text" name="add-content" class="add-content"/>
                <select class="select-sex" name="select-sex" style="display: none;">
                    <option value="男">男</option>
                    <option value="女">女</option>
                </select>
            </div>
            <input type="hidden" name="info-type" class="info-type" value="" />
        </div>
        <div class="sure-btn" style="font-size:0.8rem;">确 定</div>
    </div>
    <div class="like_brands">
        <div class="close-brands"><span class="close-brand-btn"></span></div>
        <div class="select_brands_area">
            <div class="select-brands">耐克</div>
            <div class="select-brands">addidas</div>
            <div class="select-brands">安踏</div>
            <div class="select-brands">贵人鸟</div>
            <div class="select-brands">361</div>
            <div class="select-brands">其他</div>
        </div>
        <div class="sure_add_brands">确 定</div>
    </div>
    <style type="text/css">
        .sure_add_brands {
            position: absolute; bottom: 0; width:100%;line-height:2rem;left:0;
            text-align: center; background: #C1C1C1; font-size: 1rem; }
        .like_brands { position: fixed; top:0;left:0;
            width:100%;background:#fff;
            height:100%;
            display: none;}
        .select_brands_area { padding: 5%; }
        .select-brands { border:solid 1px #C1C1C1; width: 30%; line-height: 2rem;
            text-align: center; float:left; margin-top: 2rem;margin-left: 5%;}
        .select-brands:nth-of-type(odd) {
            margin-right: 10%;
            margin-left: 10%;
        }
        .select-brands.active { border: solid 1px red; }
        .select-sex { width:3rem; height:1.5rem; }
        .close-commarea { padding-right: 5%; text-align: right; }
        .close-area-btn, .close-brand-btn,.close-btn { background:url('/images/close.png') no-repeat;width:1.2rem;
            height:1.2rem;background-size:100%;
            position: absolute; right: 0.3rem; top:0.3rem;}
        .linkuser,.commarea {
            position: fixed;
            bottom:0 ;
            width:100%;
            height: auto;
            z-index: 200;
            background: #fff;
            display:none;
        }
        .sure-btn {
            line-height:1.8rem;
            width: 100%;
            margin: 0 auto;
            text-align:center;
            border-top: solid 1px #C1C1C1;
            margin-top:1rem;
            background: #C1C1C1;
            font-size:0.8rem;
        }
        .content-title { width: 30%; line-height:1.8rem;}

        .comm-input {
            width: 100%;
            padding:1rem 10%;
        }
        .comm-input:after {
            display:block;clear:both;content:"";visibility:hidden;height:0
        }
        .comm-input-content {
            width: 80%;
            margin-left: 10%;
        }
        .add-content { width: 100%; padding:0 5%; border:solid 1px #C1C1C1;
            line-height:1.8rem;}
        .phone,.pass {
            padding: 0 10%;
        }
        .phone div, .pass div {
            line-height: 2rem;
        }
            .phone div:first-child,.pass div:first-child  {
            width: 30%;
            font-size: 0.8rem;
            color: #000;
        }
        .phone div:last-child,.pass div:last-child  {
            width: 100%;
            padding:0 15%;
        }
        #phone, #pass {
            width: 100%;
            line-height: 2rem;
            border:solid 1px #C1C1C1;
            padding:0 0.2rem;
            font-size:0.7rem;
        }
        .relate { width: 100%; line-height:2rem; text-align: center; font-size: 0.8rem;
            color: #000;
            background:#c1c1c1;
            margin-top: 1rem;
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

            border-radius: 3rem;
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
        .user-info-link div,.user-info-icon-div2 div:last-child,.link-old div:last-child {
            background:url('/images/more.png') no-repeat;
            background-size: 100%;
            width: 1rem;
            height: 1rem;
            float:right;
            margin-top:0.5rem;
        }
        .user-info-icon-div2 div:last-child { margin-top:1rem;}
        .bg { position:absolute;z-index:100;
            display: none; top:0; background: #000; opacity: 0.2; width: 100%; height: 100%;}
        .close-linkuser {
            height:1.5rem; }
    </style>
    <link rel="stylesheet" href="/layer_mobile/need/layer.css">
    <link rel="stylesheet" type="text/css" href="/css/SJHPicker.min.css">
    <script src="/layer_mobile/layer.js"></script>
    <script src="/js/SJHPicker-zepto.min.js"></script>
    <script src="/js/zepto-touch.min.js"></script>
    <script type="text/javascript">
        $(function() {
            $(".bg").height($(window).height());
            /*$("#my-input").calendar({
                value: ['2015-12-05']
            });*/
            SJHPicker.init({				       //初始化控件
                max_date: new Date('{{ date("Y-m-d") }}'),  //日期上限
                min_date: new Date('1990-08-13'),  //日期下限
                start_day: 1,					   //周开始日，0为周日，1-6为周一至周六
                selected_date: new Date('{{ empty($user->birth_date) ? date("Y-m-d") : $user->birth_date }}'),
                format: 'yyyy-MM-dd'		       //输出日期格式
            });
        });
        $(".select-brands").on("touchstart", function () {
            if ($(this).hasClass("active")) {
                $(this).removeClass("active");
            } else {
                $(this).addClass("active");
            }
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
                //alert('手机号或密码不能为空');
                layer.open({
                    content: '手机号码或密码不能为空'
                    ,btn: '确定'
                });
                return false;
            }
            $.ajax({
                url : '/relate',
                type:'post',
                data: {'phone':phone, 'pass':pass},
                dataType:'json',
                success: function (data) {
                    if (data.rs == 1) {
                        layer.open({
                            content: '关联成功'
                            ,btn: '确定',
                            yes: function() {
                                location.reload();
                            }
                        });
                    } else {
                        alert(data.errmsg);
                        layer.open({
                            content: data.errmsg
                            ,btn: '确定'
                        });
                        return false;
                    }
                }
            })
        });
        $(".user-info-link").on("click", function(item, index) {
            if ($(this).attr('attr-type') == 'child_brand') {
                $(".like_brands").show();
                var brands_arr = $("#brands").val().split(",");
                $(".select-brands").each(function () {
                    if (brands_arr.indexOf($.trim($(this).text())) >= 0) {
                        $(this).addClass("active");
                    }
                });
                return ;
            }
            $(".commarea,.bg").show();
            var par = $(this).parent();
            $(".info-type").val($(this).attr("attr-type"));
            $(".content-title").text($.trim($(this).parent().find("div").first().text()));
            var content = $.trim($(this).text());
            if ($(this).attr('attr-type') == 'child_sex') {
                $(".select-sex").show();
                $(".add-content").hide();
                $(".select-sex").val(content == '' ? '男' : content);
            } else {
                $(".select-sex").hide();
                $(".add-content").show().val(content == '添加' ? '' : content);
            }
        });
        $(".sure-btn").on('click', function() {
            var index = $('.info-type').val();
            console.log(index);
            if (index == 'child_sex') {
                var content = $(".select-sex").val();
            } else {
                var content = $(".add-content").val();
            }
            if (content == '') {
                //alert('不能为空');
                layer.open({
                    content: '没有填写信息'
                    ,btn: '确定'
                });
                return false;
            }
            if (index == 'phone' && !(/^1[34578]\d{9}$/.test(content))) {
                //alert('手机号码错误');
                layer.open({
                    content: '手机号码错误'
                    ,btn: '确定'
                });
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
                        layer.open({
                            content: '填写失败'
                            ,btn: '确定'
                        });
                        return false;
                    }
                }
            });
        });
        $(".close-area-btn").on("click", function() {
            $(".commarea,.bg").hide();
        });
        $(".close-brand-btn").on("click", function () {
            $(".like_brands").hide();
        });

        $(".sure_add_brands").on("click", function () {
            var brands = '';
            $(".select-brands").each(function () {
                if ($(this).hasClass("active"))
                    brands += $.trim($(this).text()) + ",";
            });
            if (brands == '') {
                layer.open({ content: '没有选择品牌', btn: '确定' });
                return false;
            }
            $.ajax({
                url: '/modinfo',
                data: {'index':'child_brand', 'content': brands },
                dataType:'json',
                type:'post',
                success: function (data) {
                    if (data.rs == 1) {
                        location.reload();
                    } else {
                        layer.open({
                            content: '添加失败'
                            ,btn: '确定'
                        });
                        return false;
                    }
                }
            })
        });
    </script>
@endsection