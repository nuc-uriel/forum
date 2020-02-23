<?php
/**
 * Created by PhpStorm.
 * User: uriel
 * Date: 2019/4/28 0028
 * Time: 0:10
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class MemberRelationship extends Model
{
    const STATUS_NORMAL = 0;
    const STATUS_DELETED = 1;

    const TYPE_FANS = 0;
    const TYPE_BLACKLIST = 1;

    protected $table = 'member_relationship';
    protected $primaryKey = 'id';
    protected $fillable = array('myself_id', 'other_id', 'type', 'status');
}
