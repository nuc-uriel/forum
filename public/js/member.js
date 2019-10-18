$(function () {
    // $(location)[0].search)
    // 标签切换
    $('.opt a').click(function (event) {
        var tab = $(this).attr('tab');
        console.log(!$.getUrlParam('uid'));
        if(!$.getUrlParam('uid')){
            $(location).prop('href', '/member/' + tab);
        }else{
            $(location).prop('href', '/member/' + tab + '?uid=' + $.getUrlParam('uid'));
        }
    });
    // $('.opt a').click(function (event) {
    //     $('.opt a').removeClass('active');
    //     $(this).addClass('active');
    //     tab = $(this).attr('tab');
    //     switch (tab) {
    //         case 'issue':
    //         case 'respond':
    //         case 'collect':
    //             $('.topic-list').show();
    //             $('.page').show();
    //             $('.member-list').hide();
    //             $('.base-info').hide();
    //             break;
    //         case 'idol':
    //         case 'fans':
    //         case 'blacklist':
    //             $('.topic-list').hide();
    //             $('.page').show();
    //             $('.member-list').show();
    //             $('.base-info').hide();
    //             break;
    //         case 'setting':
    //             $('.topic-list').hide();
    //             $('.page').hide();
    //             $('.member-list').hide();
    //             $('.base-info').show();
    //             break;
    //     }
    // });
    // 签名设置
    $('.set-signature, .add-signature').click(function () {
        $('.signature-form .set-in').val($('.signature-content').text());
        $('.signature-form').show();
        $('.set-signature, .add-signature').hide();
        $('.signature-content').hide();
    });
    $('.signature-form').submit(function (event) {
        $.ajax({
            url: "/member/signature/set",
            type: 'GET',
            data: $('.signature-form').serialize(),
            dataType: "json",
            success: function (data) {
                if (data.status === 10000) {
                    layer.msg(data.res, {icon: 1}, function () {
                        signature = $('.signature-form .set-in').val();
                        if (signature) {
                            $('.set-signature').show();
                        } else {
                            $('.add-signature').show();
                        }
                        $('.signature-form').hide();
                        $('.signature-content').text(signature);
                        $('.signature-content').show();
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
        return false;
    });
    $('.signature-form .set-cancer').click(function (event) {
        var signature = $('.signature-content').text();
        if (signature) {
            $('.set-signature').show();
        } else {
            $('.add-signature').show();
        }
        $('.signature-form').hide();
        $('.signature-content').show();
    });

    // 介绍设置
    if (!$('.set-introduce-in').isNull) {
        autosize(document.querySelectorAll('.set-introduce-in'));
    }
    $('.set-introduce, .add-introduce').click(function () {
        $('.introduce-form .set-introduce-in').val($('.introduce-content').text());
        $('.introduce-form').show();
        $('.set-introduce, .add-introduce').hide();
        $('.introduce-content').hide();
        $('.introduce-form .set-introduce-in').focus();
    });
    $('.introduce-form').submit(function (event) {
        $.ajax({
            url: "/member/introduce/set",
            type: 'POST',
            data: $('.introduce-form').serialize(),
            dataType: "json",
            success: function (data) {
                if (data.status === 10000) {
                    layer.msg(data.res, {icon: 1}, function () {introduce = $('.introduce-form .set-introduce-in').val();
                        if (introduce) {
                            $('.set-introduce').show();
                        } else {
                            $('.add-introduce').show();
                        }
                        $('.introduce-form').hide();
                        $('.introduce-content').text(introduce);
                        $('.introduce-content').show();});
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
        return false;
    });
    $('.introduce-form .set-introduce-cancer').click(function (event) {
        introduce = $('.introduce-content').text();
        if (introduce) {
            $('.set-introduce').show();
        } else {
            $('.add-introduce').show();
        }
        $('.introduce-form').hide();
        $('.introduce-content').show();
    });
    // 居住地选择
    var places = {};
    var set_places = null;
    select_place = function (a, k, key) {
        $('.place').val(places[k]['name'] + ',' + places[k]['sub'][key]['name']);
        layer.close(set_places);
    }
    set_city = function (a, k) {
        $('.city').empty();
        $(places[k]['sub']).each(function (key, val) {
            if (val.name != '请选择' && val.name != '其他') {
                $('.city').append($('<a href="javascript:void(0);" onclick="select_place(this, ' + k + ', ' + key + ');">' + val.name + '</a>'));
            }
        });
    };
    $('.place').click(function (event) {
        $.get('/member/places/get', function (data) {
            places = data;
            content = '<div class="province">';
            $(data).each(function (k, v) {
                if (v.name != '请选择' && v.name != '其他' && v.name != '海外') {
                    content += '<a href="javascript:void(0);" onclick="set_city(this, ' + k + ');">' + v.name + '</a>';
                }
            });
            content += '</div><div class="city"></div>';
            set_places = layer.open({
                type: 1,
                title: '选择你的长居地',
                closeBtn: 1,
                shadeClose: false,
                skin: 'place-class',
                maxWidth: 600,
                content: content
            });
        }, 'json');
    });

    // 头像修改
    var set_head = null;
    save_head = function (path) {
        layer.close(set_head);
        var data = $('.head-set > img').cropper('getData');
        data.path = path;
        $.ajax({
            url: "/member/head/save",
            type: 'POST',
            data: data,
            dataType: "json",
            success: function (data) {
                if (data.status === 10000) {
                    $('.head_portrait_img').attr({
                        src: data.res
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
    };
    $('.head_portrait').change(function (event) {
        layer.close(set_head);
        $.ajax({
            url: "/member/head/set",
            type: 'POST',
            data: new FormData($('.base-info form')[0]),
            cache: false,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function (data) {
                if (data.status === 10000) {
                    set_head = layer.open({
                        type: 1,
                        title: '头像设置',
                        closeBtn: 1,
                        shadeClose: false,
                        skin: 'head-class',
                        maxWidth: 600,
                        content: '<div class="head-set"><img src="' + data.res + '" alt=""/></div><div class="head-opt"><a href="javascript:void(0);" onclick="save_head(\'' + data.res + '\')">保存头像</a><a href="javascript:void(0);" title="" onclick="$(\'.head_portrait\').click();">重新上传</a></div>'
                    });
                    $('.head-set > img').cropper({
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
        })
    });
    // 邮箱修改
    $('.set-email').click(function (event) {
        $('.send-email, .set-email, .email-show').hide();
        $('.email-in').show();
    });
    // 个人信息修改
    $('#base-info').submit(function () {
        var loading = layer.load(2);
        $.ajax({
            url: '/member/update',
            type: 'post',
            data: $('#base-info').serialize(),
            dataType: 'json',
            success: function (data) {
                $('.error-tip').hide();
                if (data.status === 20001) {
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
                } else if (data.status === 10000 || data.status === 10001) {
                    layer.alert(data.res, {
                        title: '提示',
                        skin: 'layui-layer-lan',
                        closeBtn: 0,
                        anim: 1,
                        icon: 1
                    });
                    $('.email-show').text($('.email-in').val());
                    $('.email-in').hide();
                    $('.email-show, .set-email').show();
                } else {
                    layer.alert(data.res, {
                        title: '提示',
                        skin: 'layui-layer-lan',
                        closeBtn: 0,
                        anim: 6,
                        icon: 0
                    });
                }
                if (data.status === 10001) {
                    $('.send-email').show();
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
    // 邮箱激活
    $('.send-email').click(function () {
        var loading = layer.load(2);
        $.ajax({
            url: '/member/activate/send_email',
            type: 'get',
            dataType: 'json',
            success: function (data) {
                if (data.status === 10000) {
                    layer.msg(data.res, {icon: 1});

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
            complete: function () {
                layer.close(loading);
            }
        });
    });
    // 添加关注
    $('.add-idol').click(function () {
        var loading = layer.load(2);
        var data = {
            uid: $.getUrlParam('uid')
        };
        $.ajax({
            url: '/member/idol/add',
            type: 'get',
            dataType: 'json',
            data: data,
            success: function (data) {
                if (data.status === 10000) {
                    $('.add-idol').hide();
                    $('.del-idol').show();
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
            complete: function () {
                layer.close(loading);
            }
        });
    });
    // 取消关注
    $('.del-idol').click(function () {
        var loading = layer.load(2);
        var data = {
            uid: $.getUrlParam('uid')
        };
        $.ajax({
            url: '/member/idol/del',
            type: 'get',
            dataType: 'json',
            data: data,
            success: function (data) {
                if (data.status === 10000) {
                    $('.del-idol').hide();
                    $('.add-idol').show();
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
            complete: function () {
                layer.close(loading);
            }
        });
    });
    // 添加黑名单
    $('.add-blacklist').click(function () {
        var loading = layer.load(2);
        var data = {
            uid: $.getUrlParam('uid')
        };
        $.ajax({
            url: '/member/blacklist/add',
            type: 'get',
            dataType: 'json',
            data: data,
            success: function (data) {
                if (data.status === 10000) {
                    $('.add-blacklist, .add-idol, .del-idol, .chat').hide();
                    $('.del-blacklist').show();
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
            complete: function () {
                layer.close(loading);
            }
        });
    });
    // 取消黑名单
    $('.del-blacklist').click(function () {
        var loading = layer.load(2);
        var data = {
            uid: $.getUrlParam('uid')
        };
        $.ajax({
            url: '/member/blacklist/del',
            type: 'get',
            dataType: 'json',
            data: data,
            success: function (data) {
                if (data.status === 10000) {
                    $('.del-blacklist').hide();
                    $('.add-blacklist, .chat, .add-idol').show();
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
            complete: function () {
                layer.close(loading);
            }
        });
    });
});