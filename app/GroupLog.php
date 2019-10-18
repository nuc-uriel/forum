<?php


namespace App;


use Illuminate\Database\Eloquent\Model;

/**
 * App\GroupLog
 *
 * @property-read \App\User $creator
 * @property-read \App\Group $group
 * @mixin \Eloquent
 */
class GroupLog extends Model
{
    const STATUS_NORMAL = 0;
    const STATUS_DELETED = 1;

    const TYPE_GROUP_MANAGE = 1;
    const TYPE_MEMBER_MANAGE = 2;
    const TYPE_TOPIC_MANAGE = 3;

    protected $table = 'group_log';
    protected $primaryKey = 'id';
    protected $fillable = array('g_id', 'u_id', 'type', 'content', 'status');

    public function group()
    {
        return $this->belongsTo('App\Group', 'g_id');
    }

    public function creator()
    {
        return $this->belongsTo('App\User', 'u_id');
    }

    private $types = array(
        1 => array(
            101 => array(
                'explain' => '基本信息设置',
                'template' => "将 {setting} 修改为 {data} 。 "
            ),
            102 => array(
                'explain' => '图标修改',
                'template' => "修改了小组图标。 "
            ),
            103 => array(
                'explain' => '标签修改',
                'template' => "{opt} 了小组标签 {label} 。 "
            ),
            104 => array(
                'explain' => '违禁词设置',
                'template' => "{opt} 了违禁词 {word} 。"
            ),
        ),
        2 => array(
            201 =>  array(
                'explain' => '成员设置',
                'template' => "将用户 <a href='/member?uid={uid}'>{username}</a> {opt} 。"
            ),
            202 =>  array(
                'explain' => '处理申请',
                'template' => "{opt} 了用户 <a href='/member?uid={uid}'>{username}</a> 的入组申请。"
            ),
            203 =>  array(
                'explain' => '踢出成员',
                'template' => "踢出了用户 <a href='/member?uid={uid}'>{username}</a> 。"
            ),
            204 =>  array(
                'explain' => '转让小组',
                'template' => "将小组转让给管理员 <a href='/member?uid={uid}'>{username}</a>。"
            ),
        ),
        3 => array(
            301 =>  array(
                'explain' => '置顶主题',
                'template' => "将小组讨论“<a href='topic/{tid}'>{title}</a>”{opt}置顶，作者 <a href='/member?uid={uid}'>{username}</a> 。"
            ),
            302 =>  array(
                'explain' => '禁止回复主题',
                'template' => "将小组讨论“<a href='topic/{tid}'>{title}</a>”设置为{opt}回应，作者 <a href='/member?uid={uid}'>{username}</a> 。"
            ),
            303 =>  array(
                'explain' => '封禁帖子',
                'template' => "将小组讨论“<a href='topic/{tid}'>{title}</a>”移{opt}了回收站，作者 <a href='/member?uid={uid}'>{username}</a> 。"
            ),
            304 =>  array(
                'explain' => '删除帖子',
                'template' => "删除了小组讨论“<a href='topic/{tid}'>{title}</a>”，作者 <a href='/member?uid={uid}'>{username}</a> 。"
            ),
        )
    );

    // 拼接通知语句
    private function jointLog($template, $parameter = array())
    {
        return preg_replace_callback('/\{(.+?)\}/', function ($matches) use ($parameter) {
            return key_exists($matches[1], $parameter) ? $parameter[$matches[1]] : '';
        }, $template);
    }

    //  添加日志
    public function addLog($type, $min_type, $parameter=array()){
        $this->u_id = session('uid');
        $this->type = $type;
        $this->content = $this->jointLog($this->types[$type][$min_type]['template'], $parameter);
        return $this;
    }
}