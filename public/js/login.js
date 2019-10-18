$(function () {
    $('#login_form').submit(function () {
        var loading = layer.load(2);
        $.ajax({
            url: '/login',
            type: 'post',
            data: $('#login_form').serialize(),
            dataType: 'json',
            success: function (data) {
                $('.error_tip').hide();
                if (data.status === 10000) {
                    layer.msg(data.res, {icon: 1}, function () {$(location).prop('href', '/index');});
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
                $('.error_tip').hide();
                if (data.status == 422) {
                    responseJSON = $.parseJSON(data.responseText);
                    $.each(responseJSON, function (k, v) {
                        $('.' + k).next('.error_tip').text(v).show();
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