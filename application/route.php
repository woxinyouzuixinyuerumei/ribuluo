<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// return [
//     '__pattern__' => [
//         'name' => '\w+',
//     ],
//     '[hello]'     => [
//         ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
//         ':name' => ['index/hello', ['method' => 'post']],
//     ],
//
// ];
use think\Route;
// 注册路由到index模块的News控制器的read操作
//示例：
/*
Route::rule([
	'login' => ['admin/Demo/index',['ext'=>'html']],
	//'news/info-<code><id?>' => ['index/News/read',['ext'=>'html'],['code'=>'[a-zA-Z]+'],'id'=>'\d+']
]);
*/
/*
//多域名伪静态地址（相同）冲突解决办法
Route::domain('admin', [
	'login' => ['admin/Demo/index',['ext'=>'html']],
]);
Route::domain('union', [
	'login' => ['union/Login/index',['ext'=>'html']],
]);
*/

//生产环境配置：
//域名 www.ribuluo.com
Route::domain('www','www');
//域名 admin.ribuluo.com
Route::domain('admin','admin');
