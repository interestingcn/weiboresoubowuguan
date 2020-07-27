<?php
namespace app\index\controller;

// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | 微博热搜博物馆 
// +----------------------------------------------------------------------
// | Author: Meloncn (interestingcn01@gmail.com)
// +----------------------------------------------------------------------
// | Github: https://github.com/meloncn/weiboresoubowuguan
// +----------------------------------------------------------------------

use think\Controller;
use app\index\model\Time;
use app\index\model\Title;
use think\Request;
use think\Config;

use app\common\controller\Pusher; //推送服务
use app\common\controller\Access; //访问日志记录
use app\common\model\BanIpModel; //ip阻断

use app\index\controller\User;//用户模块控制器
use app\index\model\Contributing;//捐助者数据库
class Index extends Controller
{
    protected $beforeActionList = [
        'checkIpBlacklist',
        'checkAvailable' => ['only' => 'info,searchitem,aggregatesearchresults'], //历史时间单独验证是否登陆！前置操作方法名一律小写
    ];

    protected function checkIpBlacklist(){
        $request = Request::instance();
        $ip = $request->ip();
        $ban = new BanIpModel();
        if ($ban->checkIpBlackList($ip)){
            $this->error('您所在区域禁止访问！','','','600');
        }
    }
    protected function checkAvailable(){
        $user = new User();
        $user->checkLogin();
        $user->checkLave();
    }


    private function http_post($url,$postbody){
        $curl = curl_init();
        curl_setopt($curl,CURLOPT_URL,$url);
        // CURLOPT_RETURNTRANSFER  设置是否有返回值
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//这个是重点，如果是https请求一定要加这句话。
        curl_setopt($curl,CURLOPT_POST,true);
        curl_setopt($curl,CURLOPT_POSTFIELDS,$postbody);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public function index(Request $request)
    {
        $timeModel = new Time();
        $titleModel = new Title();
//        获取状态和时间参数
        $requestTime = date('YmdHi', strtotime($request->param('time')));  //此处先将用户输入转化为时间戳，再转化为YmdHi。对比汇聚搜索的替换形式
        $status = $request->param('status');
        if ($status == 'history'){
            $status = 'history';
            $time = $requestTime;
//            前期设计有误，此处在检索历史日期时单独判断是否 登陆！
            $user = new User();
            $user->checkLogin();
        }else{
            $status = 'now';
            $time = convertToGroup(time() - Config::get('app_wait')) ; //生成当前时间Group
        }

//        调试模式自动覆盖本地时间
        if(Config::get('app_debug')){
             $time = 202005052226;
             $status = 'now';
        }

        $res = $timeModel->listItem($time);
        if (empty($res)){
            // 传递空信号给模板
            $data = 0;
//            采集出错时发送通知
            if ($status == 'now' and Config::get('app_wxpusher_on') and Config::get('app_debug') == false){
              $pusher = new Pusher();
              $pusher->infoPusher('采集程序出错','采集程序疑似停止工作',true);
              $this->error('采集服务疑似启动失败，已将相关日志样本发送至管理员，请耐心等待程序恢复。','','','300');
            }
        }else{
            if ($status == 'history'){
                $user = new User(); //数据使用量自减 在检索历史数据时
                $user->decrease();
            }
            $data[] = [];
            foreach ($res as $value => $key){
                $data[$value]['rank'] = $key['rank'];
                $data[$value]['title'] = $titleModel->getTitleName($key['title_id']);
                $data[$value]['title_id'] = $key['title_id'];
                $data[$value]['jump_url'] = createWeiboUrl($data[$value]['title']);
                $data[$value]['star'] = $key['star'];
                $data[$value]['state'] = $key['state'];
                $data[$value]['time'] = formatGroupAll($key['time']);
            }
        }

        if ($status == 'now'){
            $access = new Access(1);//访问记录
            $this->assign('time',formatGroupAll($time));
            $this->assign('title','首页');
            $this->assign('list',$data);
            return $this->fetch('index');
        }else{
            $access = new Access(3);
            $this->assign('time',formatGroupAll($time));
            $this->assign('title','历史时间 - '.formatGroupAll($time));
            $this->assign('list',$data);
            return $this->fetch('history');
        }

    }

    /**
     * @param Request $request
     * @return mixed
     *  显示条目详情
     */
    public function info(Request $request){
//       强制转换为int，0或者空或者其他字符皆返回0
        $titleId = (int)$request->param('itemID');
        $token = $request->param('token');
        if ($titleId == 0){
            $this->redirect('index/index/index');
        }
        if (checkTk($titleId,$token) !== true){
            throw new \think\exception\HttpException(404,'Token令牌验证失败');
        }
        $timeModel = new Time();
        $titleModel = new Title();
        $titleInfo = $titleModel->getTitleInfo($titleId);
        if ($titleInfo == NULL){
            throw new \think\exception\HttpException(404);
        }
        $user = new User(); //数据使用量自减
        $user->decrease();
        $timeInfo = $timeModel->getItemInfo($titleId);
        $this->assign('title',$titleInfo['title']);
        $this->assign('maxRank',$titleInfo['maxRank']);
        $this->assign('maxStar',$titleInfo['maxStar']);
        $this->assign('createTime',formatGroupAll($titleInfo['createTime']));
        $this->assign('count_time', secondConversion(count($timeInfo)*60));
        $this->assign('timeInfo',$timeInfo);
        $access = new Access(2); //访问审计
       return $this->fetch('info');
    }

    /**
     * @param Request $request
     * @return mixed
     *  搜索指定条目
     */
    public function searchItem(Request $request){
        $key = $request->param('key');
        if (empty($key)){$this->error('缺少关键词');}
      $titleModel = new Title();
      $res = $titleModel->searchItem($key,100);
      $count = $res->total();
      $pages = $res->render();
      if ($count > 0){
          $user = new User(); //数据使用量自减
          $user->decrease();
      }
        $this->assign('titleInfo',$res);
        $this->assign('pages',$pages);
        $this->assign('key',$key);
        $this->assign('count',$count);
        $this->assign('title','关键词搜索');
        $this->assign('time',formatGroupAll(convertToGroup(\time())));
        $access = new Access(4); //访问审计
        return $this->fetch('searchTitle');
    }
    /**
     * @param Request $request
     * @return mixed
     *
     *  汇聚搜索结果显示
     */
    public function aggregateSearchResults(Request $request){
        $time = str_replace("-","",$request->param('time')).'0000'; //伪Group格式
        if (!isGroup($time,true)){$this->error('时间参数不合法！');};
        $titleModel = new Title();
        $timeModel = new Time();

        if ($request->param('firstAppearance') == 'on'){
//           仅首次出现内容检索
            $res = $titleModel->convergentSearch(substr($time, 0, 8),100);
            $count = $res->total();
            $pages = $res->render();
        }else{
//            包含次日重复值
            $res = $timeModel->convergentSearch(substr($time, 0, 8),100);
            $count = $res->total();
            $pages = $res->render();
        }

        $data[] = [];
        if ($request->param('firstAppearance') == 'on'){
//            仅首次出现  Title 表
            foreach ($res as $value => $key){
                $data[$value]['title'] = $key['title'];
                $data[$value]['id'] = $key['id'];
                $data[$value]['time'] = formatGroupAll($key['createTime']);
            }
        }else{
//            包含次日出现  Time 表
            foreach ($res as $value => $key){
                $data[$value]['title'] = $titleModel->getTitleName($key['title_id']);
                $data[$value]['id'] = $key['title_id'];
                $data[$value]['time'] = formatGroupAll($key['time']);
            }
        }

//        根据状态修改显示标题
        if ($count == 0){
            $this->assign('title','未能检索到相关内容');
        }else{
            $title = formatGroup($time);
            $title = $title['year'].'年'.$title['month'].'月'.$title['day'].'日'.' - 汇聚检索输出';
            $this->assign('title',$title);

            $user = new User(); //数据使用量自减
            $user->decrease();
        }
        $this->assign('titleInfo',$data);
        $this->assign('pages',$pages);
        $this->assign('searchTime',formatGroup($time));
        $this->assign('count',$count);
        $this->assign('time',formatGroupAll(convertToGroup(\time())));
        $access = new Access(5); //访问审计
        return $this->fetch('aggregateSearchResults');
    }
    /**
     *  关于 页面
     */
    public function about(Request $request){
        $step = $request->param('step');
        if ($step == 'main'){
            $access = new Access(8); //访问审计
            $this->assign('title','关于');
            return $this->fetch('index/about/about-main');
        }else{
            $access = new Access(7); //访问审计
            $this->assign('title','关于');
            return $this->fetch('index/about/about-cover');
        }
    }

    public function advancedSearch(){
        $this->assign('title','高级搜索');
        $access = new Access(6); //访问审计
        return $this->fetch('advancedSearch');
    }
//    帮助页面
    public function help(){
        $access = new Access(9); //访问审计
        $this->assign('title','使用指南');
        return $this->fetch('help');
    }
//    捐助页面
    public function subsidize(){
        $access = new Access(10); //访问审计
        $this->assign('title','资助本站');
        return $this->fetch('subsidize');
    }
    public function contributingMember(){
        $access = new Access(11); //访问审计
        $contributing = new Contributing();
        $contributingInfo = $contributing->getInfo();
        $this->assign('pages',$contributingInfo->render());
        $this->assign('info',$contributingInfo);
        $this->assign('title','贡献者');
        return $this->fetch('contributingMember');
    }

}

