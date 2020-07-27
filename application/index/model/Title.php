<?php
namespace app\index\model;
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
     * @param $titleId
     * @return mixed
     *  通过 titleID 获取标题名称
     */
    public function getTitleName($titleId){
        return $this->where('id','=',$titleId)->value('title');
    }

    /**
     * 根据标题获取id
     */
    public function getTitleId($title){
        $res = $this->where('title','=',$title)->value('id');
    }

    /**
     * @param $titleID
     * @return array|false|\PDOStatement|string|Model
     *  通过 TitleID 获取 标题全部信息
     */
    public function getTitleInfo($titleID = 0){
        if ($titleID !== 0){
            return $this->where('id','=',$titleID)->find();
        }else{
            return null;
        }

    }

    /**
     * @param $key
     * @param int $num
     * @return false|\PDOStatement|string|\think\Collection|\think\Paginator
     *
     * 搜索条目内容
     */
    public function searchItem($key, $num = 0)
    {
        if ($num == 0) {
            return $this->where('title', 'like', "%$key%")->order('createTime','desc')->select();
        } else {
            return $this->where('title', 'like', "%$key%")->order('createTime','desc')->paginate($num, false, ['query' => request()->param()]);//避免分页信息被覆盖
        }
    }

    /**
     * @param $time
     * @param int $num
     * @return false|\PDOStatement|string|\think\Collection|\think\Paginator
     *  Title 表
     * 聚合搜索条目内容
     */
    public function convergentSearch($time,$num = 0){
        if ($num == 0){
            return $this->where('createTime','like',"$time%")->order('createTime','desc')->select();
        }else{
            return $this->where('createTime','like',"$time%")->order('createTime','desc')->paginate($num, false, ['query' => request()->param()]);
        }
    }




}