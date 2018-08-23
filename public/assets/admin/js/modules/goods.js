layui.use(['table','form','jquery'], function(){
    var table = layui.table
        ,form = layui.form
        ,$ = layui.jquery;

    var _mod = 'goods';

    table.render({
        elem: '#goods_table'
        ,url: '/admin/' + _mod + '/list' //数据接口
        ,limit: 10
        ,page: true //开启分页
        ,cols: [[ //表头
            // {fixed: 'left',checkbox : true}
            {field: 'id', title: 'ID', width:50, align:'center'}
            ,{field: 'category_name', title: '分类名称', align:'center',width:150}
            ,{field: 'name', title: '商品名称', align:'center',width:250}
            ,{field: 'image', title: '商品图片', align:'center',width:150, templet:'<div><img src="{{d.image}}"></div>',style:'height:50px;'}
            ,{field: 'price', title: '价格', align:'center',width:120}
            ,{field: 'deploy', title: '商品属性', align:'center',width:300}
            ,{field: 'status', title: '状态', align:'center',width:110}
            // ,{field: 'created_at', title: '创建时间',align:'center', width: 165}
            ,{field: 'updated_at', title: '更新时间',align:'center', width: 165}
            ,{title: '操作', width:170, align:'center', toolbar: '#bartools'} //这里的toolbar值是模板元素的选择器
        ]]
    });

    //监听工具条
    table.on('tool(goods_table)', function (obj) { //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
        var data = obj.data; //获得当前行数据
        var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
        var tr = obj.tr; //获得当前行 tr 的DOM对象

        if (layEvent === 'del') { //删除
            console.log('detail');
            console.log(obj);
            layer.confirm('确定删除行吗，此操作不可恢复', function (index) {
                layer.close(index);
                //向服务端发送删除指令
                $.ajax({
                    type: 'DELETE',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '/admin/' + _mod + '/'+obj.data.id,
                    success: function(data) {
                        if(data.code==1){
                            layer.alert(data.msg,{icon: 1});
                            obj.del(); //删除对应行（tr）的DOM结构，并更新缓存
                        }else{
                            layer.alert(data.msg,{icon: 2});
                        }
                    },
                    error : function (msg) {
                        console.log('error');
                        layer.alert(data.msg,{icon: 2});
                    }
                });
            });
        } else if (layEvent === 'edit') { //编辑
            //do something
            location.href= '/admin/' + _mod + '/'+obj.data.id+'/edit';

            // //同步更新缓存对应的值
            // obj.update({
            //     username: '123'
            //     , title: 'xxx'
            // });
        } else if (layEvent === 'online' || layEvent === 'offline') { //删除
            var text = (layEvent === 'online') ? '确定要上架吗' : '确定要下架吗';

            layer.confirm(text, function (index) {
                layer.close(index);
                //向服务端发送删除指令
                $.ajax({
                    type: 'PUT',
                    dataType: 'json',
                    data: {action: layEvent},
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '/admin/' + _mod + '/'+ obj.data.id,
                    success: function(data) {
                        if(data.code==1){
                            layer.alert(data.msg,{icon: 1});
                            window.location.reload();
                        }else{
                            layer.alert(data.msg,{icon: 2});
                        }
                    },
                    error : function (msg) {
                        console.log('error');
                        layer.alert(data.msg,{icon: 2});
                    }
                });
            });
        }
    });

    //自定义验证规则
    form.verify({

    });

    //监听提交
    form.on('submit(goods)', function(data){
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: data.form.action,
            data: data.field,
            success: function(data) {
                if(data.code==1){
                    layer.msg(data.msg,{icon: 1}, function () {
                        location.href = '/admin/' + _mod;
                    });

                }else{
                    layer.alert(data.msg,{icon: 2});
                }
            },
            error : function (msg) {
                var json=JSON.parse(msg.responseText);
                $.each(json.errors,function(index,error){
                    $.each(error,function(key,value){
                        layer.alert(value,{icon: 2});
                    });
                });
            }
        });
        return false;
    });
});