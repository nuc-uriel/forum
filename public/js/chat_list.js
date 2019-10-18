$(function () {
    $(".message-list li, .message-list a").click(function (event) {
        event.stopPropagation();
        url = $(this).attr('href');
        $(location).prop('href', url);
    });

    $(".del-message").click(function (event) {
        url = $(this).attr('url');
        layer.confirm('确定删除全部会话吗？', {
            icon: 3,
            title: '提示'
        }, function (index) {
            $.ajax({
                url: url,
                type: 'GET',
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