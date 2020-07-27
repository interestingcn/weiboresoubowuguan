<?php
namespace app\index\model;
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

    public function getUserId($openid){
        return $this->where('openid','=',$openid)->value('id');
    }

    public function addUser($openid){
        $this->openid = $openid;
        $this->lave = 1000;
        $this->all = 1000;
        $this->session = 0;
        $this->status = 1;
        $this->save();
        return $this->id;
    }

    public function updateSessionId($userid,$sessionid){
        return $this->save(['session'  => $sessionid,],['id' => $userid]);
    }

    public function getUserStatus($userid){
        return $this->where('id','=',$userid)->value('status');
    }


    public function isBan($userid){
       $expire = $this->where('id','=',$userid)->value('ban_expire');
       if ($expire == null){$expire = 0;}
       if (time() < $expire){
           return true;
       }else{
           return false;
       }
    }

    /**
     * @param $userid
     * @return mixed
     * 获取限制登录日期
     */
    public function getBanexpire($userid){
        return $this->where('id','=',$userid)->value('ban_expire');
    }

    /**
     * @param $userid
     * @return mixed
     * 获取该账户系统认证Session信息
     */
    public function getSessionId($userid){
        return $this->where('id','=',$userid)->value('session');
    }

    /**
     * @param $userId
     * @return array|false|\PDOStatement|string|Model
     * 通过ID获取用户账户信息
     */
    public function getUserInfo($userId){
        return $this->where('id','=',$userId)->find();
    }

    /**
     * 更新最后使用时间
     */
    public function updateLastUse($userid){
        return $this->save(['last_use'  => time(),],['id' => $userid]);
    }

    /**
     * 检查可使用值是否大于0
     */
    public function checkLave($userid){
        $lave = $this->where('id','=',$userid)->value('lave');
        if ($lave > 0){
            return true;}else{return false;}
    }

    /**
     * 数值自减
     */
    public function decrease($userid){
        return $this->where('id','=',$userid)->setDec('lave',1);
    }

}