<?php
/**
 * Created by PhpStorm.
 * User: uriel
 * Date: 2019/5/5 0005 0024
 * Time: 16:32
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

/**
 * App\Collect
 *
 * @mixin \Eloquent
 */
class Collect extends Model
{
    const STATUS_NORMAL = 0;
    const STATUS_DELETED = 1;

    protected $table = 'collect';
    protected $primaryKey = 'id';
    protected $fillable = array('u_id', 't_id', 'status');

    public function topic()
    {
        return $this->belongsTo('App\Topic', 't_id');
    }

    public function creator()
    {
        return $this->belongsTo('App\User', 'u_id');
    }
}