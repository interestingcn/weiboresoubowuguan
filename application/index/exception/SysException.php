<?php
namespace app\index\exception;
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | 微博热搜博物馆 异常处理接管
// +----------------------------------------------------------------------
// | Author: Meloncn (interestingcn01@gmail.com)
// +----------------------------------------------------------------------
// | Github: https://github.com/meloncn/weiboresoubowuguan
// +----------------------------------------------------------------------
use think\exception\Handle;
use think\exception\HttpException;
use think\Log;
use think\Request;
use think\Config;
use app\common\controller\Pusher;
class SysException extends Handle
{
    /**
     * @param \Exception $e
     * @return \think\Response|\think\response\View
     *
     * $statusCode      状态码
     * $msg             出错信息
     * $errorType       日志级别
     * $time            时间
     * $guestMsg        对外输出信息
     *
     *
     *      $errorType
     * log 常规日志，用于记录日志
     * error 错误，一般会导致程序的终止
     * notice 警告，程序可以运行但是还不够完美的错误
     * info 信息，程序输出信息
     * debug 调试，用于调试信息
     * sql SQL语句，用于SQL记录，只在数据库的调试模式开启时有效
     *
     */
    public function render(\Exception $e)
    {

//        开启框架Debug模式，异常交由框架处理
        if (Config::get('app_debug')){
            return parent::render($e);
            exit;
        }

        //捕获错误
        if ($e instanceof HttpException) {
            $statusCode = $e->getStatusCode();
        }
        //未知错误
        if (!isset($statusCode)) {
            $statusCode = 500;
            $msg = '应用服务器内部错误';
        }

        switch ($statusCode){
            case 404:
                $guestMsg = '您所访问的页面不存在';
                $errorType = 'info';
                $isLog = Config::get('app_404_log');
                $isPush = Config::get('app_push_404');
                break;
            case 500:
                $guestMsg = '服务器内部错误';
                $errorType = 'error';
                $isLog = Config::get('app_500_log');
                $isPush = Config::get('app_push_500');
                break;
        }

//        日志公共参数
        $request = Request::instance();
        $msg = $e->getMessage();
        $ip = $request->ip();
        $url = $request->url(true);
        $time = date('Y年m月d日 H时i分s秒',$_SERVER['REQUEST_TIME']);

        //日志生成
        if ($isLog){
            Log::record("
        状态码：$statusCode
        错误信息 ：$msg
        发生时间 ：$time
        远程ip：$ip
        访问地址：$url
        ",$errorType);
        }

//        Wxpusher 推送模块
        if (Config::get('app_wxpusher_on')){
            if ($isPush == true){
                $pusher = new Pusher();
                $pusher->infoPusher('HTTP异常',$msg,true,$statusCode);
            }
        }

        return \view('exception/http_exception',[
            'title' => $statusCode,
            'code' => $statusCode,
            'msg'  => $guestMsg,
//            'msg'  => $msg,  //输出详细错误
            'time' => $time,
        ],'',$statusCode);
    }
}
