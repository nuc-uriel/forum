$(function () {
    $('#reset-pass-1').submit(function () {
        var loading = layer.load(2);
        $.ajax({
            url: '/member/password/reset/1',
            type: 'post',
            data: $('#reset-pass-1').serialize(),
            dataType: 'json',
            success: function (data) {
                $('.error-tip').hide();
                if (data.status === 10000) {
                    layer.msg(data.res, {icon: 1});
                }else {
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
                $('.error-tip').hide();
                if (data.status == 422) {
                    responseJSON = $.parseJSON(data.responseText);
                    $.each(responseJSON, function (k, v) {
                        $('.' + k).parents('tr').children('.error-tip').text(v).show();
                    });
                } else {
                    layer.alert('网络繁忙，请稍后再试！', {
                        title: '提示',
                        skin: 'layui-layer-lan',
                        closeBtn: 0,
                        anim: 6,
                        icon: 0
                    });
                }
            },
            complete: function () {
                layer.close(loading);
            }
        });
        return false;
    });

    $('#reset-pass-2').submit(function () {
        var loading = layer.load(2);
        $.ajax({
            url: '/member/password/reset/2',
            type: 'post',
            data: $('#reset-pass-2').serialize(),
            dataType: 'json',
            success: function (data) {
                $('.error-tip').hide();
                if (data.status === 10000) {
                    layer.alert(data.res, {
                            title: '提示',
                            skin: 'layui-layer-lan',
                            closeBtn: 0,
                            anim: 1,
                            icon: 1
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
                $('.error-tip').hide();
                if (data.status == 422) {
                    responseJSON = $.parseJSON(data.responseText);
                    $.each(responseJSON, function (k, v) {
                        $('.' + k).parents('tr').children('.error-tip').text(v).show();
                    });
                } else {
                    layer.alert('网络繁忙，请稍后再试！', {
                        title: '提示',
                        skin: 'layui-layer-lan',
                        closeBtn: 0,
                        anim: 6,
                        icon: 0
                    });
                }
            },
            complete: function () {
                layer.close(loading);
            }
        });
        return false;
    });
});