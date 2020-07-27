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

use ip\ipLocation;
use think\Controller;
use app\admin\model\SpiderDateAdmin;
use app\common\model\AccessLogModel;
use app\admin\model\Time;
use app\admin\model\Title;
use app\admin\model\System;
use app\common\model\BanIpModel;
use app\admin\controller\Authentication;
class Index extends Controller{

    protected $beforeActionList = [
        'checkLogin',
    ];
    protected function checkLogin()
    {
        $auth = new Authentication();
        $auth->checkLogin();
    }


    public function index(){
        $spiderDateModel = new SpiderDateAdmin();
        $accessLogModel = new AccessLogModel();
        $timeModel = new Time();
        $titleModel = new Title();
        $banModel = new BanIpModel();

//        今日0时时间戳
        $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));

//        概览服务检测部分
        $system = new System();
        if ($system->lastOnline('service_updateTitle') > time()-60){$service_updateTitle = 'ON';}else{$service_updateTitle = 'OFF';}
        if ($system->lastOnline('service_firewall') > time()-60){$service_firewall = 'ON';}else{$service_firewall = 'OFF';}
        if ($system->lastOnline('service_checkMax') > time()-60){$service_checkMax = 'ON';}else{$service_checkMax = 'OFF';}

        $getLastBan = $banModel->getLastBanIp('10');
        $data_lastBan = [];
        $ip = new ipLocation();
        foreach ($getLastBan as $value => $key){
            $data_lastBan[$value]['id'] = $key['id'];
            $data_lastBan[$value]['ip'] = (int)$key['ip'];
            $data_lastBan[$value]['location'] = $ip->getlocation(long2ip((int)$key['ip']));
            $data_lastBan[$value]['add_time'] = $key['add_time'];
            $data_lastBan[$value]['expire'] = $key['expire'];
            $data_lastBan[$value]['remarks'] = $key['remarks'];
            $data_lastBan[$value]['admin_add'] = $key['admin_add'];
        }


        $this->assign('cache_num',$spiderDateModel->countOfAll());
        $this->assign('request_num',$accessLogModel->getRequestCount($beginToday,time()));
        $this->assign('new_time_num',$timeModel->getTimeCount(date('Ymd').'0000'));
        $this->assign('new_title_num',$titleModel->getTitleCount(date('Ymd').'0000',date('YmdHi')));
        $this->assign('need_check_num',$titleModel->getNeedCheckNum());
        $this->assign('now_in_ban',$banModel->nowInBanIpCount());
        $this->assign('all_title_num',$titleModel->getAllCount());
        $this->assign('last_time',$timeModel->getLastTime());
        $this->assign('service_updateTitle',$service_updateTitle);
        $this->assign('service_firewall',$service_firewall);
        $this->assign('service_checkMax',$service_checkMax);
        $this->assign('lastRequest',$accessLogModel->getRequest('15'));
        $this->assign('lastBanIp',$data_lastBan);
        return $this->fetch('index');
    }
}