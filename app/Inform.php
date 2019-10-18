<?php
/**
 * Created by PhpStorm.
 * User: uriel
 * Date: 2019/4/27 0027
 * Time: 15:26
 */

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * App\Inform
 *
 * @property-read \App\User $to
 * @mixin \Eloquent
 * @property-read \App\User $disposer
 */
class Inform extends Model
{
    protected $table = 'inform';
    protected $primaryKey = 'id';
    protected $fillable = array('code', 'uf_id', 'ut_id', 'relevance_id', 'type', 'content', 'is_dispose', 'disposer_id', 'status');

    // 状态
    const STATUS_UNREAD = 0;    // 未读
    const STATUS_READ = 1;      //  已读
    const STATUS_CONFIRMED = 2; // 已确认
    const STATUS_DENIED = 3;    //  已拒绝
    const STATUS_DELETED = 4;   //  已删除

    // 是否需要处理
    const IS_DISPOSE_NO = 0;
    const IS_DISPOSE_YES = 1;

    public static $type_info = array(
        'wait_pass' => array('name' => '待处理', 'range' => array(0, 199)),
        'notify' => array('name' => '通知', 'range' => array(200, 299)),
        'reply' => array('name' => '回复', 'range' => array(300, 399)),
        'like' => array('name' => '赞', 'range' => array(400, 499)),
        'follow' => array('name' => '关注', 'range' => array(500, 599)),
    );

    // 通知类型
    /*
     * $request->merge($mid_params)
     * $request->attributes->add($mid_params);
     * */
    private $types = array(
        100 => array(
            'explain' => '添加友情小组',
            'template' => "<a href='/group/{gf_id}'>{gf_name}</a> 小组请求和您创建的 <a href='/group/{gt_id}'>{gt_name}</a> 小组成为友情小组。",
            'is_dispose' => self::IS_DISPOSE_YES,
            'relevance_table' => GroupFriendship::class,
            'dispose_class' => 'App\Http\Controllers\GroupFriendshipController',
            'pass_method' => 'pass',
            'refuse_method' => 'refuse',
            'request_parameters' => array(
                'fid' => 'f_id'
            )
        ),
        101 => array(
            'explain' => '申请加入小组',
            'template' => "用户 <a href='/member?uid={u_id}'>{u_name}</a> 申请加入您管理的小组 <a href='/group/{g_id}'>{g_name}</a> 。",
            'is_dispose' => self::IS_DISPOSE_YES,
            'relevance_table' => GroupMember::class,
            'dispose_class' => 'App\Http\Controllers\GroupMemberController',
            'pass_method' => 'passApply',
            'refuse_method' => 'refuseApply',
            'request_parameters' => array(
                'uid' => 'u_id',
                'gid' => 'g_id'
            )
        ),
        200 => array(
            'explain' => '友情小组处理反馈',
            'template' => "<a href='/group/{gf_id}'>{gf_name}</a> 小组{opt}了您创建的 <a href='/group/{gt_id}'>{gt_name}</a> 小组的友情小组请求。",
            'is_dispose' => self::IS_DISPOSE_NO
        ),
        201 => array(
            'explain' => '友情小组删除',
            'template' => "<a href='/group/{gf_id}'>{gf_name}</a> 小组解除了与您创建的 <a href='/group/{gt_id}'>{gt_name}</a> 小组的友情小组关系。",
            'is_dispose' => self::IS_DISPOSE_NO
        ),
        202 => array(
            'explain' => '入群申请处理反馈',
            'template' => "<a href='/group/{g_id}'>{g_name}</a> 小组管理员{opt}了您的入群申请。",
            'is_dispose' => self::IS_DISPOSE_NO
        ),
        203 => array(
            'explain' => '小组管理员升降提醒',
            'template' => "<a href='/member?uid={u_id}'>{u_name}</a> 将您{opt}为 <a href='/group/{g_id}'>{g_name}</a> 小组{role}。",
            'is_dispose' => self::IS_DISPOSE_NO
        ),
        204 => array(
            'explain' => '小组踢人',
            'template' => "<a href='/group/{g_id}'>{g_name}</a> 小组管理员将您移出了小组。",
            'is_dispose' => self::IS_DISPOSE_NO
        ),
        205 => array(
            'explain' => '主题置顶',
            'template' => "<a href='/group/{g_id}'>{g_name}</a> 小组管理员将您发表的讨论“<a href='/topic/{t_id}'>{title}</a>”{opt}置顶。",
            'is_dispose' => self::IS_DISPOSE_NO
        ),
        206 => array(
            'explain' => '主题是否允许评论',
            'template' => "<a href='/group/{g_id}'>{g_name}</a> 小组管理员将您发表的讨论“<a href='/topic/{t_id}'>{title}</a>”设置为{opt}评论。",
            'is_dispose' => self::IS_DISPOSE_NO
        ),
        207 => array(
            'explain' => '主题封禁',
            'template' => "<a href='/group/{g_id}'>{g_name}</a> 小组管理员将您发表的讨论“<a href='/topic/{t_id}'>{title}</a>”移{opt}回收站。",
            'is_dispose' => self::IS_DISPOSE_NO
        ),
        208 => array(
            'explain' => '主题删除',
            'template' => "<a href='/group/{g_id}'>{g_name}</a> 小组管理员删除了您发表的讨论“<a href='/topic/{t_id}'>{title}</a>”。",
            'is_dispose' => self::IS_DISPOSE_NO
        ),
        209 => array(
            'explain' => '评论删除',
            'template' => "管理员删除了您在讨论“<a href='/topic/{t_id}'>{title}</a>”回复的内容：{content}。",
            'is_dispose' => self::IS_DISPOSE_NO
        ),
        210 => array(
            'explain' => '转让小组',
            'template' => "您被任命为 <a href='/group/{g_id}'>{g_name}</a> 小组组长。",
            'is_dispose' => self::IS_DISPOSE_NO
        ),
        211 => array(
            'explain' => '小组申请结果',
            'template' => "您申请的 <a href='/group/{g_id}'>{g_name}</a> 小组{res}。",
            'is_dispose' => self::IS_DISPOSE_NO
        ),
        300 => array(
            'explain' => '回复主题',
            'template' => "用户 <a href='/member?uid={u_id}'>{u_name}</a> 回复了您的发表的讨论“<a href='/topic/{t_id}'>{title}</a>”。",
            'is_dispose' => self::IS_DISPOSE_NO
        ),
        301 => array(
            'explain' => '回复评论',
            'template' => "用户 <a href='/member?uid={u_id}'>{u_name}</a> 回复了您在讨论“<a href='/topic/{t_id}'>{title}</a>”中的评论。",
            'is_dispose' => self::IS_DISPOSE_NO
        ),
        400 => array(
            'explain' => '点赞主题',
            'template' => "用户 <a href='/member?uid={u_id}'>{u_name}</a> 点赞了您的发表的讨论“<a href='/topic/{t_id}'>{title}</a>”。",
            'is_dispose' => self::IS_DISPOSE_NO
        ),
        401 => array(
            'explain' => '点赞评论',
            'template' => "用户 <a href='/member?uid={u_id}'>{u_name}</a> 点赞了您在讨论“<a href='/topic/{t_id}'>{title}</a>”中的评论。",
            'is_dispose' => self::IS_DISPOSE_NO
        ),
        500 => array(
            'explain' => '关注',
            'template' => "用户 <a href='/member?uid={u_id}'>{u_name}</a> 关注了您。",
            'is_dispose' => self::IS_DISPOSE_NO
        ),
    );

    public function to()
    {
        return $this->belongsTo('App\User', 'ut_id');
    }

    public function disposer()
    {
        return $this->belongsTo('App\User', 'disposer_id');
    }

    // 拼接通知语句
    private function jointInform($template, $parameter = array())
    {
        return preg_replace_callback('/\{(.+?)\}/', function ($matches) use ($parameter) {
            return key_exists($matches[1], $parameter) ? $parameter[$matches[1]] : '';
        }, $template);
    }

    // 获得唯一标识码
    private function getCode()
    {
        return uniqid(str_random(3));
    }

    // 设置发送人
    public function setFrom($id)
    {
        $this->uf_id = $id;
        return $this;
    }

    // 设置收件人
    public function setTo($id)
    {
        $this->ut_id = $id;
        return $this;
    }

    // 发送通知
    public function sendInform(Model $model, $type_id, $parameter = array(), $code = '')
    {
        if (key_exists($type_id, $this->types)) {
            $type = $this->types[$type_id];
            $this->uf_id = session('uid');
            $this->relevance_id = $model->id;
            $this->code = $code ? $code : $this->getCode();
            $this->type = $type_id;
            $this->content = $this->jointInform($type['template'], $parameter);
            $this->is_dispose = $type['is_dispose'];
            $this->status = self::STATUS_UNREAD;
            return $this;
        } else {
            throw new \Exception('通知配置错误');
        }
    }

    // 利用反射机制调用其他控制器中的处理函数
    private function callMethod($object, $fun, $parameters)
    {
        // 初始化反射类
        $object = new \ReflectionClass($object);
        // 实例化对象
        $controller = $object->newInstance();
        // 获得处理函数
        $fun = $object->getMethod($fun);
        // 调用处理函数
        return $fun->invoke($controller, $parameters);
    }

    public function dispose(Request $request, $res = false)
    {
        $type = $this->types[$this->type];
        $model = $type['relevance_table']::find($this->relevance_id);
        $parameters = array();
        foreach ($type['request_parameters'] as $k => $v) {
            $parameters[$k] = $model->getAttribute($v);
        }
        $request->merge($parameters);
        if ($res) {
            return $this->callMethod($type['dispose_class'], $type['pass_method'], $request);
        } else {
            return $this->callMethod($type['dispose_class'], $type['refuse_method'], $request);
        }
    }
}