<?php

// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | 微博热搜博物馆 路由配置
// +----------------------------------------------------------------------
// | Author: Meloncn (interestingcn01@gmail.com)
// +----------------------------------------------------------------------

return [
		'/' => ['index/index/index',['ext' => 'html']],
        'info/[:itemID]/[:token]' => ['index/index/info',['ext' => 'html']],
        'search' => ['index/index/searchItem',['ext' => '']],
        'about/[:step]' =>  ['index/index/about',['ext' => 'html']],
        'help' => ['index/index/help',['ext' => 'html']],
        'subsidize' => ['index/index/subsidize',['ext' => 'html']],
        'contributingMember' => ['index/index/contributingMember',['ext' => 'html']],
        'advancedSearch' => ['index/index/advancedSearch',['ext' => 'html']],
        'aggregateSearchResults' => ['index/index/aggregateSearchResults',['ext' => 'html']],
        'recognitionRobot'  => ['index/index/recognitionRobot',['ext' => 'html']],
        'noF12' => ['index/index/noF12',['ext' => 'html']]
];
