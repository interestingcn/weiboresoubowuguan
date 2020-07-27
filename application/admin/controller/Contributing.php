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

use app\admin\model\Contributing as ContributingModel;
use think\Request;

class Contributing extends Controller{
    public function index(){
        $contr = new ContributingModel();
        $info = $contr->getInfo();
        $this->assign('Info',$info);
        $this->assign('pages',$info->render());
        return $this->fetch('index');
    }
    
    public function add(Request $request){
        if ($request->param('name') == null){
            return $this->fetch('add');
        }else{
            $con = new ContributingModel();
            if ($con->add($request->param('name'),$request->param('price')) > 0){
                $this->success('添加成功！');
            }else{
                $this->error('添加失败！');
            }
        }
    }

    public function del(Request $request){
        $id = $request->param('id');
        if ($id == null){
            $this->error('未指定ID');
        }
        $con = new ContributingModel();
        if ($con->deleteInfoById($id) > 0){
            $this->success('删除成功！');
        }else{
            $this->error('删除失败！');
        }
    }

}