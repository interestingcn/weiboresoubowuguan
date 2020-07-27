<?php
namespace app\index\controller;

// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | 微博热搜博物馆 
// +----------------------------------------------------------------------
// | Author: Meloncn (interestingcn01@gmail.com)
// +----------------------------------------------------------------------
// | Github: https://github.com/meloncn/weiboresoubowuguan
// +----------------------------------------------------------------------

use think\Controller;
use think\Request;
use think\Config;
use think\Session;
use think\Cookie;

use app\index\Model\User as UserModel;
class User extends Controller{

    /**
     * 用户首页
     */
    public function index(){
        $this->checkLogin();

        $userModel = new UserModel();
        $userInfo = $userModel->getUserInfo(Session::get('userid'));
        $this->assign('openid',$userInfo['openid']);
        $this->assign('lave',$userInfo['lave']);
        $this->assign('all',$userInfo['all']);
        $this->assign('session',$userInfo['session']);
        $this->assign('title','用户中心');
        return $this->fetch('home');
    }

    /**
     *  登录页面
     */
    public function login(Request $request){
        if($request->get('type') == 'qq'){
            $this->redirect('https://api.uomg.com/api/login.qq?method=login&callback='.Config::get('callback'),302);
        }
        if (Session::has('openid')){
            $this->redirect('index/user/index');
        }
        $this->assign('title','登录');
        return $this->fetch('login');
    }

    /**
     * 登录回调验证以及登录认证
     */
    public function callback(Request $request){
        if ($request->post() == null){
            $this->error('登录信息获取失败');
        }
        $post = array(
         'openid'        =>    $request->post('openid')?$request->post('openid'):$this->error('OpenID回调失败')
        ,'name'        =>    $request->post('name')?$request->post('name'):$this->error('用户名回调失败')
        ,'avatar'        =>    $request->post('avatar')
        ,'md5'        =>    $request->post('md5')?$request->post('md5'):$this->error('签名回调失败')
        ,'callback'    =>    Config::get('callback')
        );
        $rel = $this->http_curl('https://api.uomg.com/api/login.qq?method=check',$post);
        $arr = json_decode($rel,true);
        if ($arr['code'] == 1) {
//            登录成功
           $userModel = new UserModel();
           $userId = $userModel->getUserId($post['openid']);

//            首次登陆时创建ID
           if ($userId == null){
               $userId = $userModel->addUser($post['openid']);
           }

//           判断账户使用状态
           if ($userModel->getUserStatus($userId) !== 1){
               $this->error('当前账户已被禁用！<br><small><small><small>ID:'.$post['openid'].'</small></small></small>','index/index/index','','300');
           }

//           检查是否在限制时间内
           if ($userModel->isBan($userId)){
               $user = new UserModel();
               $this->error(date('Y-m-d H:i:s',$user->getBanexpire($userId)).'此时间之前限制登录','index/index/index','','30');
           }
//           登录授权添加认证部分部分
            Session::set('openid',$post['openid']);
            Session::set('name',$post['name']);
            Session::set('userid',$userId);
//            ip与session配合生成token 用于验证账户登录
            if ($_SERVER['HTTP_USER_AGENT'] == null){$ua = 'ua';}else{$ua = $_SERVER['HTTP_USER_AGENT'];}
            Cookie::set('sign',createTk($request->ip().$ua.session_id()),36000);

//            更新session ID
            $userModel->updateSessionId($userId,session_id());
            $this->redirect('index/user/index');
        }else{
            $this->error('登录失败,请重新尝试授权!','index/user/login','','6');
        }
    }

    /**
     * 检查登录状态是否合法
     */
    public function checkLogin(){
//        初次判断是否存在session
        if (Session::has('openid') == false or Session::has('name') == false or Session::has('userid')== false){
            $this->error('请先登录！','index/user/login');
        }
        $request = Request::instance();
        if ($_SERVER['HTTP_USER_AGENT'] == null){$ua = 'ua';}else{$ua = $_SERVER['HTTP_USER_AGENT'];}
        if (checkTk($request->ip().$ua.session_id(),Cookie::get('sign')) == false){
            Session::clear();
            session_destroy();
            Cookie::delete('sign');
            $this->error('登录状态失效','index/user/login');
        }
        $userModel = new UserModel();
        if ($userModel->getSessionId(Session::get('userid')) !== session_id()){
            Session::clear();
            session_destroy();
            Cookie::delete('sign');
            $this->error('登录状态失效','index/user/login');
        }
        $userModel->updateLastUse(Session::get('userid'));//更新最后使用时间
    }

    /**
     * 检查剩余使用次数
     */
    public function checkLave(){
        $user = new UserModel();
        if ($user->checkLave(Session::get('userid')) == false ){
            $this->error('使用量已达上限！','index/index/index');
        }
    }

    /**
     * 每次调用，剩余量减一
     */
    public function decrease(){
        $user = new UserModel();
        $user->decrease(Session::get('userid'));
    }

    /**
     * 退出登录
     */
    public function logout(){
        Session::clear();
        session_destroy();
        Cookie::delete('sign');
        $this->success('当前账户已安全退出','index/index/index');
    }



    /**
     * 内部工具
     */
    private function http_curl($url,$post=0,$header=0,$cookie=0,$referer=0,$ua=0,$nobaody=0){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $httpheader[] = "Accept:*/*";
        $httpheader[] = "Accept-Encoding:gzip,deflate,sdch";
        $httpheader[] = "Accept-Language:zh-CN,zh;q=0.8";
        $httpheader[] = "Connection:close";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
        if($post){
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }
        if($header){
            curl_setopt($ch, CURLOPT_HEADER, TRUE);
        }
        if($cookie){
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        }
        if($referer){
            curl_setopt($ch, CURLOPT_REFERER, $referer);
        }
        if($ua){
            curl_setopt($ch, CURLOPT_USERAGENT,$ua);
        }else{
            curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (iPhone; CPU iPhone OS 10_2_1 like Mac OS X) AppleWebKit/602.4.6 (KHTML, like Gecko) Mobile/14D27 MicroMessenger/6.5.5 NetType/WIFI Language/zh_CN');
        }
        if($nobaody){
            curl_setopt($ch, CURLOPT_NOBODY,1);
        }
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        $ret = curl_exec($ch);
        curl_close($ch);
        return $ret;
    }
}