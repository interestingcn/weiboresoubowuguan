<?php
namespace app\admin\Controller;
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
use ip\ipLocation;
use think\Controller;
use think\Request;
use app\admin\controller\Authentication;
class Ban extends Controller{

    protected $beforeActionList = [
        'checkLogin',
    ];
    protected function checkLogin()
    {
        $auth = new Authentication();
        $auth->checkLogin();
    }

    /**
     *  被阻断IP列表
     * @return mixed
     */
    public function index(){
        $banModel = new BanIpModel();
        $banInfo = $banModel->getBanInfo('50');
//        halt($banInfo);
        $this->assign('pages',$banInfo->render());
        $this->assign('BanInfo',$banInfo);
        return $this->fetch('index');
    }


    /**
     *  解除阻断
     * @param Request $request
     */
    public function unBan(Request $request){
        if (is_null($request->param('longIP'))){
            $this->error('未指定解除阻断IP');
        }
        $ip = $request->param('longIP');
        $banModel = new BanIpModel();
        $banModel->unBan($ip);
        $this->success(long2ip($ip).'已解除阻断');
    }

    /**
     *  添加到阻断列表
     * @param Request $request
     * @return mixed
     */
    public function add(Request $request){
        $ipaddr = $request->post('ip');
        if (is_null($ipaddr)){
            return $this->fetch('add');
        }
        $ip = explode(PHP_EOL, trim($ipaddr));
        $remarks = $request->post('remarks');
        $expire = $request->post('expire');
            if ($expire == -1){
                $expire = 0;
            }

        $banModel = new BanIpModel();
        foreach ($ip as $key => $value){
            if ($banModel->checkIpBlackList($value,false)){
                continue;
            }else{
                $banModel->addIp($value,$expire,$remarks,false,true);
            }
        }
        $this->success('添加成功');
    }

    public function listBan(Request $request){
        $banModel = new BanIpModel();
        $banlist = $banModel->getBanList('100');
        $ip = new ipLocation();
        $data = [];
        foreach ($banlist as $value => $key){
            $data[$value]['id'] = $key['id'];
            $data[$value]['ip'] = (int)$key['ip'];
            $data[$value]['location'] = $ip->getlocation(long2ip((int)$key['ip']));
            $data[$value]['add_time'] = $key['add_time'];
            $data[$value]['expire'] = $key['expire'];
            $data[$value]['remarks'] = $key['remarks'];
            $data[$value]['admin_add'] = $key['admin_add'];
        }


        $pages = $banlist->render();
        $this->assign('banList',$data);
        $this->assign('pages',$pages);
        return $this->fetch('list');
    }


}