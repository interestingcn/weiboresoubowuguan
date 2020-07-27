<?php
namespace app\common\controller;
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
use think\Request;
use think\Config;
use think\Session;

class Access{

    function __construct(){
            $this->addAccessLog();
    }
    /**
     * 获取客户端浏览器类型
     * @return string
     */
    private function GetBrowser() {
        $Browser = $_SERVER['HTTP_USER_AGENT'];
        if ($Browser == null){
            $Browser = 'Disable';
        }
        if (preg_match('/MSIE/i',$Browser)) {
            $Browser = 'MSIE';
        }
        elseif (preg_match('/Firefox/i',$Browser)) {
            $Browser = 'Firefox';
        }
        elseif (preg_match('/Chrome/i',$Browser)) {
            $Browser = 'Chrome';
        }
        elseif (preg_match('/Safari/i',$Browser)) {
            $Browser = 'Safari';
        }
        elseif (preg_match('/Opera/i',$Browser)) {
            $Browser = 'Opera';
        }
        else {
            $Browser = 'Other';
        }
        return $Browser;
    }

    /** 获取客户端系统类型
     * @return string
     */
    private function GetOS() {
        $OS = $_SERVER['HTTP_USER_AGENT'];
        if (preg_match('/win/i',$OS)) {
            $OS = 'Windows';
        }
        elseif (preg_match('/mac/i',$OS)) {
            $OS = 'MAC';
        }
        elseif (preg_match('/linux/i',$OS)) {
            $OS = 'Linux';
        }
        elseif (preg_match('/unix/i',$OS)) {
            $OS = 'Unix';
        }
        elseif (preg_match('/bsd/i',$OS)) {
            $OS = 'BSD';
        }
        else {
            $OS = 'Other';
        }
        return $OS;
    }

    /**
     * 添加访问记录
     * @param int $type
     */
    public function addAccessLog(){
//        if (Config::get('access_log') and Config::get('app_debug') == false){
        if (Config::get('access_log')){
            $system = $this->GetOS();
            $browser = $this->GetBrowser();
            $ua = $_SERVER['HTTP_USER_AGENT'];
            $request = Request::instance();
            $request_address = $request->url();
            $ip = ip2long($request->ip());
            if (empty($_SERVER['HTTP_REFERER'])){
                $refer = 'unknow';
            }else{
                $refer = $_SERVER['HTTP_REFERER'];
            }
            $AccessLogModel = new AccessLogModel();
            if (Session::has('userid')){
                $userid = Session::get('userid');
            }else{
                $userid = 0;
            }
            $AccessLogModel->addRequestLog($userid,$system,$browser,$ua,$request_address,$refer,$ip);
        }
    }

    /**
     * 删除指定ip记录。
     * 此处无需ip2long操作
     * @param $ip
     * @return bool
     */
    public function deleteAllAccessLogByIp($ip){
        $AccessLogModel = new AccessLogModel();
        $res = $AccessLogModel->deleteAllRequestLogByIp($ip);
        if ($res == 0){
            return false;
        }else{
            return true;
        }
    }

    /**
     * 删除全部记录
     */
    public function deleteAllAccessLog(){
        $AccessLogModel = new AccessLogModel();
        $AccessLogModel->deleteAllRequestLog();
    }
}