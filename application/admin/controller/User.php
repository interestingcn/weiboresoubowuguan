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
use app\common\model\AccessLogModel;
use think\Controller;
use think\Request;
use app\admin\model\User as UserModel;

use app\admin\controller\Authentication;
class User extends Controller{

    protected $beforeActionList = [
        'checkLogin',
    ];
    protected function checkLogin()
    {
        $auth = new Authentication();
        $auth->checkLogin();
    }

    public function index(){
        $user = new UserModel();
        $userList = $user->getUserList(100);
        $this->assign('pages',$userList);
        $this->assign('userList',$userList);
        return $this->fetch('index');
    }


    public function info(Request $request){
        $id = $request->param('id');
        if ($id == null){
            $this->error('未指定ID！');
        }
        $user = new UserModel();
        if ($request->param('idType') == 'userid'){
            $userInfo = $user->getUserInfoByUserId($id);
        }else{
            $userInfo = $user->getUserInfoByOpenId($id);
        }
        if ($userInfo == null){
            $this->error('没有相关用户');
        }
        $accessModel = new AccessLogModel();
        $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
//        只有使用UserID索引用户时可显示来访次数
        if ($request->param('idType') == 'userid') {
            $this->assign('today_num', $accessModel->getRequestCountByUserId($id, '', $beginToday));
            $this->assign('all_num', $accessModel->getRequestCountByUserId($id, '', 1));
        }
        $this->assign('info',$userInfo);
        return $this->fetch('info');
    }

    public function searchUser(){
        return $this->fetch('searchUser');
    }

    /**
     * 初始化指定用户已使用量
     */
    public function initUsed(Request $request){
        $userid = $request->param('userid');
        if ($userid == null){
            $this->error('未指定ID！');
        }
        $user = new UserModel();
        if ($user->initUsed($userid) > 0){
            $this->success('使用量已重置！');
        }else{
            $this->error('使用量重置失败！');
        }
    }

    /**
     * 设置初始最大值
     */
    public function setInitAll(Request $request){
        $userid = $request->param('userid');
        if ($request->param('userid') == null){
            $this->error('未指定ID！');
        }
        if ($request->param('all') == null){
            return $this->fetch('setInitAll');
        }else{
            $all = $request->param('all');
            $user = new UserModel();
            if ($user->setInitAll($userid,$all) > 0){
             return $this->success('更新成功！');
            }else{
                return $this->error('更新失败');
            }
        }
    }

    /**
     * 重置全部用户使用量
     */
    public function resetUsedAll(){
        $user = new UserModel();
        if ($user->resetUsedAll() > 0){
            $this->success('全部重置完成');
        }else{
            $this->error('全部重置出现问题！');
        }
    }

    /**
     * @param Request $request
     * 改变账户状态 - 启用/停用
     */
    public function setUserStatus(Request $request){
        $userid = $request->param('userid');
        if ($userid == null){
            $this->error('未指定ID!');
        }
        $user = new UserModel();
        if ($user->setUserStatus($userid)>0){
            $this->success('账户状态已改变！');
        }else{
            $this->error('账户状态改变失败！');
        }
    }

    public function down(Request $request){
        $userid = $request->param('userid');
        if ($userid == null){
            $this->error('未指定ID!');
        }
        $user = new UserModel();
        if ($user->down($userid) > 0){
            $this->success('已远程注销登陆！');
        }else{
            $this->error('远程注销失败！');
        }
    }

    public function userBan(Request $request){
        $userid = $request->param('userid');
        $time = $request->param('time')*24*60*60;//此处传入以天为单位
        $user = new UserModel();
        if ($user->addUserBan($userid,$time) > 0){
            $this->success('添加成功！');
        }else{
            $this->error('添加失败！');
        }
    }

}