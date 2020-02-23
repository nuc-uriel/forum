<?php
/**
 * Created by PhpStorm.
 * User: uriel
 * Date: 2019/5/10 0010 0024
 * Time: 15:57
 */

namespace App\Admin\Extensions;

use Encore\Admin\Admin;

class Apply
{
    protected $id;
    protected $status;

    public function __construct($id, $status)
    {
        $this->id = $id;
        $this->status = $status;
    }

    protected function script()
    {
        return <<<SCRIPT
$('.grid-apply-row').on('click', function () {
    $.ajax({
      method: 'get',
      url: '/admin/groups/apply?id=' + $(this).data('id') + '&status=' + $(this).data('status'),
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
});

SCRIPT;
    }

    protected function render()
    {
        Admin::script($this->script());
        if ($this->status == 'pass') {
            return "<a href='javascript:void(0);' class='grid-apply-row' title='通过申请' data-id='{$this->id}' data-status='{$this->status}'><i class='fa fa-check'></i></a>";
        } else {
            return "<a href='javascript:void(0);' class='grid-apply-row' title='拒绝申请' data-id='{$this->id}' data-status='{$this->status}'><i class='fa fa-close'></i></a>";
        }
    }

    public function __toString()
    {
        return $this->render();
    }
}
