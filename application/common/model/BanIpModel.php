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

class BanIpModel extends Model{

    protected $table = 'ban_ip';

    public function getAdminAddAttr($value)
    {
        switch ($value) {
            case '0':
                $state = '系统自动';
                break;
            case '1':
                $state = '管理员';
                break;
            default:
                $state = 'unknow';
        }
        return $state;
    }
    /**
     * 添加信息到IP黑名单
     * @param $ipddr
     * @param $expire_minuites
     * @param $remarks
     * @param $is_admin
     * @return false|int
     */
    public function addIp($ipddr,$expire_minuites = 0,$remarks = null,$is_longIP = false,$is_admin = true){
        if ($is_admin == true){$is_admin = 1;}else{$is_admin = 0;}
        if ($expire_minuites == 0){
            $expire_minuites = 60000000;
        }
        if ($is_longIP){
            $ip = $ipddr;
        }else{
            $ip = ip2long($ipddr);
        }
        $this->data([
            'ip'  =>  $ip,
            'expire' => time() + $expire_minuites*60,
            'add_time' =>time(),
            'remarks' => $remarks,
            'admin_add' => $is_admin
        ]);
        return $this->isUpdate(false)->save();
    }

    /**
     * 检测IP是否处于黑名单
     * @param $ipaddr
     * @return int|string
     */
    public function checkIpBlackList($ipaddr,$is_longIP = false){
        if ($is_longIP){$ip = $ipaddr;}else{$ip = ip2long($ipaddr);}
        $res = $this->where('ip','=',$ip)->where('expire','>',time())->count();
        if ($res == 0){
            return false;
        }else{
            return true;
        }
    }

    /**
     * 清除已过期黑名单
     * 返回 影响行数
     */
    public function clean(){
        return $this->where('expire','<',time())->delete();
    }

    /**
     *  当前时间被Ban的ip数量
     * @return int|string
     */
    public function nowInBanIpCount(){
        $time = time();
        return $this->where('add_time','<',$time)->where('expire','>',$time)->count();
    }

    /**
     * 获取最后n条被ban的IP
     */
    public function getLastBanIp($num = 10){
        return $this->order('id','desc')->limit($num)->select();
    }

    /**
     *  获取当前被阻断IP列表
     * @param $num
     * @return \think\Paginator
     */
    public function getBanInfo($num){
        return $this->where('add_time','<',time())->where('expire','>',time())->paginate($num, false, ['query' => request()->param()]);
    }

    /**
     *  获取被阻断IP全部记录
     * @param $num
     * @return \think\Paginator
     */
    public function getBanList($num){
        return $this->order('add_time','desc')->paginate($num, false, ['query' => request()->param()]);
    }

    /**
     *  解除IP黑名单
     * @param $ipaddr
     * @param bool $isLong
     * @return int
     */
    public function unBan($ipaddr,$isLong = true){
        if ($isLong){
            $ip = $ipaddr;
        }else{
            $ip = ip2long($ipaddr);
        }
        return $this->where('ip','=',$ip)->delete();
    }


}