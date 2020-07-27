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

use think\Controller;

use app\admin\model\Title;
use think\Request;
use app\admin\controller\Authentication;
class CheckMax extends Controller{

    protected $beforeActionList = [
        'checkLogin',
    ];
    protected function checkLogin()
    {
        $auth = new Authentication();
        $auth->checkLogin();
    }

    public function index(){
        $titleModel = new Title();
        $this->assign('needCheckNum',$titleModel->getNeedCheckNum());
        return $this->fetch('index');
    }

    /**
     * 添加待核查任务
     * @param Request $request
     */
    public function addNeedCheck(Request $request){
        $startTime = $request->param('start');
        $endTime = $request->param('end');
        if (isGroup($startTime) == false or isGroup($endTime) == false){
            $this->error('提交信息有误请重新检查后提交');
        }
        $titleModel = new Title();
        $res = $titleModel->addCheck($startTime,$endTime);
        $this->success('任务提交成功，共计'.$res.'条待核查数据');
    }


    public function deleteAllNeedCheck(){
        $title = new Title();
        $res = $title->deleteAllNeedCheck();
        $this->success('待核查队列清除完成，共计取消'.$res.'条任务');
    }

    public function addAllNeedCheck(){
        $title = new Title();
        $res = $title->addAllNeedCheck();
        $this->success('待核查队列添加完成，共计添加'.$res.'条任务');
    }


}