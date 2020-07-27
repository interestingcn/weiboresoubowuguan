<?php
namespace app\admin\controller;
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | 微博热搜博物馆 
// +----------------------------------------------------------------------
// | Author: Meloncn (interestingcn01@gmail.com)
// +----------------------------------------------------------------------
// | Github: https://github.com/meloncn/weiboresoubowuguan
// +----------------------------------------------------------------------
// +----------------------------------------------------------------------
// | 用户登录登出及授权
// +----------------------------------------------------------------------
// | 2020年5月11日 18：18
// +----------------------------------------------------------------------
// | Author: Meloncn <1430567640@qq.com>
// +----------------------------------------------------------------------
use think\Controller;
use think\Request;
use app\admin\model\Auth;
use think\Session;
use think\Url;
use app\common\controller\Pusher;

class Authentication extends Controller{
    /**
     *  用户身份验证
     * @param Request $request
     * @return mixed
     */

    public function login(Request $request){
        
    if (empty($request->post('password')) or empty($request->post('username') or empty($request->post('sec_code')))) {
//      关闭全局Layout
        $this->view->engine->layout(false);
        return $this->fetch('login');
        }
        $username = $request->post('username');
        $password = $request->post('password');
        $sec_code = $request->post('sec_code');

        $auth = new Auth();
        $res = $auth->checkUser($username,$password,$sec_code);
        if ($res !== null){
            Session::set('username',$username);
            Session::set('user_id',$res);
//           每次执行登陆时更新数据库内Session
            $auth->updateSession($username,session_id());
            $pusher = new Pusher();
            $msg = "\n\r 账户名称：$username \n\r 登陆状态：成功登陆";
            $pusher->infoPusher('后台登陆提示', $msg,false);
            $this->success('登陆成功',\think\Url::build('admin/index/index'));
        }else{
            $this->error('认证失败');
        }
    }

    /**
     *  判断用户是否登陆以及异地登录
     */
    public function checkLogin(){
            if (Session::has('user_id') == false){
                $this->redirect(Url::build('admin/Authentication/login'),302);
            }
            $auth = new Auth();
            if ($auth->checkRemotelogin(Session::get('user_id'),session_id()) == null){
                Session::clear();
                $this->redirect(Url::build('admin/Authentication/login'),302);
            }
        }

    /**
     * 用户注销登陆状态
     */
        public function logout(){
            Session::clear();
            session_destroy();
            $this->success('您已成功注销登陆');
        }

}