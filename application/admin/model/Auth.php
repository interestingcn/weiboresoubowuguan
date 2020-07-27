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

class Auth extends Model{
    /**
     *  判断用户输入凭据是否有效
     */
    public function checkUser($username,$pwd,$secure_code){

        return $this->where('username','=',$username)->where('pwd','=',salt($pwd))->where('sec_code','=',$secure_code)->value('id');
    }

    /**
     * 管理员登陆记录session
     */
    public function updateSession($user,$session){
        $this->save([
            'session'  => $session,
        ],['username' => $user]);
    }

    /**
     *  检测Session是否有变动
     */
    public function checkRemotelogin($user_id,$session){
        return $this->where('id','=',$user_id)->where('session','=',$session)->value('id');
    }

}