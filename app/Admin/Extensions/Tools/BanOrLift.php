<?php
/**
 * Created by PhpStorm.
 * User: uriel
 * Date: 2019/5/10 0010 0024
 * Time: 22:23
 */

namespace App\Admin\Extensions\Tools;

use App\User;
use Encore\Admin\Grid\Tools\BatchAction;

class BanOrLift extends BatchAction
{
    protected $status;
    protected $type;

    public function __construct($type, $status = User::STATUS_NORMAL)
    {
        $this->status = $status;
        $this->type = $type;
    }

    public function script()
    {
        return <<<EOT

$('{$this->getElementClass()}').on('click', function() {

    $.ajax({
        method: 'post',
        url: '/admin/{$this->type}/ban',
        data: {
            _token:LA.token,
            ids: selectedRows(),
            status: '{$this->status}'
        },
        success: function () {
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

EOT;
    }
}
