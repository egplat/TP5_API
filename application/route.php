<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;
//api.tp5.com/user/2  ===>  www.tp5.com/index.php/api/user/index/id/2
Route::domain('api', 'api'); //toggle url_domain_deploy;follow api means module
//login
Route::post('user/login', 'User/login'); //modify .htaccess ('/' -> '?/')
//getCode
Route::get('user/:time/:token/:user_contact/:is_exist', 'code/getCode');
//register
Route::post('user/register', 'User/register');
//upload headimg
Route::post('user/headimg', 'User/uploadHeadimg');
return [
	'__pattern__' => [
		'name' => '\w+',
	],
	'[hello]' => [
		':id' => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
		':name' => ['index/hello', ['method' => 'post']],
	],

];
