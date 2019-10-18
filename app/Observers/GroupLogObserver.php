<?php


namespace App\Observers;

use App\GroupLog;

class GroupLogObserver
{
    public function creating(GroupLog $log)
    {
        $content = '';
        if($log->group->leader->find(session('uid'))){
            $content .= '组长';
        }elseif ($log->group->admin->find(session('uid'))){
            $content .= '管理员';
        }
        $content .= " <a href='/member?uid=". session('uid') ."'>". session('uname') . "</a> ";
        $log->content = $content . $log->content;
        return true;
    }

}