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
class Contributing extends Model{

    public function getInfo(){
        return $this->paginate(50);
    }

    public function add($name,$price){
        $this->data([
            'name'  =>  $name,
            'price' =>  $price
        ]);
        return $this->save();
    }

    public function deleteInfoById($id){
        return $this->where('id','=',$id)->delete();
    }
}