<?php
/**
 * Created by PhpStorm.
 * User: uriel
 * Date: 2019/5/12 0012 0024
 * Time: 21:33
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    const STATUS_NORMAL = 0;
    const STATUS_DELETED = 1;

    const TYPE_USER = 0;
    const TYPE_GROUP = 1;
    const TYPE_TOPIC = 2;
    const TYPE_COMMENT = 3;

    protected $table = 'report';
    protected $primaryKey = 'id';
    protected $fillable = array('u_id', 'target_id', 'type', 'content', 'status');

    //  创建者
    public function creator()
    {
        return $this->belongsTo('App\User', 'u_id');
    }

}