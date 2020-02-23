<?php
/**
 * Created by PhpStorm.
 * User: uriel
 * Date: 2019/4/24 0024
 * Time: 15:36
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\GroupType
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Group[] $groups
 * @mixin \Eloquent
 */
class GroupType extends Model
{
    const STATUS_NORMAL = 0;
    const STATUS_DELETED = 1;

    protected $table = 'group_type';
    protected $primaryKey = 'id';
    protected $fillable = array('name','status', 'introduce', 'status', 'created_at', 'updated_at');

    public function groups()
    {
        return $this->hasMany('App\Group', 'gt_id', 'id')->where('status', '!=', Group::STATUS_DELETED);
    }
}
