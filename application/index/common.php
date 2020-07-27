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
// 应用公共文件
/**格式化数据库内group数据为分割单个时间数组
 * @param $value
 * @return array|bool
 */
function formatGroup($value){
    if (isGroup($value)){
        return [
            'year' => substr($value,0,4)>date('Y',time())?date('Y',time()):substr($value,0,4),
            'month' => substr($value,4,2)>12?'12':substr($value,4,2),
            'day' => substr($value,6,2)>31?'31':substr($value,6,2),
            'hour' => substr($value,8,2)>24?'00':substr($value,8,2),
            'min' => substr($value,10,2)>60?'00':substr($value,10,2),
        ];
    }else{
        return false;
    }
}

/**
 * @param $value
 * @return bool|string
 * 返回group字段格式化为 年-月-日
 */
function formatGroupAll($value)
{
    if (strlen($value) != 12 or !is_numeric($value)) {
        return false;
    } else {
        $year = substr($value,0,4)>date('Y',time())?date('Y',time()):substr($value,0,4);
        $month = substr($value,4,2)>12?'12':substr($value,4,2);
        $day = substr($value,6,2)>31?'31':substr($value,6,2);
        $hour = substr($value,8,2)>24?'00':substr($value,8,2);
        $min = substr($value,10,2)>60?'00':substr($value,10,2);
        return $year.'年'.$month.'月'.$day.'日'.$hour.'时'.$min.'分';
    }
}


/**
 * @param $time
 * @return false|string
 *
 * 时间戳转换成group格式
 */

function convertToGroup($time = 0){
    if ($time == 0) {
        $time = time();
    }
    return date('YmdHi',$time);
}

/**
 * @param $group
 * @return false|int
 *
 *  group 转换为 时间戳格式
 */
function groupCoverToUnix($group){
    if (isGroup($group)){
        $value = formatGroup($group);
        return strtotime($value['year'].'-'.$value['month'].'-'.$value['day'] .''.$value['hour'].':'.$value['min'].':00');
    }
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

/**
 * @param $b
 * @param int $times
 * @return string
 * 格式化文件大小
 */
function formatSize($b,$times=0){
    if($b>1024 and $times){
        $temp=$b/1024;
        return formatSize($temp,$times+1);
    }else{
        $unit='B';
        switch($times){
            case '0':$unit='B';break;
            case '1':$unit='KB';break;
            case '2':$unit='MB';break;
            case '3':$unit='GB';break;
            case '4':$unit='TB';break;
            case '5':$unit='PB';break;
            case '6':$unit='EB';break;
            case '7':$unit='ZB';break;
            default: $unit='单位未知';
        }
        return sprintf('%.2f',$b).$unit;
    }
}

/**
 * @param $time
 * @return string
 *  格式化 秒 为友好时间格式
 */
function secondConversion($time)
{
    $d = floor($time / (3600*24));
    $h = floor(($time % (3600*24)) / 3600);
    $m = floor((($time % (3600*24)) % 3600) / 60);
    if($d> 0){
        return $d.'天'.$h.'小时'.$m.'分钟';
    }else{
        if($h >= 0 ){
            return $h.'小时'.$m.'分钟';
        }else{
            return $m.'分钟';
        }
    }
}

function createTk($key,$customsalt = 0){
    $salt = '4Tg0jxB6b5';
    $res = md5($key.$salt.$customsalt);
    $tk = substr($res, 0, 8) . '-' .
        substr($res, 8, 4) . '-' .
        substr($res, 12, 4) . '-' .
        substr($res, 16, 4) . '-' .
        substr($res, 20, 12);
    return $tk;
}

function checkTk($key,$value,$customsalt = 0){
    $salt = '4Tg0jxB6b5';
    $value = str_replace("-","",$value);
    $res = md5($key.$salt.$customsalt);
    if ($res == $value){
        return true;
    }else{
        return false;
    }
}

function createWeiboUrl($title){
    return 'https://s.weibo.com/weibo?q='.urlencode('#'. $title.'#');
}



