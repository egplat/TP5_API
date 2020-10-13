<?php
namespace app\api\controller;

class Index {
	public function index() {
		$name = 'zyt';
		$pwd = '123456';
		$show = md5('api' . md5($name) . md5($pwd) . 'api');
		echo $show;
	}
}