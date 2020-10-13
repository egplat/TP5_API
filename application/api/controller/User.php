<?php
namespace app\api\controller;

class User extends Common {
	public function index() {
		echo 'welcom to user interface!';
	}

	/**
	 * user login
	 * @return [json] [result of user's login]
	 */
	public function login() {
		echo 'welcom to user interface->login!';
		$data = $this->params;
		$puser_name = $data['user_name'];
		$puser_pwd = $data['user_pwd'];
		if ($this->checkExist($puser_name)) {
			if ($db_res = $this->checkUserPwdLcode($puser_name, $puser_pwd)) {
				$this->returnMsg(200, '登录成功!', $db_res);
			} else {
				$this->returnMsg(400, '用户名或密码错误！');
			} //check user\pwd
		} else {
			$this->returnMsg(400, '用户不存在！');
		} //check user exist
	}

	/**
	 * user register
	 * @return [json] [result of user's register]
	 */
	public function register() {
		echo 'welcom to user interface->register!';
		//$db_data = [];
		$data = $this->params;
		$puser_name = $data['user_name'];
		$puser_contact = $data['user_contact'];
		$pcode = intval($data['code']);
		//$this->checkCode($puser_contact, $pcode); //check code
		if ($this->checkExist($puser_name)) {
			$this->returnMsg(400, '用户已存在！');
		}
		$user_contact_type = $this->checkUsercontact($puser_contact); //check usercontact's type[phone/email]
		switch ($user_contact_type) {
		case 'phone':
			$this->checkExist($puser_contact, 'phone', 0);
			$db_data['user_phone'] = $puser_contact;
			break;
		case 'email':
			$this->checkExist($puser_contact, 'email', 0);
			$db_data['user_email'] = $puser_contact;
			break;
		}
		$db_data['user_name'] = $data['user_name'];
		$db_data['user_pwd'] = $data['user_pwd'];
		$db_data['user_rtime'] = time(); //register time
		//dump($db_data);
		$res = db('user')->insert($db_data); //insert into db
		if (!$res) {
			$this->returnMsg(400, '用户注册失败！');
		} else {
			unset($db_data['user_pwd']);
			$this->returnMsg(200, '注册成功！', $db_data);
		}
	}

	/**
	 * upload headimg
	 * @return [json] [result of upload]
	 */
	public function uploadHeadimg() {
		echo 'welcon to user interface->uploadHeadimg!';
		$data = $this->params;
		$headimg_path = $this->uploadFile($data['user_headimg'], 'headimg'); //get saved headimg path
		$server_headimg_path = db('user')
			->where('user_name', $data['user_name'])
			->value('user_headimg'); //save pre headimg path
		$res = db('user')
			->where('user_name', $data['user_name'])
			->setField('user_headimg', $headimg_path); //insert headimg path to db
		if ($res) {
			if ($server_headimg_path) {
				unlink(ROOT_PATH . 'public' . $server_headimg_path);
			} //delete pre headimg path in file
			$this->returnMsg(200, '用户头像上传成功！');
		} else {
			$this->returnMsg(400, '用户头像上传失败！');
		}
	}
}