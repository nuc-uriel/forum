<?php
/**
 * Created by PhpStorm.
 * User: uriel
 * Date: 2019/5/10 0010 0024
 * Time: 21:59
 */

namespace App\Admin\Extensions;


use Encore\Admin\Admin;

class Lift
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
$('.grid-lift-row').on('click', function () {
$.ajax({
      method: 'get',
      url: '/admin/{$this->type}/ban?id=' + $(this).data('id') + '&status=lift',
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

        return "<a href='javascript:void(0);' class='grid-lift-row' title='解封{$this->typeMap[$this->type]}' data-id='{$this->id}'><i class='fa fa-street-view'></i></a>";
    }

    public function __toString()
    {
        return $this->render();
    }
}