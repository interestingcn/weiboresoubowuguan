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

class System extends Model
{
    public function lastOnline($service){
        return $this->where('item', '=',$service)->value('value');
    }
}