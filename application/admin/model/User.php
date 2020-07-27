<?php
namespace app\admin\model;
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | 微博热搜博物馆 
// +----------------------------------------------------------------------
// | Author: Meloncn (interestingcn01@gmail.com)
// +----------------------------------------------------------------------
// | Github: https://github.com/meloncn/weiboresoubowuguan
// +----------------------------------------------------------------------
use think\Model;
class User extends Model{

    public function getStatusAttr($value)
    {
        $status = [1=>'正常',0=>'停用'];
        return $status[$value];
    }

    public function getUserList($num){
        return $this->paginate($num, false, ['query' => request()->param()]);
    }

    public function getUserInfoByUserId($id){
        return $this->where('id','=',$id)->find();
    }

    public function getUserInfoByOpenId($id){
        return $this->where('openid','=',$id)->find();
    }

    public function initUsed($userid){
        $all = $this->where('id','=',$userid)->value('all');
        return $this->save(['lave'  => $all,],['id' => $userid]);
    }

    public function setInitAll($userid,$num){
        return $this->save(['all'  => $num,],['id' => $userid]);
    }

    public function resetUsedAll(){
        return $this->query('update user set lave=CONCAT(`all`)');
    }

    public function setUserStatus($userid){
        $status = $this->where('id','=',$userid)->value('status');
        if ($status == 1){
            return $this->save(['status'  => '0',],['id' => $userid]);
        }else{
            return $this->save(['status'  => 1,],['id' => $userid]);
        }
    }

    public function down($userid){
        return $this->save(['session'  => null,],['id' => $userid]);
    }

    public function addUserBan($userid,$time){
        return $this->save(['ban_expire'  => time()+$time,],['id' => $userid]);
    }
}