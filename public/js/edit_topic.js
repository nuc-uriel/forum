$(function () {
    // var old_top = $('.w-e-toolbar').offset().top;
    var old_top = 190;
    $(document).scroll(function (event) {
        var documentHeight = $(document).height();
        var windowsHeight = $(window).height();
        var windowScrollTop = $(window).scrollTop();
        var top = $('.w-e-toolbar').offset().top;
        if (windowScrollTop + 44 >= top && documentHeight > windowScrollTop + windowsHeight) {
            $('.w-e-toolbar').offset({top: windowScrollTop + 44});
        } else if (windowScrollTop + 44 <= top && top >= old_top) {
            $('.w-e-toolbar').offset({top: windowScrollTop + 44});
        } else if (top <= old_top) {
        }
    });

    $('.w-e-text').scroll(function (event) {
        height = $('.w-e-text').height();
        scrollTop = $('.w-e-text').scrollTop();
        if (scrollTop > 0) {
            $('.w-e-text-container').height(height + scrollTop)
        }
    });

    $('.submit-for-form').click(function (event) {
        var data = $('#add-topic-form').serialize();
        $.ajax({
            url: "",
            type: 'POST',
            data: data,
            dataType: "json",
            success: function (data) {
                if (data.status === 10000) {
                    layer.msg(data.res, {icon: 1}, function () {
                        $(location).prop('href', '/topic/' + data.tid);
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

    $('.preview').click(function (event) {
        title = $('.title-in').val();
        content = _wangeditor.txt.html();
        $('.preview-container h3').text(title);
        $('.content-show').html(content);
        $('.group-info, #add-topic-form, .preview').hide();
        $('.preview-container, .edit').show();
    });

    $('.edit').click(function (event) {
        $('.group-info, #add-topic-form, .preview').show();
        $('.preview-container, .edit').hide();
    });
});