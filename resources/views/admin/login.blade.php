<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>后台登录</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.6 -->
    <link rel="stylesheet" href="/css/admin/bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="/css/admin/dist/css/AdminLTE.min.css">
    <link rel="stylesheet" href="/css/admin/plugins/iCheck/square/blue.css">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <a href="../../index2.html"><b>管理后台</b></a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">

        <div class="form-group has-feedback">
            <input type="name" id="name" class="form-control" placeholder="账号" value="{{ $name }}">
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
            <input type="password" id="password" class="form-control" placeholder="密码">
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        </div>
        <div class="row">
            <div class="col-xs-8">
                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox" id="remeber"> 记住账号
                    </label>
                </div>
            </div>
            <!-- /.col -->
            <div class="col-xs-4">
                <button type="submit" class="btn btn-primary btn-block btn-flat login-btn">登录</button>
            </div>
            {{ csrf_field() }}
            <!-- /.col -->
        </div>
        <!-- /.social-auth-links -->

    </div>
    <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<!-- jQuery 2.2.3 -->
<script src="/css/admin/plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="/css/admin/bootstrap/js/bootstrap.min.js"></script>
<!-- iCheck -->
<script src="/css/admin/plugins/iCheck/icheck.min.js"></script>
<script>
    $(function () {
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });
        $(".login-btn").on("click", function () {
            var name = $("#name").val();
            var pass = $("#password").val();
            if (name == '' || pass == '') {
                alert('账号或密码不存在');
                return false;
            }
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
                }
            });
            console.log(name);
            console.log(pass);
            $.ajax({
                url: '/admin/check',
                type: 'post',
                data: {'name': name, 'password': pass, 'remeber': $("#remeber").prop('checked')/*, '_token': $("input[name='_token']").val()*/},
                dataType: 'json',
                success: function(data) {
                    if (data.rs == 1) {
                        alert('登陆成功');
                        location.href = "/admin/index"
                    }
                }
            });
        })
    });
</script>
</body>
</html>
