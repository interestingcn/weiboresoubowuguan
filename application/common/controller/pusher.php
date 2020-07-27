<?php
namespace app\common\controller;
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | 微博热搜博物馆 
// +----------------------------------------------------------------------
// | Author: Meloncn (interestingcn01@gmail.com)
// +----------------------------------------------------------------------
// | Github: https://github.com/meloncn/weiboresoubowuguan
// +----------------------------------------------------------------------
use think\Config;
use think\Request;
use think\Log;
class Pusher{
    public function infoPusher($title,$Msg,$is_exception = false,$code = 0){
            $wxpusherToken = Config::get('app_wxpusher_token');
            $getId = Config::get('app_wxpusher_topicIds');
            $errortime = date('Y年m月d日 H时i分s秒',$_SERVER['REQUEST_TIME']);
            $wxpusher = new \notify\Wxpusher($wxpusherToken);

            if ($is_exception){
                $request = Request::instance();
                $ip = $request->ip();
                $url = $request->url(true);
                $body =
                    "异常警告 - ".$title.
                    " \n\n\r应用名称：" .Config::get('app_name').
                    " \n\n\r状态码：".$code.
                    " \n\n\r异常信息：".$Msg.
                    " \n\n\r发生时间：".$errortime.
                    " \n\n\r访问地址：".$url.
                    " \n\n\r远程ip：".$ip;
            }else{
                $body =
                    "应用通知 - ".$title.
                    " \n\n\r应用名称：" .Config::get('app_name').
                    " \n\n\r消息：".$Msg.
                    " \n\n\r发生时间：\n\r".$errortime;
            }
            $result = $wxpusher->send($body,'2',false,$getId,'',false);
            if ($result !== true) {
                foreach ($result as $key => $k) {
                    $errorUid = $k['uid'];
                    $errorTopicId = $k['topicId'];
                    $errorMessageId = $k['messageId'];
                    $errorCode = $k['code'];
                    $errorStatus = $k['status'];
                    $errorType = 'push';
                    Log::record("
                                    PushApplication：Wxpusher
                                    uid：$errorUid
                                    topicId ：$errorTopicId
                                    messageId ：$errorMessageId
                                    Remote_code：$errorCode
                                    status：$errorStatus
                                    ", $errorType);
                }
            }
    }
    }