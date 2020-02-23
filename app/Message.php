<?php


namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    // 状态
    const STATUS_UNREAD = 0;    // 接收人未读
    const STATUS_READ = 1;      // 接收人已读
    const STATUS_SENDER_DELETED = 2;  // 接收方已删除
    const STATUS_RECEIVER_DELETED = 3; // 发送方已删除
    const STATUS_ALL_DELETED = 4;   // 双方都删除

    protected $table = 'message';
    protected $primaryKey = 'id';
    protected $fillable = array('uf_id', 'ut_id', 'content', 'group_code', 'status');

    // 发送者
    public function sender()
    {
        return $this->belongsTo('App\User', 'uf_id');
    }

    // 接受者
    public function receiver()
    {
        return $this->belongsTo('App\User', 'ut_id');
    }

    public static function getGroupCode($uf_id, $ut_id)
    {
        return min($uf_id, $ut_id) . '@' . max($uf_id, $ut_id);
    }
}
