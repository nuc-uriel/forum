<?php
/**
 * Created by PhpStorm.
 * User: uriel
 * Date: 2019/5/10 0010 0024
 * Time: 15:57
 */

namespace App\Admin\Extensions;

use Encore\Admin\Admin;

class Ban
{
    protected $id;
    protected $type;
    protected $typeMap = array(
        'users'=>'用户',
        'topics'=>'讨论',
        'groups'=>'小组',
    );

    public function __construct($id, $type)
    {
        $this->id = $id;
        $this->type = $type;
    }

    protected function script()
    {
        return <<<SCRIPT
$('.grid-ban-row').on('click', function () {
if (confirm("确认封禁该{$this->typeMap[$this->type]}吗?")) {
    $.ajax({
      method: 'get',
      url: '/admin/{$this->type}/ban?id=' + $(this).data('id') + '&status=ban',
      success: function(data) {
        $.pjax.reload('#pjax-container');

        if (typeof data === 'object') {
          if (data.status) {
            toastr.success(data.message);
          } else {
            toastr.error(data.message);
          }
        }
      }
    });
  }
});

SCRIPT;
    }

    protected function render()
    {
        Admin::script($this->script());

        return "<a href='javascript:void(0);' class='grid-ban-row' title='封禁{$this->typeMap[$this->type]}' data-id='{$this->id}'><i class='fa fa-ban'></i></a>";
    }

    public function __toString()
    {
        return $this->render();
    }
}
