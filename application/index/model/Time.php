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

class Time extends Model
{

    /**
     * @param $value
     * @return string
     *  热搜标签转换
     */
    public function getStateAttr($value)
    {
        switch ($value) {
            case '1':
                $state = '新';
                break;
            case '2':
                $state = '热';
                break;
            case '3':
                $state = '沸';
                break;
            case '4':
                $state = '爆';
                break;
            case '5':
                $state = '荐';
                break;
            default:
                $state = '';
        }
        return $state;
    }

    /**
     * @param $value
     * @return string
     * 将排名为0的值改写为置顶
     */
    public function getRankAttr($value)
    {
        if ($value == '0') {
            return '置顶';
        } else {
            return $value;
        }
    }

    /**
     * @param int $time
     * @return false|\PDOStatement|string|\think\Collection
     *  列出指定时间数据  用于主页显示或者指定时间页面
     */
    public function listItem($time = 0)
    {
        if ($time == 0) {
            $time = convertToGroup();
        }
        return $this->where('time', '=', $time)->order('rank', 'asc')->select();
    }

    /**
     * @param int $titleID
     * @return false|null|\PDOStatement|string|\think\Collection
     *  获取指定 TitleID 的全部信息
     */
    public function getItemInfo($titleID = 0)
    {
        if ($titleID !== 0) {
            return $this->where('title_id', '=', $titleID)->select();
        } else {
            return null;
        }
    }


    /**
     * @param $time
     * @param int $num
     * @return false|\PDOStatement|string|\think\Collection|\think\Paginator
     *  Time 表
     * 聚合搜索条目内容
     */
    public function convergentSearch($time, $num = 0)
    {
        if ($num == 0) {
            return $this->where('time', 'like', "$time%")->group('title_id');
        } else {
            return $this->where('time', 'like', "$time%")->group('title_id')->paginate($num, false, ['query' => request()->param()]);
        }
    }


}


