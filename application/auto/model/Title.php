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

class Title extends Model{

    /**
     * 检查 标题 在 Title 表内是否已经注册
     */
    public function isInTitle($title){
        $res = $this->where('title','=',$title)->value('id');
        if ($res == NULL){
            return false;
        }else{
            return $res;
        }
    }
    /**
     *  向 Title 表注册数据
     */
    public function addTitle($title,$time,$rank,$star,$getId = false)
    {
        $this->data([
            'title'  =>  $title,
            'createTime' =>  $time,
            'maxRank'  =>  $rank,
            'maxStar' =>  $star
        ],true);
        $this->isUpdate(false)->save();
        if ($getId) {
            return $this->id;
        }
    }

    /**
     *  重置最大值时使用此方法进行查找
     */
    public function needMax(){
        return $res = $this->where('maxRank',null)->where('maxStar',null)->value('id');
    }

    /**
     *  查找需要更新最大值的条目
     */
    public function needCheck(){
        return $res = $this->where('check','=','1')->value('id');
    }

    /**
     * 最大值检查完成进行标记
     * @param $id
     */
    public function check_complete($id){
        $this->save(['check'  => null,],['id' => $id]);
    }
    /**
     *      获取最大值纪录
     */
    public function getMaxRecord($titleId,$type){
        if ($type == 'rank'){
            return $this->where('id','=',$titleId)->value('maxRank');
        }elseif($type == 'star'){
            return $this->where('id','=',$titleId)->value('maxStar');
        }elseif($type == 'all'){
            return $this->where('id','=',$titleId)->find();
        }else{
            return false;
        }
    }

    /**
     *      更新最大值纪录
     */
    public function updateMaxRecord($titleId,$type,$value){
        if ($type == 'rank'){
           return $this->save(['maxRank'  => $value,],['id' => $titleId]);
        }elseif ($type == 'star'){
            return $this->save(['maxStar'  => $value,],['id' => $titleId]);
        }else{
            return false;
        }
    }

    /**
     * 将全部项目添加到待核查队列
     * @return $this
     */
    public function addAllNeedCheck(){
        return $this->where('createTime','>',0)->update(['check' => 1]);
    }

    /**
     * 获取待核查队列中剩余数目
     * @return int|string
     *
     */
    public function getNeedCheckNum(){
        return $this->where('check','=',1)->count();
    }


}