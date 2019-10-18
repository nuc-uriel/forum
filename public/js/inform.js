$(function () {
    $("#records").niceScroll({cursorborder: "", cursorcolor: "#e0e0e0", boxzoom: true});
    $("#records").getNiceScroll(0).doScrollTop(($("#records li").length + 10) * 100);

    $(".dispose").click(function (event) {
        event.stopPropagation();
        url = $(this).attr('href');
        $.ajax({
            url: url,
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
    });

    $('#send-msg').submit(function (event) {
        uid = $('meta[name=uid]').attr('content');
        data = $(this).serialize() + '&uid=' + uid;
        content = $('.say-in').val();
        date = new Date();
        var strDate = date.getFullYear() + "-";
        strDate += date.getMonth() + 1 + "-";
        strDate += date.getDate() + " ";
        strDate += date.getHours() + ":";
        strDate += date.getMinutes() + ":";
        strDate += date.getSeconds();
        avatar = $('.user-info img').attr('src');
        li = $('<li class="myself"><div><p>' + content + '</p><div class="date">' + strDate + '</div></div><img src="' + avatar + '" alt=""></li>');
        $('#records').append(li);
        $('.say-in').val("");
        setTimeout(function () {
            $("#records").getNiceScroll(0).doScrollTop(($("#records li").length + 10) * 100);
        }, 100);
        return false;
    });
});