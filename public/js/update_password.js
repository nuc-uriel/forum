$(function () {
    $('#update-pass').submit(function () {
        var loading = layer.load(2);
        $.ajax({
            url: '/member/password/update',
            type: 'post',
            data: $('#update-pass').serialize(),
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