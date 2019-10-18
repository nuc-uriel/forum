<?php
/**
 * Created by PhpStorm.
 * User: uriel
 * Date: 2019/4/27 0027
 * Time: 17:28
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

/**
 * App\GroupFriendship
 *
 * @mixin \Eloquent
 * @property-read \App\Group $friend
 * @property-read \App\Group $owner
 */
class GroupFriendship extends Model
{
    const STATUS_NORMAL = 0;
    const STATUS_WAIT_CONFIRM = 1;
    const STATUS_DELETED = 2;

    protected $table = 'group_friendship';
    protected $primaryKey = 'id';
    protected $fillable = array('go_id', 'gf_id', 'ut_id', 'status');

    public function owner()
    {
        return $this->belongsTo('App\Group', 'go_id');
    }

    public function friend()
    {
        return $this->belongsTo('App\Group', 'gf_id');
    }
}