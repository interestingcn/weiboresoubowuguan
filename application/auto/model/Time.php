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

class Time extends Model{

    /**
     * @param $value
     * @return int|string
     *   修改器
     *  |- 新
     *  |- 热
     *  |- 沸
     *  |- 爆
     *  |- 荐
     *
     */
    public function setStateAttr($value)
    {
        switch ($value)
        {
            case '新':
                $state = 1;
                break;
            case '热':
                $state = 2;
                break;
            case '沸':
                $state = 3;
                break;
            case '爆':
                $state = 4;
                break;
            case '荐':
                $state = 5;
                break;
            default:
                $state ='';
        }
        return $state;
    }

    /**
     * @param $titleId
     * @param $time
     * @param $rank
     * @param $star
     * @param $state
     *
     *  向 Time 表注册数据信息
     *
     */
    public function addTime($titleId,$time,$rank,$star,$state){
        $this->data([
            'title_id'  =>  $titleId,
            'time' =>  $time,
            'star' => $star,
            'rank' => $rank,
            'state' => $state,
        ],true);
        $this->isUpdate(false)->save();
    }

    /**
     * @param $titleId
     * @param $time
     * @return bool
     *
     *  检查 Time 表注册信息是否重复
     */
    public function checkNoRepeat($titleId,$time){
        $res = $this->where('title_id','=',$titleId)->where('time','=',$time)->find();
        if ($res == NULL){
            return true;
        }else{
            return false;
        }
    }

    /**
     *  通过Time表单独计算最高排名
     */
    public function getMaxRank($titleId){
         $ranks = $this->where('title_id','=',$titleId)->column('rank');
        return min($ranks);
    }

    /**
     * 通过Time表单独计算最高热度
     */
    public function getMaxStar($titleId){
        $stars = $this->where('title_id','=',$titleId)->column('star');
        return max($stars);
    }



}