@extends('layouts.app')

@section('title', '编辑地址')

@section('content')
    <div class="address-create">
        <ul>
            <li>
                <div class="address-create-div">
                    <div>收货人</div>
                    <div>
                        <input type="text" name="" placeholder="收货人" id="name" value="{{ $address->name }}"/>
                    </div>
                    <br clear="all" />
                </div>
            </li>
            <li>
                <div class="address-create-div">
                    <div>联系电话</div>
                    <div>
                        <input type="text" name="" placeholder="联系电话" id="phone" value="{{$address->phone}}"/>
                    </div>
                    <br clear="all" />
                </div>
            </li>
            <li>
                <div class="address-create-div" id="picker">
                    <div>地区</div>
                    <div>
                        <input type="text" id="address" value="{{$address->address}}" readonly=""/>
                        <input id="value1" type="hidden" value="20,234,504"/>
                    </div>
                    <br clear="all" />
                </div>
            </li>
            <li>
                <div class="address-location">
                    <div style="padding-top:0.2rem;">详细地址</div>
                    <div>
                        <textarea id="location">{{ $address->location }}</textarea>
                    </div>
                </div>
            </li>
            <li>
                <div class="address-create-checkbox">
                    <div>设为默认</div>
                    <div>
                        <div class="item-input">
                            <label class="label-switch">
                                <input type="checkbox" id="select" {{ $address->is_default ? 'checked' : '' }}>
                                <div class="checkbox"></div>
                            </label>
                        </div>
                    </div>
                    <br clear="all" />
                </div>
            </li>
            <input type="hidden" id="address_id" value="{{ $address->address_id }}" />
            {{ csrf_field() }}
        </ul>
        <div class="address-add-btn">
            确认修改
        </div>
    </div>
    <script type="text/javascript" src="/js/LArea.min.js" charset="utf-8"></script>
    <script type="text/javascript" src="/js/LArea.Data1.js" charset="utf-8"></script>
    <link href="/css/LArea.css" type="text/css" rel="stylesheet"/>

    <script type="text/javascript">
        var area1 = new LArea();
        area1.init({
            'trigger': '#address', //触发选择控件的文本框，同时选择完毕后name属性输出到该位置
            'valueTo': '#value1', //选择完毕后id属性输出到该位置
            'keys': {
                id: 'id',
                name: 'name'
            }, //绑定数据源相关字段 id对应valueTo的value属性输出 name对应trigger的value属性输出
            'type': 1, //数据源类型
            'data': LAreaData //数据源
        });
        console.log(area1);
        $(function () {
            $(".address-add-btn").on("click", function() {
                var name = $("#name").val();
                var phone = $("#phone").val();
                var address = $("#address").val();
                var location = $("#location").val();
                var address_id = $("#address_id").val();
                var select = $("#select");
                if (name == '' || phone == '' || address == '' || location == '' || address_id == '') {
                    $.toast("信息填写不全", 1000);
                    return false;
                }
                if (!(/^1[34578]\d{9}$/.test(phone))) {
                    $.toast('手机号码错误');
                    $("#phone").trigger('focus');
                    return false;
                }
                $.ajax( {
                    type: 'put',
                    data: {'name': name, 'phone': phone, 'address': address,
                        'location': location, 'checked': select.prop('checked'),
                        '_token': $("input[name='_token']").val(), 'address_id' : address_id},
                    dataType: 'json',
                    url: '/address',
                    success: function(data) {
                        if (data.rs == 1) {
                            //alert("添加成功，返回列表页");
                            location.href = '/address';
                        }
                    }
                })
            });
        })
    </script>
@endsection