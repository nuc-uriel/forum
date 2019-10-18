$(function () {
    // 通知广播
    inform_channel = 'inform.' + $('meta[name=code]').attr('content');
    window.Echo.private(inform_channel).listen('SystemInform', (e) => {
        if (e.inform.is_dispose === 1) {
            layer.open({
                type: 1,
                title: '系统通知',
                shade: false,
                skin: 'layer-inform',
                area: ['340px', '215px'],
                offset: 'rb', //右下角弹出
                time: 10000, //10秒后自动关闭
                anim: 2,
                resize: false,
                content: e.inform.content,
                btn: ['同意', '拒绝', '忽略'],
                yes: function (index, layero) {
                    $.ajax({
                        url: "/inform/pass/" + e.inform.code,
                        type: 'GET',
                        dataType: "json",
                        success: function (data) {
                            if (data.status === 10000) {
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
                },
                btn2: function (index, layero) {
                    $.ajax({
                        url: "/inform/refuse/" + e.inform.code,
                        type: 'GET',
                        dataType: "json",
                        success: function (data) {
                            if (data.status === 10000) {
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
                        },
                    });
                    layer.close(index);
                },
                btn3: function (index, layero) {
                    layer.close(index);
                },
            });
        } else {
            layer.open({
                type: 1,
                title: '系统通知',
                shade: false,
                skin: 'layer-inform',
                area: ['340px', '215px'],
                offset: 'rb', //右下角弹出
                time: 10000, //10秒后自动关闭
                anim: 2,
                resize: false,
                content: e.inform.content,
            });
        }
    });
    // 私信广播
    chat_channel = 'chat.' + $('meta[name=code]').attr('content');
    window.Echo.private(chat_channel).listen('Chat', (e) => {
        layer.open({
            type: 1,
            title: '来自' + e.user.username,
            shade: false,
            skin: 'layer-inform',
            area: ['340px', '215px'],
            offset: 'rb', //右下角弹出
            time: 10000, //10秒后自动关闭
            anim: 2,
            resize: false,
            content: e.message.content,
            btn: ['查看', '忽略'],
            yes: function (index, layero) {
                $(location).prop('href', '/chat/' + e.user.id);
                layer.close(index);
            },
            btn2: function (index, layero) {
                layer.close(index);
            }
        });
    });
    // 退出登录
    $('.logout').click(function (event) {
        $.ajax({
            url: '/logout',
            type: 'GET',
            dataType: "json",
            success: function (data) {
                if (data.status === 10000) {
                    layer.msg(data.res, {icon: 1}, function () {
                        window.location.reload();
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
});

