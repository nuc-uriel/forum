$(function () {
    Highcharts.setOptions({global: {useUTC: false}});

    // 标签切换
    $('.tabs a').click(function (event) {
        tab = $(this).attr('tab');
        $(location).prop('href', '/group/edit/' + tab + '?gid=' + $.getUrlParam('gid'));
    });
    // $('.tabs a').click(function (event) {
    //     $('.tabs a').removeClass('active');
    //     $(this).addClass('active');
    //     tab = $(this).attr('tab');
    //     $('.base-info, .member-edit, .data-statistics, .ban-manage, .other-set, .log').hide();
    //     $('.go-group, .group-info-in, .member-search-tips, .member-search, .member-edit-tips, .ban-manage-tip, .log-tips, .set-admin').hide();
    //     $('.' + tab).show();
    //     switch (tab) {
    //         case 'base-info':
    //         case 'other-set':
    //         case 'data-statistics':
    //             $('.go-group').show();
    //             $('.page').hide();
    //             break;
    //         case 'member-edit':
    //             $('.group-info-in').show();
    //             $('.ember-search-tips').show();
    //             $('.member-search').show();
    //             $('.member-edit-tips').show();
    //             $('.set-admin').show();
    //             $('.page').show();
    //             break;
    //         case 'ban-manage':
    //             $('.ban-manage-tip').show();
    //             $('.go-group').show();
    //             $('.page').show();
    //             break;
    //         case 'log':
    //             $('.log-tips').show();
    //             $('.go-group').show();
    //             $('.page').show();
    //             break;
    //     }
    // });
    // 图标修改
    var set_icon = null;
    save_icon = function (path) {
        layer.close(set_icon);
        var data = $('.icon-set > img').cropper('getData');
        data.path = path;
        $.ajax({
            url: "/group/icon/save",
            type: 'POST',
            data: data,
            dataType: "json",
            success: function (data) {
                if (data.status === 10000) {
                    $('.path').val(data.res);
                    $('.icon_show').attr({
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
    $('.icon').change(function (event) {
        layer.close(set_icon);
        data = new FormData($('.base-info form')[0]);
        $.ajax({
            url: "/group/icon/set",
            type: 'POST',
            data: data,
            cache: false,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function (data) {
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

    // 表单提交
    $('#base-info-form').submit(function () {
        var loading = layer.load(2);
        data = $('#base-info-form').serialize() + '&gid=' + $.getUrlParam('gid');
        $.ajax({
            url: '/group/edit',
            type: 'post',
            data: data,
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
                } else if (data.status === 10000) {
                    layer.msg(data.res, {icon: 1});
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

    // 设置加入方式
    $('.member-add-mode form').submit(function () {
        var loading = layer.load(2);
        data = $('.member-add-mode form').serialize() + '&gid=' + $.getUrlParam('gid');
        $.ajax({
            url: '/group/set_join_way',
            type: 'get',
            data: data,
            dataType: 'json',
            success: function (data) {
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
                } else if (data.status === 10000) {
                    layer.msg(data.res, {icon: 1}, function () {
                        $('.join_way_show').text($('input[name=join_way]:checked').next('span').text());
                        $('.member-add-mode').slideUp();
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

    // 友情小组联想词
    function set_associational_groups() {
        keyword = $('.add-friendship-in').val();
        $('.associational-groups').empty().show();
        $.ajax({
            url: '/group/associational_groups?keyword=' + keyword,
            type: 'get',
            dataType: 'json',
            success: function (data) {
                if (data.status === 10000) {
                    $.each(data.res, function (k, v) {
                        $('.associational-groups').append('<li>' + v + '</li>');
                    });
                }
            }
        });
    };
    $('.add-friendship-in').on({
        input: function (e) {
            var flag = e.target.isNeedPrevent;
            if (flag) return;
            set_associational_groups();
        },
        compositionstart: function (e) {
            e.target.isNeedPrevent = true;
        },
        compositionend: function (e) {
            e.target.isNeedPrevent = false;
        }
    });
    $('.add-friendship-in').focus(function (event) {
        set_associational_groups();
        $('.associational-groups').show();
    });
    $('.add-friendship-in').blur(function (event) {
        $('.associational-groups').slideUp();
    });
    $('.associational-groups').delegate('li', 'click', function () {
        $('.add-friendship-in').val($(this).text());
    });
    $('.associational-groups').delegate('li', 'mouseover', function () {
        $(this).siblings().removeClass('selected');
        $(this).addClass('selected');
    });

    // 设置友情小组
    $('.add-friendship form').submit(function () {
        var loading = layer.load(2);
        data = $('.add-friendship form').serialize() + '&gid=' + $.getUrlParam('gid');
        $.ajax({
            url: 'group/friendship/add',
            type: 'get',
            data: data,
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
        return false;
    });

    // 删除友情小组
    $('.del-friendship').click(function () {
        _this = this;
        layer.confirm('确定解除与' + $(this).parents('li').find('>div>a').text() + '小组的关系吗？', {
            icon: 3,
            title: '提示'
        }, function (index) {
            var data = {
                gid: $.getUrlParam('gid'),
                fid: $(_this).parents('li').attr('fid')
            };
            $.ajax({
                url: '/group/friendship/del',
                type: 'get',
                data: data,
                dataType: 'json',
                success: function (data) {
                    if (data.status === 10000) {
                        layer.msg(data.res, {icon: 1}, function () {
                            $(_this).parents('li').empty().append('<a href="javascript:void(0);" title="" onclick="$(\'.add-friendship\').slideToggle()" class="add-friendship-but">+</a>');
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

    // 通过申请
    $('.pass-apply').click(function (event) {
        var data = {
            gid: $.getUrlParam('gid'),
            uid: $(this).parents('.member-item').attr('uid')
        };
        $.ajax({
            url: "/group/member/apply/pass",
            type: 'GET',
            data: data,
            dataType: "json",
            success: function (data) {
                if (data.status === 10000) {
                    layer.msg(data.res, {icon: 1}, function () {window.location.reload();});
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

    // 拒绝申请
    $('.refuse-apply').click(function (event) {
        var data = {
            gid: $.getUrlParam('gid'),
            uid: $(this).parents('.member-item').attr('uid')
        };
        layer.confirm('确定' + $(this).attr('title') + '吗？', {
            icon: 3,
            title: '提示'
        }, function (index) {
            $.ajax({
                url: "/group/member/apply/refuse",
                type: 'GET',
                data: data,
                dataType: "json",
                success: function (data) {
                    if (data.status === 10000) {
                        layer.msg(data.res, {icon: 1}, function () {window.location.reload();});
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

    // 转让小组
    $('.set-leader').click(function (event) {
        var data = {
            gid: $.getUrlParam('gid'),
            uid: $(this).parents('.member-item').attr('uid')
        };
        layer.confirm('确定' + $(this).attr('title') + '吗？', {
            icon: 3,
            title: '提示'
        }, function (index) {
            $.ajax({
                url: "/group/member/leader/set",
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

    // 任命管理员
    $('.appoint-admin').click(function (event) {
        var data = {
            gid: $.getUrlParam('gid'),
            uid: $(this).parents('.member-item').attr('uid')
        };
        layer.confirm('确定' + $(this).attr('title') + '吗？', {
            icon: 3,
            title: '提示'
        }, function (index) {
            $.ajax({
                url: "/group/member/admin/appoint",
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

    // 撤销管理员
    $('.revocation-admin').click(function (event) {
        var data = {
            gid: $.getUrlParam('gid'),
            uid: $(this).parents('.member-item').attr('uid')
        };
        layer.confirm('确定' + $(this).attr('title') + '吗？', {
            icon: 3,
            title: '提示'
        }, function (index) {
            $.ajax({
                url: "/group/member/admin/revocation",
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

    // 踢出小组
    $('.del-member').click(function (event) {
        var data = {
            gid: $.getUrlParam('gid'),
            uid: $(this).parents('.member-item').attr('uid')
        };
        layer.confirm('确定' + $(this).attr('title') + '吗？', {
            icon: 3,
            title: '提示'
        }, function (index) {
            $.ajax({
                url: "/group/member/del",
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

    // 拉黑用户
    $('.add-blacklist').click(function (event) {
        var data = {
            gid: $.getUrlParam('gid'),
            uid: $(this).parents('.member-item').attr('uid')
        };
        layer.confirm('确定' + $(this).attr('title') + '吗？', {
            icon: 3,
            title: '提示'
        }, function (index) {
            $.ajax({
                url: "/group/member/blacklist/add",
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

    // 解除拉黑
    $('.del-blacklist').click(function (event) {
        var data = {
            gid: $.getUrlParam('gid'),
            uid: $(this).parents('.member-item').attr('uid')
        };
        layer.confirm('确定' + $(this).attr('title') + '吗？', {
            icon: 3,
            title: '提示'
        }, function (index) {
            $.ajax({
                url: "/group/member/blacklist/del",
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

    // 添加违禁词
    $('#ban-form').submit(function (event) {
        var data = $(this).serialize() + '&gid=' + $.getUrlParam('gid');
        $.ajax({
            url: "/group/ban/add",
            type: 'GET',
            data: data,
            dataType: "json",
            success: function (data) {
                $('#ban-form span').hide();
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
                        }
                    );
                }
            },
            error: function (data) {
                $('#ban-form span').hide();
                if (data.status == 422) {
                    responseJSON = $.parseJSON(data.responseText);
                    $('#ban-form span').text(responseJSON.word[0]).show();
                } else {
                    layer.alert('网络繁忙，请稍后再试！', {
                        title: '提示',
                        skin: 'layui-layer-lan',
                        closeBtn: 0,
                        anim: 6,
                        icon: 0
                    });
                }
            }
        });
        return false;
    });

    // 删除违禁词
    $('.del-ban-word').click(function (event) {
        var data = {
            gid: $.getUrlParam('gid'),
            bid: $(this).parents('tr').attr('bid')
        };
        layer.confirm('确定' + $(this).attr('title') + '吗？', {
            icon: 3,
            title: '提示'
        }, function (index) {
            $.ajax({
                url: "/group/ban/del",
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

    // 解除禁言和解封帖子
    $('.ban-opt').click(function (event) {
        url = $(this).attr('url');
        item = $(this).parents('tr');
        $.ajax({
            url: url,
            type: 'GET',
            dataType: "json",
            success: function (data) {
                if (data.status === 10000) {
                    layer.msg(data.res, {icon: 1}, function () {
                        item.remove();
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

    // 日志类型切换
    $('.log .select select').change(function () {
        $(location).prop('href', '/group/edit/log?gid=' + $.getUrlParam('gid') + '&type=' + $('.log .select option:selected').val());
    });

    // 图表统计
    chart = 'topic';
    range = 'week';

    $('.set-chart').click(function (event) {
        chart = $(this).attr('chart');
        $(this).parents('li').siblings().removeClass('active');
        $(this).parents('li').addClass('active');
        $(this).parents('li').find('div').removeClass('active');
        $(this).find('div').addClass('active');
        get_chart(chart, range);
    });

    $('.set-range').click(function (event) {
        range = $(this).attr('range');
        $(this).parents('ul').find('a').removeClass('active');
        $(this).addClass('active');
        get_chart(chart, range);
        get_all_chart(range);
    });

    function get_chart(chart, range) {
        data = {
            chart: chart,
            range: range,
            gid: $.getUrlParam('gid'),
        };
        $.ajax({
            url: "/group/chart",
            type: 'GET',
            data: data,
            dataType: "json",
            success: function (data) {
                chart_data = [];
                $.each(data, function (k, v) {
                    chart_data.push([k * 1000, v]);
                });
                switch (chart) {
                    case 'topic':
                        init_chart('讨论', chart_data);
                        break;
                    case 'comment':
                        init_chart('回应', chart_data);
                        break;
                    case 'in':
                        init_chart('成员', chart_data);
                        break;
                    case 'out':
                        init_chart('成员', chart_data);
                        break;
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
    }

    function get_all_chart(range) {
        data = {
            range: range,
            gid: $.getUrlParam('gid'),
        };
        $.ajax({
            url: "/group/chart/all",
            type: 'GET',
            data: data,
            dataType: "json",
            success: function (data) {
                chart_data = [];
                $.each(data, function (k, v) {
                    $('.chart-' + k).find('.sum').text(v);
                    chart_data.push([k * 1000, v]);
                });
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
    }

    function init_chart(type, data) {
        c = Highcharts.chart('line-chart', {
            title: {
                text: ''
            },
            legend: {
                enabled: false
            },
            xAxis: {
                type: 'datetime',
                dateTimeLabelFormats: {
                    day: '%m-%d'
                }
            },
            yAxis: {
                allowDecimals: false,
                title: {
                    text: ''
                },
            },
            tooltip: {
                dateTimeLabelFormats: {
                    day: '%Y-%m-%d'
                },
                pointFormat: '<span style="color:{point.color}">\u25CF</span> {series.name}: <b>{point.y}个' + type + '</b><br/>.'
            },
            plotOptions: {
                spline: {
                    marker: {
                        enable: true
                    }
                }
            },
            series: [{
                name: '',
                data: data
            }],
            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 700,
                        minWidth: 600
                    },
                }]
            }
        });
    }

    if ($('#line-chart').length !== 0) {
        get_chart(chart, range);
        get_all_chart(range);
    }
});