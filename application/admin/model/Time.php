<?php
namespace app\admin\model;
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | 微博热搜博物馆 
// +----------------------------------------------------------------------
// | Author: Meloncn (interestingcn01@gmail.com)
// +----------------------------------------------------------------------
// | Github: https://github.com/meloncn/weiboresoubowuguan
// +----------------------------------------------------------------------

use think\Model;
class Time extends Model{
    /**
     *  获取指定时间新增数据
     */
    public function getTimeCount($startTime,$endTime = null){
        if ($endTime == null){$endTime = date('YmdHis');}
        return $this->where('time','>',$startTime)->where('time','<',$endTime)->count();
    }

    /**
     * 获取最后更新的时间
     */
    public function getLastTime(){
        return $this->order('time','desc')->limit(1)->value('time');
    }
}