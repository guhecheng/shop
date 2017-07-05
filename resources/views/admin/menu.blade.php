<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        {{--<div class="user-panel">
            <div class="pull-left image">
                <img src="dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p>Alexander Pierce</p>
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>
        <!-- search form -->
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search...">
                <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
            </div>
        </form>--}}
        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            <li class="active treeview">
                <a href="#">
                    <i class="fa fa-dashboard"></i> <span>商品管理</span>
                    <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                </a>
                <ul class="treeview-menu">
                    <li class="active"><a href="/admin/goods"><i class="fa fa-circle-o"></i>商品列表</a></li>
                    <li><a href="/admin/type"><i class="fa fa-circle-o"></i> 类目管理</a></li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-files-o"></i>
                    <span>用户管理</span>
                    <span class="pull-right-container"><span class="label label-primary pull-right"></span></span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="/admin/user"><i class="fa fa-circle-o"></i>用户列表</a></li>
                    <li><a href="/admin/userexport"><i class="fa fa-circle-o"></i>导入用户</a></li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-files-o"></i>
                    <span>订单管理</span>
                    <span class="pull-right-container"><span class="label label-primary pull-right"></span></span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="/admin/card"><i class="fa fa-circle-o"></i>订单列表</a></li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-files-o"></i>
                    <span>会员卡管理</span>
                    <span class="pull-right-container"><span class="label label-primary pull-right"></span></span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="/admin/card"><i class="fa fa-circle-o"></i>卡片列表</a></li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-files-o"></i>
                    <span>消息管理</span>
                    <span class="pull-right-container"><span class="label label-primary pull-right"></span></span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="/admin/message"><i class="fa fa-circle-o"></i>消息列表</a></li>
                </ul>
            </li>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>