<ul class="layui-nav layui-layout-left">
    <li class="layui-nav-item"><a href="{{url('manager')}}">控制台</a></li>
    {{--<li class="layui-nav-item">
        <a href="javascript:;">下拉菜单</a>
        <dl class="layui-nav-child">
            <dd><a href="">邮件管理</a></dd>
            <dd><a href="">消息管理</a></dd>
            <dd><a href="">授权管理</a></dd>
        </dl>
    </li>--}}
</ul>
<ul class="layui-nav layui-layout-right">
    <li class="layui-nav-item">
        <a href="javascript:;">
            <img src="http://t.cn/RCzsdCq" class="layui-nav-img">
            {{ Auth::user()->name }}
        </a>
    </li>
    <li class="layui-nav-item">
        <a href="{{ url('auth/logout') }}"
           onclick="event.preventDefault();  document.getElementById('logout-form').submit();">
            退出
        </a>

        <form id="logout-form" action="{{ url('auth/logout') }}" method="GET" style="display: none;">
            {{ csrf_field() }}
        </form>
    </li>
</ul>