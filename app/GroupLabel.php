<?php
/**
 * Created by PhpStorm.
 * User: uriel
 * Date: 2019/4/24 0024
 * Time: 15:35
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

/**
 * App\GroupLabel
 *
 * @property-read \App\Group $group
 * @mixin \Eloquent
 */
class GroupLabel extends Model
{
    const STATUS_NORMAL = 0;
    const STATUS_WAIT_CONFIRM = 1;
    const STATUS_DELETED = 2;
    const MAX_LENGTH = 6;

    protected $table = 'group_label';
    protected $primaryKey = 'id';
    protected $fillable = array('g_id', 'name', 'status');

    public function group(){
        return $this->belongsTo('App\Group', 'g_id');
    }
}