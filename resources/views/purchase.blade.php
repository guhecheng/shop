@extends('layouts.comm')

@section('title', '指定代购')

@section('content')
    <div class="purcharse">
        <!-- <form action="/purchase/add" method="post" id="add_form" enctype="multipart/form-data">-->
        <div class="purchase_goods_name">
            <div>商品名称</div>
            <div><input type="text" name="" placeholder="点击输入" id="goods_name"></div>
        </div>
        <div class="purchase_contact_phone">
            <div>联系电话</div>
            <div><input type="text" name="concat_phone" placeholder="点击输入" id="concat_phone"></div>
        </div>
        <div class="purchase_upload">
            <div class="purchase_upload_img">
                <div>上传图片</div>
                <div>
                    <span>点击选择</span>
                </div>
            </div>
            <div class="purchase_upload_area">
                <div class="upload_img">
                    <img class='img' />
                    <input type="file" id="upload" name="upload"/>
                    <div class="img_delete"></div>
                </div>
            </div>
        </div>
        {{ csrf_field() }}
        <div class="purchase_goods_desc">
            <div class="purchase_goods_desc_title">具体描述</div>
            <div class="purchase_goods_desc_content"><textarea placeholder="点击输入相关描述" name="goods_desc" id="goods_desc"></textarea></div>
        </div>
        <div id="purchase_add_btn">提交信息</div>
    </div>
    <!--</form>-->
    <div class="purchase_add_content">
        <div class="purchase_add_image"></div>
        <div class="purchase_">您的代购信息已提交成功!</div>
    </div>
    <style type="text/css">
        .img { width: 4rem; height: 4rem;
            overflow: hidden; }
        .upload_img {
            width:4rem;
            height:4rem;
            background-image:url('/images/add_image_btn.png');
            background-repeat: no-repeat;
            background-size: 100% 100%;
            margin-left: 5%;
            position: relative;
            float:left;
        }
        #upload { width:4rem; height: 4rem; position: absolute;top:0;left:0;opacity:0;  }
        .img_delete { background-image:url('/images/X.png');background-repeat: no-repeat;
            -webkit-background-size: 100% 100%;
            background-size: 100% 100%;
            width:1rem;
            height: 1rem;
            position: absolute;
            top:0;
            right:0;
            display: none;
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
        .purchase_add_content { display: none; }
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
        #goods_desc {
            width: 100%;
            font-size: 1.2rem;
            font-weight: 400;
            height: auto;
            min-height: 6rem;
        }
        #upload_image { width:1rem; height: 1rem; }
    </style>
    <script type="text/javascript">
        $.ajaxSettings = $.extend($.ajaxSettings, {
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $("#upload").on("change", function () {
            //if ($(this).parent().find(".img").attr('src') != '') return false;
            var file = this.files[0];
            var type = file.type;
            if (type != 'image/png' && type != 'image/jpeg' && type != 'image/jpg' && type != 'image/gif') {
                layer.open({
                    content: '请上传图片'
                    ,btn: '确定'
                });
                return false;
            }
            if (file.size > 1024 * 1024 * 2) {
                layer.open({
                    content: '图片过大'
                    ,btn: '确定'
                });
                return false;
            }
            var r = new FileReader();
            r.readAsDataURL(file);
            $(r).load(function() {
                $(".img").attr('src', this.result);
                $(".upload").hide();
                $(".img_delete").show();
                //console.log(r);
                //var img_clone = $(".upload_img").clone();
                //img_clone.find('.img').attr('src', this.result);
                //$(".purchase_upload_area").append(img_clone);
                //$('#photo').html('<img src="' + this.result + '" alt="" />');
            });
        });
        $(".img_delete").on("touchstart", function () {
            $(".img").attr("src", '');
            $(".upload").show().val('');
            $(this).hide();
        });
        $("#purchase_add_btn").on('click', function () {
            var formdata = new FormData();

            var goods_name = $("#goods_name").val();
            var concat_phone = $("#concat_phone").val();
            var goods_desc = $("#goods_desc").val();
            if (goods_desc == '' || concat_phone == '' || goods_name == '') {
                layer.open({
                    content: '信息填写不全'
                    ,btn: '确定'
                });
                return false;
            }
            formdata.append("goods_name", goods_name);
            formdata.append("concat_phone", concat_phone);
            formdata.append("file", $("#upload").get(0).files[0]);
            formdata.append("goods_desc", goods_desc);
            console.log(formdata);
            $.ajax({
                url: '/purchase/add',
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
                        callpay(data);
                    }
                }
            });
        });
    </script>
    <script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <script type="text/javascript">
        //调用微信JS api 支付
        function jsApiCall(data)
        {
            WeixinJSBridge.invoke(
                'getBrandWCPayRequest',data,
                function(res){
                    alert(res.err_msg);
                    alert(data);
                    if(res.err_msg == "get_brand_wcpay_request:ok" ) {
                        alert('支付成功');
                        $(".purchase_add_content").show();
                        $(".purchase").remove();
                    } else {
                        $("#pay_chance").val(0);
                    }
                }
            );
        }

        function callpay(param)
        {
            if (typeof WeixinJSBridge == "undefined"){
                if( document.addEventListener ){
                    document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
                }else if (document.attachEvent){
                    document.attachEvent('WeixinJSBridgeReady', jsApiCall);
                    document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
                }
            }else{
                jsApiCall(param);
            }
        }
    </script>
@endsection