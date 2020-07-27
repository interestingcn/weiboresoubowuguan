<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | 微博热搜博物馆 
// +----------------------------------------------------------------------
// | Author: Meloncn (interestingcn01@gmail.com)
// +----------------------------------------------------------------------
// | Github: https://github.com/meloncn/weiboresoubowuguan
// +----------------------------------------------------------------------
// Admin模块公共文件
function salt($string){
    return md5('T5kijhL7bq'.$string.'5yCO0A');
}

/**
 * @param $value
 * @return bool
 * 判断是否符合group格式
 * $strict 严格模式 判断年月日是否符合规范
 */
function isGroup($value,$strict = false){
    if (strlen($value) != 12 or !is_numeric($value)){
        return false;
    }else{
        if ($strict){
//            严格模式
            $isyear = substr($value,0,4) <= date('Y',time()) ? true:false;
            $ismonth = substr($value,4,2)<13 and substr($value,4,2)>0?true:false;
            $isday = substr($value,6,2)<32 and substr($value,6,2) > 0?true:false;
            $ishour = substr($value,8,2)<25 and substr($value,8,2) > 0?true:false;
            $ismin = substr($value,10,2)<61 and substr($value,10,2)>0?true:false;
            if ($isyear and $ismonth and $isday and $ishour and $ismin){
//                严格模式通过
                return true;
            }else{
//                严格模式未通过
                return false;
            }
        }else{
//            粗略结构通过
            return true;
        }
    }
}