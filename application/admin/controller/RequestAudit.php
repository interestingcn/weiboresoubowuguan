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

use app\common\model\BanIpModel;
use think\Controller;
use app\common\model\AccessLogModel;
use think\Request;
use ip\ipLocation;
use app\admin\controller\Authentication;
class RequestAudit extends Controller{

    protected $beforeActionList = [
        'checkLogin',
    ];
    protected function checkLogin()
    {
        $auth = new Authentication();
        $auth->checkLogin();
    }


    /**
     * 请求审计页面
     * @return mixed
     */
    public function index(){
        $accessModel = new AccessLogModel();
        $requestInfo = $accessModel->getRequests('200');
        $pages = $requestInfo->render();
        $this->assign('pages',$pages);
        $this->assign('requestInfo',$requestInfo);
        return $this->fetch('index');
    }


    /**
     *  某一条请求信息记录 通过请求ID
     * @param Request $request
     * @return mixed
     */
    public function info(Request $request){
        if (is_null($request->param('id'))){
            $this->error('未指定ID');
        }
        $id = $request->param('id');
        $accessModel = new AccessLogModel();
        $info = $accessModel->getRequestInfoById($id);

        $ip = new ipLocation();
        $location = $ip->getlocation(long2ip($info['ip']));

        $ban = new BanIpModel();

        if ($ban->checkIpBlackList($info['ip'],true)){
            $IpInBlackList = '已阻断连接';
        }else{
            $IpInBlackList = '放行';
        }
        $this->assign('ipStatus',$IpInBlackList);
        $this->assign('iplocation',$location);
        $this->assign('info',$info);
        return $this->fetch('info');
    }


    /**
     *  IP详情记录
     */
    public function ipInfo(Request $request){
        if (is_null($request->param('longIP'))){
            $this->error('未指定需查询IP');
        }
        $ip = $request->param('longIP');

        $iplocation = new ipLocation();
        $location = $iplocation->getlocation(long2ip($ip));

        $accessModel = new AccessLogModel();
        $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));

        $ban = new BanIpModel();
        if ($ban->checkIpBlackList($ip,true)){
            $ipStatus = '已阻断连接';
        }else{
            $ipStatus = '放行';
        }

        $this->assign('ipStatus',$ipStatus);
        $this->assign('today_num',$accessModel->getRequestCountByIp($ip,'',$beginToday));
        $this->assign('all_num',$accessModel->getRequestCountByIp($ip,'',1));
        $this->assign('iplocation',$location);
        $this->assign('ipaddr',$ip);

        return $this->fetch('ipInfo');
    }

    /**
     * 追踪IP访问记录
     */
    public function IpTrace(Request $request){
        if (is_null($request->param('longIP'))){
            $this->error('未指定需跟踪IP');
        }
        $ip = $request->param('longIP');
        $accessModel = new AccessLogModel();
        $traceInfo = $accessModel->getIpTrace($ip,100);
        $this->assign('traceInfo',$traceInfo);
        $this->assign('pages',$traceInfo->render());
        return $this->fetch('IpTrace');
    }

    /**
     * 追踪账户访问记录
     */
    public function IdTrace(Request $request){
        if (is_null($request->param('userid'))){
            $this->error('未指定需跟踪用户ID');
        }
        $id = $request->param('userid');
        $accessModel = new AccessLogModel();
        $traceInfo = $accessModel->getUserIdTrace($id,100);
        $this->assign('traceInfo',$traceInfo);
        $this->assign('pages',$traceInfo->render());
        return $this->fetch('IdTrace');
    }

}