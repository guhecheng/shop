@extends('layouts.comm')

@section('title', '钻石试衣特权')

@section('content')
    <div class="purcharse">
        <div class="purchase_goods_name">
            <div>姓 名</div>
            <div><input type="text" name="" placeholder="点击输入" id="concat_name"></div>
        </div>
        <div class="purchase_contact_phone">
            <div>联系电话</div>
            <div><input type="text" name="concat_phone" placeholder="点击输入" id="concat_phone"></div>
        </div>
        <div class="purchase_goods_name">
            <div>宝贝年龄</div>
            <div><input type="text" name="" placeholder="点击输入" id="age"></div>
        </div>
        <div class="purchase_goods_name">
            <div>宝贝性别</div>
            <div><input type="text" name="" placeholder="点击输入" id="gender"></div>
        </div>
        <div class="purchase_goods_name">
            <div>参考尺码</div>
            <div><input type="text" name="" placeholder="点击输入" id="size"></div>
        </div>
        {{ csrf_field() }}
        <div class="purchase_goods_desc">
            <div class="purchase_goods_desc_title">更多详情</div>
            <div class="purchase_goods_desc_content"><textarea placeholder="点击输入相关描述" name="goods_desc" id="desc"></textarea></div>
        </div>
        <div class="purchase_goods_content">
            <p>请您填写真实有效的联系方式</p>
            <p>我们的工作人员会在收到后与您联系</p>
            <p>为您提供上门试衣服务</p>
        </div>
        <div id="purchase_add_btn">提交信息</div>
    </div>
    <!--</form>-->
    <div class="purchase_add_content">
        <div class="purchase_add_image"></div>
        <div class="purchase_add_result">您的代购信息已提交成功!</div>
    </div>
    <style type="text/css">
        .purchase_goods_content {
            margin-top:1rem;
            background: #fff;
            padding-top:2rem;
            marign:1rem auto;
            text-align: center;
            height: 8rem;
        }
        #purchase_add_btn {
            position: fixed;
            bottom: 0;
            width: 100%;
            line-height:2.5rem;
            text-align: center;
            font-size: 1.2rem;
            background: #fff;
        }
        .purchase_add_image { background-image:url('/images/purchase_no_content.png');
            background-repeat:no-repeat;
            background-size: 100% 100%;
            width: 20%;
            margin: 20% auto 1%;
            height: 3.8rem;
        }
        .purchase_add_content { display: none; }
        .purchase_add_result { text-align: center; }
        .purchase_contact_phone, .purchase_goods_name, .purchase_upload_img,.purchase_goods_desc {
            padding: 0 5%;
            width: 100%;
            background: #fff;
            margin-top: 0.7rem;
        }
        .purchase_contact_phone div:nth-child(1),.purchase_goods_name div:nth-child(1),.purchase_upload_img div:nth-child(1) { width: 30%;  }
        .purchase_contact_phone div:nth-child(2),.purchase_goods_name div:nth-child(2),.purchase_upload_img div:nth-child(2) { width: 70%;  }
        .purchase_contact_phone input,.purchase_goods_name input,.purchase_upload_img input{ height: 100%;
            width: 100%;
            line-height: 2rem;
            height: 2rem;
            font-size: 1rem;
            text-align: right;
        }
        .purchase_upload_img { line-height: 2rem; }
        .purchase_upload_area { background: #fff; padding-bottom:0.5rem; padding-top:0.5rem;   }
        .purchase_contact_phone:after , .purchase_goods_name:after, .purchase_upload_img:after, .purchase_upload_area:after {
            display:block;clear:both;content:"";visibility:hidden;height:0
        }
        .purchase_contact_phone div,.purchase_goods_name div, .purchase_upload_img div {
            float: left;
            line-height: 2rem;
        }
        .purchase_goods_desc { padding-top:0.4rem; padding-bottom:0.4rem;}
        .purchase_goods_desc_title { line-height:2rem; }
        .purchase_upload_img div:nth-child(2) { float:right; width:28%; text-align: right; }
        #desc {
            width: 100%;
            font-size: 1.2rem;
            font-weight: 400;
            height: auto;
            min-height: 6rem;
        }
        #upload_image { width:1rem; height: 1rem; }
    </style>
    <script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <script type="text/javascript">
        $.ajaxSettings = $.extend($.ajaxSettings, {
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $("#purchase_add_btn").on('click', function () {
            var formdata = new FormData();

            var concat_name = $("#concat_name").val();
            var concat_phone = $("#concat_phone").val();
            var age = $("#age").val();
            var desc = $("#desc").val();
            var gender = $("#gender").val();
            var size = $("#size").val();

            if (desc == '' || concat_phone == '' || concat_name == '' || age == '') {
                layer.open({
                    content: '信息填写不全'
                    ,btn: '确定'
                });
                return false;
            }
            if(!(/^1[34578]\d{9}$/.test(concat_phone))){
                layer.open({
                    content: '联系电话错误'
                    ,btn: '确定'
                });
                return false;
            }
            if (gender == '' || ( gender != '男' && gender != '女')) {
                layer.open({
                    content: '性别只能填男女'
                    ,btn: '确定'
                });
                return false;
            }
            formdata.append("concat_name", concat_name);
            formdata.append("concat_phone", concat_phone);
            formdata.append("age", age);
            formdata.append("desc", desc);
            formdata.append("size", size);
            formdata.append("gender", gender);
            console.log(formdata);
            $.ajax({
                url: '/user/fit',
                data: formdata,
                type: 'post',
                processData: false,
                contentType: false,
                success: function (data) {
                    if (data.rs == 0) {
                        layer.open({
                            content: data.errmsg
                            ,btn: '确定'
                        });
                        return false;
                    } else {
                        $(".purcharse").remove();
                        $(".purchase_add_content").show();
                        $("body").css('background', '#fff');
                    }
                }
            });
        });
    </script>
@endsection