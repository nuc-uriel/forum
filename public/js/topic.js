$(function () {
    // 举报删除按钮显隐
    $(".reply").hover(function () {
        $(this).find('.report').show();
        $(this).find('.delete').show();
    }, function () {
        $(this).find('.report').hide();
        $(this).find('.delete').hide();
    });

    // 小组信息浮动显示
    var documentHeight = $(document).height();
    var windowsHeight = $(window).height();
    var old_top = $('.group').offset().top;
    var left = $('.group').offset().left;

    $(document).scroll(function (event) {
        var windowScrollTop = $(window).scrollTop();
        var top = $('.group').offset().top;
        var left = $('.group').offset().left;
        if (windowScrollTop >= top && documentHeight > windowScrollTop + windowsHeight) {
            $('.group').offset({top: windowScrollTop, left: left});
        } else if (windowScrollTop <= top && top >= old_top) {
            $('.group').offset({top: windowScrollTop, left: left});
        } else if (top <= old_top) {
        }
    });

    autosize(document.querySelectorAll('textarea'));

    $('.topic-admin-opt').click(function (event) {
        url = $(this).attr('url');
        layer.confirm('确定' + $(this).text() + '该讨论帖吗？', {
            icon: 3,
            title: '提示'
        }, function (index) {
            var data = {
                tid: $('meta[name=tid]').attr('content')
            };
            $.ajax({
                url: url,
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

    $('.topic-del').click(function (event) {
        url = '/topic/del';
        layer.confirm('确定删除该讨论帖吗？', {
            icon: 3,
            title: '提示'
        }, function (index) {
            var data = {
                tid: $('meta[name=tid]').attr('content')
            };
            $.ajax({
                url: url,
                type: 'GET',
                data: data,
                dataType: "json",
                success: function (data) {
                    if (data.status === 10000) {
                        layer.msg(data.res, {icon: 1}, function () {
                            $(location).prop('href', $('.group-top div a').attr('href'));
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

    $('.btn-reply').click(function (event) {
        $(this).parents('li').find('form').slideToggle();
    });

    $('.add-img, .upload-img').click(function (event) {
        $(this).parents('form').find('.img-in').click();
    });

    $('.img-in').change(function () {
        var file = this.files[0];
        var show = $(this).parents('form').find('.img-show').hide();
        if ($.inArray(file.type, ["image/jpeg", "image/png", "image/gif"]) === -1) {
            layer.alert("图片类型不允许,请上传常规的图片(jpg、png、gif)文件", {
                title: '提示',
                skin: 'layui-layer-lan',
                closeBtn: 0,
                anim: 6,
                icon: 0
            });
            $(this).val("");
            $(this).parents('form').find('.img-show').hide();
            $(this).parents('form').find('.img-del').hide();
            $(this).parents('form').find('.add-img').show();
            return;
        }
        if (file.size > 3 * 1024 * 1024) {
            layer.alert(data.res, {
                title: '提示',
                skin: 'layui-layer-lan',
                closeBtn: 0,
                anim: 6,
                icon: 0
            });
            $(this).val("");
            $(this).parents('form').find('.img-show').hide();
            $(this).parents('form').find('.img-del').hide();
            $(this).parents('form').find('.add-img').show();
            return;
        }
        var reader = new FileReader();
        reader.onload = function (event) {
            var txt = event.target.result;
            show.attr('src', txt);
        };
        reader.readAsDataURL(file);
        $(this).parents('form').find('.img-show').show();
        $(this).parents('form').find('.img-del').show();
        $(this).parents('form').find('.add-img').hide();
    });

    $('.img-del').click(function (event) {
        $(this).parents('form').find('.img-in').val("");
        $(this).parents('form').find('.img-show').hide();
        $(this).parents('form').find('.img-del').hide();
        $(this).parents('form').find('.add-img').show();
    });
    // 评论
    $('.add-comment').submit(function (event) {
        var loading = layer.load(2);
        $.ajax({
            url: "/topic/comment/add",
            type: 'POST',
            data: new FormData($(this)[0]),
            cache: false,
            processData: false,
            contentType: false,
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
                if (data.status == 422) {
                    responseJSON = $.parseJSON(data.responseText);
                    $.each(responseJSON, function (k, v) {
                        layer.alert(v, {
                            title: '提示',
                            skin: 'layui-layer-lan',
                            closeBtn: 0,
                            anim: 6,
                            icon: 0
                        });
                        return false;
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

    // 删除评论
    $('.delete').click(function (event) {
        url = '/topic/comment/del/' + $(this).attr('cid');
        layer.confirm('确定删除该评论吗？', {
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

    // 点赞
    $(".opt-like").click(function (event) {
        content = $(this).text().trim();
        count = parseInt(content.match(/[0-9]+/)[0]);
        type = $(this).attr('type');
        if (content.startsWith('赞')) {
            url = '/topic/like/add/' + $(this).attr('tid');
            content = '已赞(' + ++count + ')';
        } else {
            url = '/topic/like/del/' + $(this).attr('tid');
            content = '赞(' + --count + ')';
        }
        data = {
            'type': $(this).attr('type'),
        };
        like = $(this);
        $.ajax({
            url: url,
            type: 'GET',
            data: data,
            dataType: "json",
            success: function (data) {
                if (data.status === 10000) {
                    if (type == 0 && content.startsWith('赞')) {
                        like.removeClass('liked');
                    } else if (type == 0 && !content.startsWith('赞')) {
                        like.addClass('liked');
                    }
                    like.text(content);
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
            }
        });
    });

    // 收藏
    $(".save").click(function (event) {
        content = $(this).text().trim();
        ;
        count = parseInt(content.match(/[0-9]+/)[0]);
        type = $(this).attr('type');
        if (content.startsWith('已')) {
            url = '/topic/collect/del/' + $(this).attr('tid');
            content = '收藏(' + --count + ')';
        } else {
            url = '/topic/collect/add/' + $(this).attr('tid');
            content = '已收藏(' + ++count + ')';
        }
        collect = $(this);
        $.ajax({
            url: url,
            type: 'GET',
            dataType: "json",
            success: function (data) {
                if (data.status === 10000) {
                    if (content.startsWith('已')) {
                        collect.addClass('saved');
                    }
                    {
                        collect.removeClass('saved');
                    }
                    collect.text(content);
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
            }
        });
    });

});