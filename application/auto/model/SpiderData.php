<?php
namespace app\auto\model;
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

class SpiderData extends Model{

    /**
     * @return mixed
     *
     * 获得第一条数据
     */
    public function getFirstTime($order = 'asc'){
        $data = $this->order('id',$order)->value('group');
        return $data;
    }

    /**
     * @param $time
     * @return false|\PDOStatement|string|\think\Collection
     *
     *  获取指定时间数据
     */
    public function getItem($time){
        $res = $this->where('group','=',$time)->select();
        return $res;
    }

    /**
     * @param $time
     * 删除指定时间数据
     */
    public function deleteTime($time){
        $this->where('group','=',$time)->delete();
    }

}
