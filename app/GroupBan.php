<?php


namespace App;


use Illuminate\Database\Eloquent\Model;

/**
 * App\GroupBan
 *
 * @property-read \App\User $creator
 * @property-read \App\Group $group
 * @mixin \Eloquent
 */
class GroupBan extends Model
{

    const STATUS_NORMAL = 0;
    const STATUS_DELETED = 1;

    protected $table = 'group_ban';
    protected $primaryKey = 'id';
    protected $fillable = array('g_id', 'u_id', 'word', 'status');

    public function group()
    {
        return $this->belongsTo('App\Group', 'g_id');
    }

    public function creator()
    {
        return $this->belongsTo('App\User', 'u_id');
    }
}