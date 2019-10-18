<?php
/**
 * Created by PhpStorm.
 * User: uriel
 * Date: 2019/5/10 0010 0024
 * Time: 21:59
 */

namespace App\Admin\Extensions;


use Encore\Admin\Admin;

class ResetGroupAdmin
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    protected function script()
    {
        return <<<SCRIPT
$('.grid-reset-row').on('click', function () {
    layer.open({
      type: 2,
      title: false,
      shadeClose: true,
      shade: 0.8,
      area: ['430px', '330px'],
      fixed: false,
      resize: false,
      scrollbar: false,
      content: '/admin/groups/reset/'+ $(this).data('id'),
      end:function(){
        $.pjax.reload('#pjax-container');
        var res = $('#reset-group-res').attr('content');
        if(res == 'OK'){
            toastr.success('操作成功！');
        } else {
            toastr.error(res);
        }
      }
    });
});
SCRIPT;
    }

    protected function render()
    {
        Admin::script($this->script());

        return "<a href='javascript:void(0);' class='grid-reset-row' title='撤销组长' data-id='{$this->id}'><i class='fa fa-repeat'></i></a>";
    }

    public function __toString()
    {
        return $this->render();
    }
}