<?php
/**
 * Created by PhpStorm.
 * User: uriel
 * Date: 2019/5/5 0005 0024
 * Time: 16:32
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    const STATUS_NORMAL = 0;
    const STATUS_DELETED = 1;

    const TYPE_TOPIC = 0;
    const TYPE_COMMENT = 1;

    protected $table = 'like';
    protected $primaryKey = 'id';
    protected $fillable = array('u_id', 'target_id', 'type', 'status');

    public function comment()
    {
        return $this->belongsTo('App\Comment', 'target_id');
    }

    public function topic()
    {
        return $this->type == self::TYPE_TOPIC ? $this->belongsTo('App\Topic', 'target_id') : $this->comment->topic();
    }

    public function creator()
    {
        return $this->belongsTo('App\User', 'u_id');
    }
}