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
use think\Controller;

use app\common\model\AccessLogModel;
use think\Request;
use app\admin\controller\Authentication;
class Clean extends Controller{

    protected $beforeActionList = [
        'checkLogin',
    ];
    protected function checkLogin()
    {
        $auth = new Authentication();
        $auth->checkLogin();
    }

    public function cleanRequest(Request $request){
        $day = $request->post('day');
        if ($day == null){
            return $this->fetch('cleanRequest');
        }
        $accessModel = new AccessLogModel();
        $num = $accessModel->deleteNDaysAgoRequestLog($day);
        $this->success('执行成功，共删除'.$num.'条记录');
    }

    public function cleanExpireBan(){
        $ban = new BanIpModel();
        $num = $ban->clean();
        $this->success('执行成功，共清除'.$num.'条过期记录');
    }

    public function cleanFarmeworkCache(){
        if ($this->delete_dir_file(CACHE_PATH) || $this->delete_dir_file(TEMP_PATH)) {
            $this->success('清除缓存成功');
        } else {
            $this->error('清除缓存失败');
        }
    }

    /**
     * 循环删除目录和文件
     * @param string $dir_name
     * @return bool
     */
    private function delete_dir_file($dir_name) {
        $result = false;
        if(is_dir($dir_name)){
            if ($handle = opendir($dir_name)) {
                while (false !== ($item = readdir($handle))) {
                    if ($item != '.' && $item != '..') {
                        if (is_dir($dir_name . DS . $item)) {
                            delete_dir_file($dir_name . DS . $item);
                        } else {
                            unlink($dir_name . DS . $item);
                        }
                    }
                }
                closedir($handle);
                if (rmdir($dir_name)) {
                    $result = true;
                }
            }
        }
        return $result;
    }
}