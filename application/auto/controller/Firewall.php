<?php
namespace app\auto\controller;
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
use app\common\model\BanIpModel;
use app\auto\model\System;
class Firewall{
    public function Monitor(){
        $LatelyTime = 1; //检测间隔范围内IP
        $LimitTimes = 10; //限制请求次数
        $ban_expire = 30; //屏蔽时间

        $now = '['.date('Y-m-d H:i:s',\time()).']';
        $accessLogModel = new AccessLogModel();
        $ban = new BanIpModel();
        $res = $accessLogModel->getLastRequest($LatelyTime);

        $systemModel = new System();
        $systemModel->online('service_firewall');  //服务检测心跳

        if (empty($res)){
            echo $now.' - 时间段无访问产生'."\r\n";
            sleep(20);
            exit();
        }
        foreach ($res as $key => $k){
            $ip = $k['ip'];
//            根据访问次数拦截
            if ($ban->checkIpBlackList($ip,true) == false){
                $requestCount = $accessLogModel->getRequestCountByIp($ip,$LatelyTime);
//                单位时间访问量小于限制
                if ($requestCount < $LimitTimes){
                    if ($accessLogModel->getRequestCountByIp($ip,$LatelyTime * 2) > 8){
//                    每分钟访问次数小于限制，此处根据访问频率再次判断
//                    获取IP的请求信息详情
                        $requestInfo = $accessLogModel->getRequestInfoByIp($ip,$LatelyTime*2);
                        $interval = [];//记录访问与上一次访问差值
                        $typeList = []; //记录页面访问类型
                        foreach ($requestInfo as $keyb => $kb){
                            if ($keyb == 0){continue;}else{
                                $interval[] = $requestInfo[$keyb]['time'] - $requestInfo[$keyb - 1]['time'];
                            }
                            $typeList[] = $kb['type'];
                        }
                        if (count(array_unique($typeList == 1))){
                            $ban->addIp($ip,$ban_expire,"单位时间内访问页面类型仅一种",true,false);
                        }
                        if ($this->variance($interval) > 1.2){
                            $ban->addIp($ip,$ban_expire,"访问间隔相差较小疑似爬虫",true,false);
                        }
                    }
                }else{
//                    每分钟请求大于限制次数此处BAN掉
                    $ban->addIp($ip,$ban_expire,"$LatelyTime 分钟内访问超过 $LimitTimes 次 锁定 $ban_expire 分钟",true,false);
                }
            }
        }
        echo $now.' - IP审查处理完成。'."\r\n";
        sleep(30);
    }

    /**
     * 方差计算
     * @param $arr
     * @return array|float|int
     */
    private function variance($arr) {
        $length = count($arr);
        if ($length == 0) {
            return array(0,0);
        }
        $average = array_sum($arr)/$length;
        $count = 0;
        foreach ($arr as $v) {
            $count += pow($average-$v, 2);
        }
        $variance = $count/$length;
        return  $variance;
    }
}