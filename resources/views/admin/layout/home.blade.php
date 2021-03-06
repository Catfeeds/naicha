<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>PinYuer后台管理系统</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <!-- load css -->
    <link rel="stylesheet" type="text/css" href="{{asset('assets/admin/css/bootstrap.min.css?v=v3.3.7')}}" media="all">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/admin/css/font/iconfont.css?v=1.0.0')}}" media="all">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/admin/css/layui.css?v=1.0.9')}}" media="all">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/admin/css/main.css?v1.3.4')}}" media="all">

</head>

<body>
<div class="container-fluid larry-wrapper">
    <!--顶部统计数据预览 -->
    <div class="row">
        <div class="col-xs-6 col-sm-4 col-md-2">
            <section class="panel">
                <div class="symbol bgcolor-blue"> <i class="iconfont">&#xe672;</i>
                </div>
                <div class="value tab-menu">
                    <a href="javascript:;" data-url="{:url('admin/user/index')}" data-parent="true" data-title="会员总量"><i class="iconfont " data-icon='&#xe672;'></i>
                        <h1>222</h1>
                    </a>

                    <a href="javascript:;" data-url="{:url('admin/user/index')}" data-parent="true" data-title="会员总量"> <i class="iconfont " data-icon='&#xe672;'></i><span>会员总量</span></a>

                </div>
            </section>
        </div>
        <div class="col-xs-6 col-sm-4 col-md-2">
            <section class="panel">
                <div class="symbol bgcolor-commred"> <i class="iconfont">&#xe674;</i>
                </div>
                <div class="value tab-menu">
                    <a href="javascript:;" data-url="{:url('admin/user/index')}" data-parent="true" data-title="今日注册"> <i class="iconfont " data-icon='&#xe674;'></i>
                        <h1>333</h1>
                    </a>

                    <a href="javascript:;" data-url="{:url('admin/user/index')}" data-parent="true" data-title="今日注册"> <i class="iconfont " data-icon='&#xe674;'></i><span>今日注册</span></a>

                </div>
            </section>
        </div>

        <div class="col-xs-6 col-sm-4 col-md-2">
            <section class="panel">
                <div class="symbol bgcolor-dark-green"> <i class="iconfont">&#xe6bc;</i>
                </div>
                <div class="value tab-menu">
                    <a href="javascript:;" data-url="{:url('article/AdminContent/index')}" data-parent="true" data-title="文章总数"> <i class="iconfont " data-icon='&#xe6bc;'></i>
                        <h1>444</h1>
                    </a>
                    <a href="javascript:;" data-url="{:url('article/AdminContent/index')}" data-parent="true" data-title="文章总数"> <i class="iconfont " data-icon='&#xe6bc;'></i><span>文章总数</span></a><br />（多语言）
                </div>
            </section>
        </div>

        <div class="col-xs-6 col-sm-4 col-md-2">
            <section class="panel">
                <div class="symbol bgcolor-yellow-green"> <i class="iconfont">&#xe649;</i>
                </div>
                <div class="value tab-menu">
                    <a href="javascript:;" data-url="{:url('article/AdminContent/index')}" data-parent="true" data-title="今日新增"> <i class="iconfont " data-icon='&#xe649;'></i>
                        <h1>555</h1>
                    </a>
                    <a href="javascript:;" data-url="{:url('article/AdminContent/index')}" data-parent="true" data-title="今日新增"> <i class="iconfont " data-icon='&#xe649;'></i><span>今日新增</span></a><br />（多语言）
                </div>
            </section>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-md-6">
            <section class="panel">
                <div class="panel-heading">
                    网站信息
                    <a href="javascript:;" class="pull-right panel-toggle"><i class="iconfont">&#xe604;</i></a>
                </div>
                <div class="panel-body">
                    <table class="layui-table">
                        <tbody>
                        <tr>
                            <td>
                                <strong>网站名称</strong>：

                            </td>
                            <td>
                                <a href="">壹凯巴cms</a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>软件版本</strong>：

                            </td>
                            <td>
                                V2.0
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>开发作者</strong>：

                            </td>
                            <td>壹凯</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>php版本要求</strong>：
                            </td>
                            <td>
                                5.5以上
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <strong>QQ讨论群</strong>：
                            </td>
                            <td>
                                <a target="_blank" href="//shang.qq.com/wpa/qunwpa?idkey=31f713ee8afb94e4190a68a0a374d98b4304d6aa0c7ca7d4b2f8093fa7d59401"><img src="//pub.idqqimg.com/wpa/images/group.png" alt="创【壹凯巴】" title="创【壹凯巴】" border="0"></a></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </section>

        </div>


        <div class="col-xs-12 col-md-6">

            <section class="panel">
                <div class="panel-heading">
                    数据统计
                    <a href="javascript:;" class="pull-right panel-toggle"><i class="iconfont">&#xe604;</i></a>
                </div>
                <div class="panel-body">
                    <div class="echarts" id="echarts"></div>
                </div>
            </section>

        </div>
    </div>

</div>

<div class="testcatch" style="display: none;">
    <p>这是一个捕获弹窗</p>
</div>
</body>
<script type="text/javascript" src="{{asset('assets/admin/js/layui/layui.js')}}"></script>
<script>
    layui.config({
        base: "{{asset('assets/admin/js')}}/",
        version: "1.3.4r2"
    }).extend({
        elem: 'jqmodules/elem',
        tabmenu: 'jqmodules/tabmenu',
        jqmenu: 'jqmodules/jqmenu',
        ajax: 'jqmodules/ajax',
        dtable: 'jqmodules/dtable',
        jqdate: 'jqmodules/jqdate',
        modal: 'jqmodules/modal',
        tags: 'jqmodules/tags',
        jqform: 'jqmodules/jqform',
        echarts: 'lib/echarts',
        webuploader: 'lib/webuploader'
    })
</script>
<script>
    layui.use(['main', 'echart']);
</script>

</html>