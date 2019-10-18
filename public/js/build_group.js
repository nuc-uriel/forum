$(function() {
    // 图标修改
    var set_icon = null;
    save_icon = function(path) {
        layer.close(set_icon);
        var data = $('.icon-set > img').cropper('getData');
        data.path = path;
        $.ajax({
            url: "/group/icon/save",
            type: 'POST',
            data: data,
            dataType: "json",
            success: function(data) {
                if (data.status === 10000) {
                    $('.path').val(data.res);
                    $('.icon_show').attr({
                        src: data.res
                    }).show();
                    $('.update_icon').show();
                    $('.add_icon').hide();
                } else if (data.status === 20001) {
                    layer.alert(data.res, {
                            title: '提示',
                            skin: 'layui-layer-lan',
                            closeBtn: 0,
                            anim: 6,
                            icon: 0
                        },
                        function() {
                            $(location).prop('href', '/login');
                        });
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
            error: function(data) {
                layer.alert('网络繁忙，请稍后再试！', {
                    title: '提示',
                    skin: 'layui-layer-lan',
                    closeBtn: 0,
                    anim: 6,
                    icon: 0
                });
            }
        });
    };
    $('.icon').change(function(event) {
        layer.close(set_icon);
        $.ajax({
            url: "/group/icon/set",
            type: 'POST',
            data: new FormData($('.base-info form')[0]),
            cache: false,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function(data) {
                if (data.status === 10000) {
                    set_icon = layer.open({
                        type: 1,
                        title: '图标设置',
                        closeBtn: 1,
                        shadeClose: false,
                        skin: 'icon-class',
                        maxWidth: 600,
                        content: '<div class="icon-set"><img src="' + data.res + '" alt=""/></div><div class="icon-opt"><a href="javascript:void(0);" onclick="save_icon(\'' + data.res + '\')">保存图标</a><a href="javascript:void(0);" title="" onclick="$(\'.icon\').click();">重新上传</a></div>'
                    });
                    $('.icon-set > img').cropper({
                        aspectRatio: 1 / 1,
                        background: false,
                        minContainerWidth: 600,
                        minContainerHeight: 300
                    });
                } else if (data.status === 20001) {
                    layer.alert(data.res, {
                            title: '提示',
                            skin: 'layui-layer-lan',
                            closeBtn: 0,
                            anim: 6,
                            icon: 0
                        },
                        function() {
                            $(location).prop('href', '/login');
                        });
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
            error: function(data) {
                layer.alert('网络繁忙，请稍后再试！', {
                    title: '提示',
                    skin: 'layui-layer-lan',
                    closeBtn: 0,
                    anim: 6,
                    icon: 0
                });
            },
        })
    });
    // 表单提交
    $('#group-form').submit(function() {
        var loading = layer.load(2);
        $.ajax({
            url: '/group/build',
            type: 'post',
            data: $('#group-form').serialize(),
            dataType: 'json',
            success: function(data) {
                $('.error-tip').hide();
                if (data.status === 20001) {
                    layer.alert(data.res, {
                            title: '提示',
                            skin: 'layui-layer-lan',
                            closeBtn: 0,
                            anim: 6,
                            icon: 0
                        },
                        function() {
                            $(location).prop('href', '/login');
                        });
                }else if (data.status === 10000) {
                    layer.alert(data.res, {
                            title: '提示',
                            skin: 'layui-layer-lan',
                            closeBtn: 0,
                            anim: 1,
                            icon: 1
                        },
                        function() {
                            $(location).prop('href', '/index');
                        });
                }
            },
            error: function(data) {
                $('.error-tip').hide();
                if (data.status == 422) {
                    responseJSON = $.parseJSON(data.responseText);
                    $.each(responseJSON, function(k, v) {
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
            complete: function() {
                layer.close(loading);
            }
        });
        return false;
    });
})