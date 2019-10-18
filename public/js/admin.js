$(function () {
    $('.grid-show-row').on('click', function () {
        layer.open({
            type: 2,
            title: false,
            shadeClose: true,
            shade: 0.8,
            area: ['750px', '500px'],
            fixed: false,
            resize: false,
            content: '/admin/' + $(this).data('type') + '/show/' + $(this).data('id')
        });
    });

    $('#group-reset').submit(function ($event) {
        $.ajax({
            method: 'post',
            url: '',
            data: $(this).serialize(),
            dataType:'json',
            success: function (data) {
                if (typeof data === 'object') {
                    if (data.status) {
                        parent.$("#reset-group-res").attr('content', 'OK');
                    } else {
                        parent.$("#reset-group-res").attr('content', data.message);
                    }
                }
            },
            error:function(data){
                parent.$("#reset-group-res").attr('content', '操作失败！');
            },
            complete: function () {
                var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                parent.layer.close(index); //再执行关闭
            }
        });
        return false;
    });
});