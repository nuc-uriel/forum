$(function () {
    // 加入小组
    $('.join-group').click(function (event) {
        var data = {
            gid: $('meta[name=gid]').attr('content')
        };
        $.ajax({
            url: "/group/join",
            type: 'GET',
            data: data,
            dataType: "json",
            success: function (data) {
                if (data.status === 10000) {
                    layer.msg(data.res, {icon: 1}, function () {
                        window.location.reload();
                    });
                } else if (data.status === 10001) {
                    layer.alert(data.res, {
                        title: '提示',
                        skin: 'layui-layer-lan',
                        closeBtn: 0,
                        anim: 1,
                        icon: 1
                    });
                } else if (data.status === 20001) {
                    layer.alert(data.res, {
                            title: '提示',
                            skin: 'layui-layer-lan',
                            closeBtn: 0,
                            anim: 6,
                            icon: 0
                        },
                        function () {
                            $(location).prop('href', '/login');
                        });
                } else {
                    layer.alert(data.res, {
                        title: '提示',
                        skin: 'layui-layer-lan',
                        closeBtn: 0,
                        anim: 6,
                        icon: 0
                    });
                }
            },
            error: function (data) {
                layer.alert('网络繁忙，请稍后再试！', {
                    title: '提示',
                    skin: 'layui-layer-lan',
                    closeBtn: 0,
                    anim: 6,
                    icon: 0
                });
            }
        });
    });

    // 退出小组
    $('.quit-group').click(function (event) {
        layer.confirm('确定退出' + $('.group-info .wrapper>span').text() + '小组吗？', {
            icon: 3,
            title: '提示'
        }, function (index) {
            var data = {
                gid: $('meta[name=gid]').attr('content')
            };
            $.ajax({
                url: "/group/quit",
                type: 'GET',
                data: data,
                dataType: "json",
                success: function (data) {
                    if (data.status === 10000) {
                        layer.msg(data.res, {icon: 1}, function () {
                            window.location.reload();
                        });
                    } else if (data.status === 20001) {
                        layer.alert(data.res, {
                                title: '提示',
                                skin: 'layui-layer-lan',
                                closeBtn: 0,
                                anim: 6,
                                icon: 0
                            },
                            function () {
                                $(location).prop('href', '/login');
                            });
                    } else {
                        layer.alert(data.res, {
                            title: '提示',
                            skin: 'layui-layer-lan',
                            closeBtn: 0,
                            anim: 6,
                            icon: 0
                        });
                    }
                },
                error: function (data) {
                    layer.alert('网络繁忙，请稍后再试！', {
                        title: '提示',
                        skin: 'layui-layer-lan',
                        closeBtn: 0,
                        anim: 6,
                        icon: 0
                    });
                }
            });
            layer.close(index);
        });
    });
});