<?php
namespace app\common\model;
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

class AccessLogModel extends Model
{
    protected $table = 'access_log';
    /**
     *  向 Access_log 表中添加访问信息
     * @param $system
     * @param $browser
     * @param $ua
     * @param $request_address
     * @param $type
     * @param $ip
     * @return false|int
     */
    public function addRequestLog($userid,$system,$browser,$ua,$request_address,$refer,$ip){
        $data = ([
            'userid'    => $userid,
            'time'      => time(),
            'system'    => $system,
            'browser'   => $browser,
            'ua'        => $ua,
            'request'   => $request_address,
            'refer'     => $refer,
            'ip'        => $ip,
        ]);
        return $this->save($data);
    }

    /**
     * 删除指定ip地址访问记录
     * @param $ip_address
     * @return int
     */
    public function deleteAllRequestLogByIp($ip_address){
        $ip = ip2long($ip_address);
        return $this->where('ip','=',$ip)->delete();
    }


    /**
     * 删除所有访问日志
     */
    public function deleteAllRequestLog(){
        return $this->delete();
    }

    /**
     * 删除7天前访问日志
     * @return int
     */
    public function deleteNDaysAgoRequestLog($day){
        $time = time() - $day*24*60*60;
        return $this->where('time','<',$time)->delete();
    }


    /**
     * 获取近？分钟访问日志
     * @param int $min
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getLastRequest($min = 1){
        $time = time();
        $minute = $min * 60;
        return $this->where('time','>',$time-$minute)->where('time','<',$time)->select();
    }

    /**
     * 统计某长ip近？分钟访问次数
     * @param $long_ip
     * @param $lastTime_min
     * @return int|string
     */
    public function getRequestCountByIp($long_ip,$lastTime_min = 0,$startTime = 0,$endTime = 0){
        if($startTime == 0){
            $time = time();
            $lastTime = $lastTime_min * 60;
            return $this->where('ip','=',$long_ip)->where('time','>',$time-$lastTime)->where('time','<',$time)->count();
        }else{
            if ($endTime == 0){
                $endTime = time();
            }
            return $this->where('ip','=',$long_ip)->where('time','>',$startTime)->where('time','<',$endTime)->count();
        }
        }

    /**
     * 统计某长id近？分钟访问次数
     * @param $long_ip
     * @param $lastTime_min
     * @return int|string
     */
    public function getRequestCountByUserId($userid,$lastTime_min = 0,$startTime = 0,$endTime = 0){
        if($startTime == 0){
            $time = time();
            $lastTime = $lastTime_min * 60;
            return $this->where('userid','=',$userid)->where('time','>',$time-$lastTime)->where('time','<',$time)->count();
        }else{
            if ($endTime == 0){
                $endTime = time();
            }
            return $this->where('userid','=',$userid)->where('time','>',$startTime)->where('time','<',$endTime)->count();
        }
    }
    /**
     * 获取某IP近？分钟访问详细
     * 获取某IP访问详情
     * @param $long_ip
     * @param $lastTime_min
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getRequestInfoByIp($long_ip,$lastTime_min = 1){
        $time = time();
        $lastTime = $lastTime_min * 60;
            return $this->where('ip','=',$long_ip)->where('time','>',$time-$lastTime)->where('time','<',$time)->select();
    }

    /**
     *  获取已记录请求总数
     * @return int|string
     */
    public function countOfAll(){
        return $this->count();
    }

    /**
     * 获取指定时间段请求信息
     */
    public function getRequestInfo($startTime,$endTime = null){
        if ($endTime == null){$endTime = time();}
        return $this->where('time','>',$startTime)->where('time','<',$endTime)->select();
    }
    /**
     * 获取指定时间段请求数量
     */
    public function getRequestCount($startTime,$endTime = null){
        if ($endTime == null){$endTime = time();}
        return $this->where('time','>',$startTime)->where('time','<',$endTime)->count();
    }

    /**
     * 返回最近 n 条访问记录
     * @param $num
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getRequest($num = 10){
        return $this->order('time','desc')->limit($num)->select();
    }

    /**
     * 分页形式返回请求记录
     */
    public function getRequests($num = 0)
    {
        if ($num == 0) {
            return $this->order('time','desc')->select();
        } else {
            return $this->order('time','desc')->paginate($num, false, ['query' => request()->param()]);//避免分页信息被覆盖
        }
    }

    /**
     *  通过ID查询请求详细信息
     * @param $id
     * @return array|false|\PDOStatement|string|Model
     */
    public function getRequestInfoById($id){
        return $this->where('id','=',$id)->find();
    }

    /**
     * 跟踪ip访问记录
     */
    public function getIpTrace($longIP,$num){
        return $this->where('ip','=',$longIP)->order('time','desc')->paginate($num, false, ['query' => request()->param()]);
    }

    /**
     * 跟踪用户ID访问记录
     */
    public function getUserIdTrace($userid,$num){
        return $this->where('userid','=',$userid)->order('time','desc')->paginate($num, false, ['query' => request()->param()]);
    }
}
