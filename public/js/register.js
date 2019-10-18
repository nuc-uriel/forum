$(function () {
    $('#username').blur(function (event) {
        $('#username').next().hide();
        $.get('/check_name?username=' + $('#username').val(), function (data) {
            if (data.status === 20002) {
                $('#username').next().text(data.res).show();
            }
        }, 'json');
    });

    $('#reg_form').submit(function () {
        var loading = layer.load(2);
        $.ajax({
            url: '/register',
            type: 'post',
            data: $('#reg_form').serialize(),
            dataType: 'json',
            success: function (data) {
                $('.error_tip').hide();
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
                    $('.captcha img').click();
                    $('.captcha input').val('');
                }
            },
            error: function (data) {
                $('.error_tip').hide();
                if (data.status == 422) {
                    responseJSON = $.parseJSON(data.responseText);
                    $.each(responseJSON, function (k, v) {
                        $('#' + k).parents('li').children('.error_tip').text(v).show();
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
                $('.captcha img').click();
                $('.captcha input').val('');
            },
            complete: function () {
                layer.close(loading);
            }
        });
        return false;
    });
});