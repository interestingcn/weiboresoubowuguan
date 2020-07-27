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

class Title extends Model{
    /**
     *  获取指定时间新增名词
     */
    public function getTitleCount($startTime,$endTime = null){
        if ($endTime == null){$endTime = time();}
        return $this->where('createTime','>',$startTime)->where('createTime','<',$endTime)->count();
    }

    /**
     *  获取需要核验数目
     * @return int|string
     */
    public function getNeedCheckNum(){
        return $this->where('check','=',1)->count();
    }

    /**
     *  获取全部Title数目
     */
    public function getAllCount(){
        return $this->count();
    }

    /**
     *  添加需要检查的项目
     * @param $start
     * @param $end
     * @return $this
     */
    public function addCheck($start,$end){
        return $this->where('createTime','>=',$start)->where('createTime','<',$end)->update(['check' => 1]);
    }


    /**
     * 删除全部待核查项目
     * @return $this
     */
    public function deleteAllNeedCheck(){
        return $this->where('createTime','>',0)->update(['check' => null]);
    }

    /**
     * 将全部项目添加到待核查
     * @return $this
     */
    public function addAllNeedCheck(){
        return $this->where('createTime','>',0)->update(['check' => 1]);
    }
}