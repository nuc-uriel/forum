<?php
/**
 * Created by PhpStorm.
 * User: uriel
 * Date: 2019/4/27 0027
 * Time: 23:12
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\GroupMember
 *
 * @mixin \Eloquent
 */
class GroupMember extends Model
{
    const STATUS_NORMAL = 0;
    const STATUS_WAIT_CONFIRM = 1;
    const STATUS_NOT_PASS = 2;
    const STATUS_BLACKLIST = 3;
    const STATUS_DELETED = 4;

    const ROLE_MEMBER = 0;
    const ROLE_ADMIN = 1;
    const ROLE_LEADER = 2;

    const MAX_ADMIN_COUNT = 4;

    protected $table = 'group_member';
    protected $primaryKey = 'id';
    protected $fillable = array('u_id', 'g_id', 'role', 'status');
}
