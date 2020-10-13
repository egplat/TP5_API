<?php
namespace app\api\controller;
use think\Controller;
use think\Db;
use think\Image;
use think\Request;
use think\Validate;

class Common extends Controller {
//api security
	protected $request; //for dealing param
	protected $validater; //for verifying data/params
	protected $params; //for storing filtered params
	protected $rules = array(
		'User' => array(
			'login' => array(
				'user_name' => ['require', 'number', '/201\d{3}304\d{4}$/', 'length' => 13],
				'user_pwd' => ['require', 'length' => 32],
				'lcode' => ['require'],
			),
			'register' => array(
				'user_name' => ['require', 'number', '/201\d{3}304\d{4}$/', 'length' => 13],
				'user_contact' => ['require'],
				'user_pwd' => ['require', 'length' => 32],
				'code' => ['require', 'length' => 6],
			),
			'uploadheadimg' => array(
				'user_name' => ['require', 'number', '/201\d{3}304\d{4}$/', 'length' => 13],
				'user_headimg' => ['require', 'image', 'fileSize' => 2000000, 'fileExt' => 'jpg,png,bmp,jpeg'],
			),
		),
		'Code' => array(
			'getcode' => array(
				'user_contact' => ['require'],
				'is_exist' => ['require', 'number', 'length' => 1],
			),
		),
	);

	protected function _initialize() {
		parent::_initialize();
		$this->request = Request::instance();
		//dump($this->request->param(true));die;
		// $this->checkTime($this->request->only(['time']));
		// $this->checkToken($this->request->param());
		$this->params = $this->checkParams($this->request->param(true));
	}

	/**
	 * verify app timestamp
	 * @param  [array] $arr [includ param 'time']
	 * @return [json]		[result]
	 */
	public function checkTime($arr) {
		if (!isset($arr['time']) || intval($arr['time']) <= 1) {
			$this->returnMsg(400, '时间戳不正确！');
		}
		if (time() - intval($arr['time']) > 60) {
			$this->returnMsg(400, '请求超时！');
		}
	}

	/**
	 * verify app token
	 * @param  [array]  $arr  [request param]
	 * @return [json]         [result]
	 */
	public function checkToken($arr) {
		//verify app token
		if (!isset($arr['token']) || empty($arr['token'])) {
			$this->returnMsg(400, 'token不能为空！');
		}
		$app_token = $arr['token'];
		//produce service token
		unset($arr['token']);
		$service_token = '';
		foreach ($arr as $key => $value) {
			$service_token .= md5($value);
		}
		$service_token = md5('api' . $service_token . 'api');
		//dump($service_token);die;
		if ($app_token != $service_token) {
			$this->returnMsg(400, 'token不正确！');
		}

	}

	/**
	 * verify and filter params
	 * @param  [array] $arr [params]
	 * @return [array] $arr [filtered params]
	 */
	public function checkParams($arr) {
		// verify request params
		//dump($this->request->controller());die;
		$rule = $this->rules[$this->request->controller()][$this->request->action()];
		$this->validater = new Validate($rule);
		if (!$this->validater->check($arr)) {
			$this->returnMsg(400, $this->validater->getError());
		}
		return $arr;
	}

	/**
	 * check usercontact type
	 * @param  [string] $username [email or phone]
	 * @return [string]           [errorMsg|email|phone]
	 */
	public function checkUsercontact($usercontact) {
		$is_email = Validate::is($usercontact, 'email') ? 1 : 0;
		$is_phone = preg_match('/1[345678]\d{9}$/', $usercontact) ? 4 : 2;
		$flag = $is_email + $is_phone;
		switch ($flag) {
		case 2:
			$this->returnMsg(400, '邮箱或手机号不正确！');
			break;
		case 3:
			return 'email';
			break;
		case 4:
			return 'phone';
			break;
		}
	}

	/**
	 * check exist
	 * @param  [string] $value [username/usercontact]
	 * @param  string $type  [phone/email/NULL]
	 * @param  int $exist [0/1]
	 * @return [json]        [status]
	 */
	public function checkExist($value, $type = NULL, $exist = 0) {
		if (isset($type)) {
			$type_num = $type == 'phone' ? 2 : 4;
			$flag = $type_num + $exist;
			$phone_res = db('user')->where('user_phone', $value)->find();
			$email_res = db('user')->where('user_email', $value)->find();
			switch ($flag) {
			case '2':
				if ($phone_res) {
					$this->returnMsg(400, '此手机号已经注册！');
				}
				break;
			case '3':
				if (!$phone_res) {
					$this->returnMsg(400, '此手机号不存在！');
				}
				break;
			case '4':
				if ($email_res) {
					$this->returnMsg(400, '此邮箱已经注册！');
				}
				break;
			case '5':
				if (!$email_res) {
					$this->returnMsg(400, '此邮箱不存在！');
				}
				break;
			}
		} else {
			$username_res = db('user')->where('user_name', $value)->find();
			return $username_res;
		}

	}

	/**
	 * ckeck code
	 * @param  [string] $usercontact [usercontact]
	 * @param  [int] $code        [md5(usercontact._code)]
	 * @return [json]              [result]
	 */
	public function checkCode($usercontact, $code) {
		$last_time = session($usercontact . '_last_send_time');
		if (time() - $last_time > 60) {
			$this->returnMsg(400, '验证码超时!');
		} //check code over time
		if (session($usercontact . '_code') !== $code) {
			session($usercontact . '_code', null); //clear code session
			$this->returnMsg(400, '验证码不正确!');
		}
		session($usercontact . '_code', null); //clear code session
	}

	/**
	 * check user,pwd and lcode are correct
	 * @param  [string] $username [username]
	 * @param  [string] $userpwd  [md5(pwd)]
	 * @param  stirng $lcode    [lcode]
	 * @return [db_res]           [db query result]
	 */
	public function checkUserPwdLcode($username, $userpwd, $lcode = NULL) {
		if (isset($lcode)) {
		} //check lcode
		$db_res = db('user')
			->field('user_name, user_phone, user_email, user_rtime')
			->where('user_name', $username)
			->where('user_pwd', $userpwd)
			->find();
		return $db_res; //check username and userpwd
	}

	/**
	 * upload to server file
	 * @param  [string] $file [file path]
	 * @param  string $type [type of file]
	 * @return [string]       [file path]
	 */
	public function uploadFile($file, $type = '') {
		$info = $file->move(ROOT_PATH . 'public' . '/uploads/headimg'); //save img to file
		if ($info) {
			$path = '/uploads/headimg/' . $info->getSaveName(); //get img path
			if (!empty($type)) {
				$this->imageEdit($path, $type); //edit img
			}
			return str_replace('\\', '/', $path);
		} else {
			$this->returnMsg(400, $file->getError());
		}
	}
	public function imageEdit($path, $type) {
		$img = Image::open(ROOT_PATH . 'public' . $path);
		switch ($type) {
		case 'headimg':
			$img->thumb(200, 200, Image::THUMB_CENTER)->save(ROOT_PATH . 'public' . $path);
			break;
		}
	}

	/**
	 * @param  [int]  $code [state of request]
	 * @param  string $msg  [illustration]
	 * @param  array  $data [data]
	 * @return [json]       [return msg]
	 */
	public function returnMsg($code, $msg = '', $data = []) {
		$return_data['code'] = $code;
		$return_data['msg'] = $msg;
		$return_data['data'] = $data;
		echo json_encode($return_data);die;
	}
}