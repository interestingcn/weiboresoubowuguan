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

use think\Controller;
use app\auto\model\SpiderData;
use app\auto\model\Title;
use app\auto\model\Time;
use app\auto\model\System;
use think\Debug;

class Power extends Controller
{
    /**
     * @return string
     * 从 spider_data 表内获取数据分别填入 Title 和 Time
     * 清空缓存表
     * 更新主程序数据
     */
    public function updateTitle()
    {
//       开始时间
        $now = '['.date('Y-m-d H:i:s',\time()).']';
        $spiderTable = new SpiderData();
        $timeTable = new Time();
        $titleTable = new Title();
        $time = $spiderTable->getFirstTime();

        if ($time == NULL) {
            return $now.'- 缓存表已处理完毕暂无待处理项'."\r"."\n";
            exit;
        }
        Debug::remark('start');
        //    获取爬取信息
        $item = $spiderTable->getItem($time);
        $total = count($item); //处理数据总量
        //       循环表内数据
        foreach ($item as $key => $k) {
            $title = $k['title'];
            $rank = $k['rank'];
            $star = $k['star'];
            //   判断是否已经存在Title表内
            $res = $titleTable->isInTitle($title);
            if ($res == false) {
                //  Title表中不存在时，创建Title表数据
                $titleId = $titleTable->addTitle($title, $time,$rank,$star, true);
                //   向 Time 表注册数据
                $timeTable->addTime($titleId, $time, $k['rank'], $k['star'], $k['state']);
            } else {
                //   Title表已经存在 直接获取Title_Id
                $titleId = $res;
                //   检查重复 避免填写重复数据
                $checkRepeat = $timeTable->checkNoRepeat($titleId, $time);
                if ($checkRepeat == false) {
                    continue;
                }else{
//                    判断Title表内最大排名最最高热度是否更新
                    $maxRecord = $titleTable->getMaxRecord($titleId,'all');
//                     更新title表内最高纪录值
                    if ($k['rank'] < $maxRecord['maxRank']){$titleTable->updateMaxRecord($titleId,'rank',$k['rank']);}
                    if ($k['star'] > $maxRecord['maxStar']){$titleTable->updateMaxRecord($titleId,'star',$k['star']);}
                    //   Time 注册数据
                    $timeTable->addTime($titleId, $time, $k['rank'], $k['star'], $k['state']);
                }
            }
        }
//      删除已处理完毕数据
        $spiderTable->deleteTime($time);
        $systemModel = new System();
        $systemModel->online('service_updateTitle'); // 服务心跳检测
//        计算运行消耗时间
        Debug::remark('end');
        return $now.'处理完成,版本批次为' . formatGroupAll($time) . "\r\n".'共计' . $total . "条项目 \r\n耗时". Debug::getRangeTime('start','end')."s \r\n\r\n";
    }

    /**
     * @return mixed
     * 当最大值纪录为空时使用此方法进行重建
     * 用于更新Title表最大值纪录手动刷新
     */
//  http://a.com/autoupdate.php/power/up
    public function updatemax(){
        $now = '['.date('Y-m-d H:i:s',\time()).']';
        $timeTable = new Time();
        $titleTable = new Title();
        $titleId = $titleTable->needMax();
        if ($titleId == null){
            echo "$now - 最大值纪录组建完成 \r\n";
            sleep('300');
        }else{
            Debug::remark('start');
            $maxStar = $timeTable->getMaxStar($titleId);
            $maxRank = $timeTable->getMaxRank($titleId);
            $titleTable->updateMaxRecord($titleId,'rank',$maxRank);
            $titleTable->updateMaxRecord($titleId,'star',$maxStar);
            Debug::remark('end');
            return "$now - 重建数据库热搜最大值纪录\r\n标题ID - $titleId \r\n数据库耗时 - ".Debug::getRangeTime('start','end')."s \r\n". "内存消耗 - ".Debug::getRangeMem('start','end')."\r\n\r\n";
        }
}

    /**
     * 最大值错误纠正系统
     * 将数据库Title表字段Check字段为1时使用此方法进行最大值重新计算
     */
    public function checkMax(){
        $now = '['.date('Y-m-d H:i:s',\time()).']';
        $timeTable = new Time();
        $titleTable = new Title();
        $titleId = $titleTable->needCheck();

        $systemModel = new System();
        $systemModel->online('service_checkMax'); // 服务心跳检测
        if($titleId == null){
            echo "$now - 数据核对完毕 \r\n";
            sleep('300');
        }else{
            Debug::remark('start');
            $maxStar = $timeTable->getMaxStar($titleId);
            $maxRank = $timeTable->getMaxRank($titleId);
            $titleTable->updateMaxRecord($titleId,'rank',$maxRank);
            $titleTable->updateMaxRecord($titleId,'star',$maxStar);
            $titleTable->check_complete($titleId);
            Debug::remark('end');
            return "$now - Max纪录核对检查\r\n标题ID - $titleId \r\n数据库耗时 - ".Debug::getRangeTime('start','end')."s \r\n". "内存消耗 - ".Debug::getRangeMem('start','end')."\r\n\r\n";
        }

    }

    /**
     * 自动创建待核查标记
     */
    public function autoReCheck(){
        $now = '['.date('Y-m-d H:i:s',\time()).']';
        $titleTable = new Title();
        $needCheck = $titleTable->addAllNeedCheck();
        $num = $titleTable->getNeedCheckNum();
        echo "$now - 添加 $needCheck 条数据到待核查队列 - 当前队列中待核查项目还有 $num 条 \r\n";
        sleep('20'); 
    }
}