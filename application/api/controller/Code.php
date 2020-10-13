<?php
namespace app\api\controller;
use phpmailer\PHPMailer;

class Code extends Common {
	public function getCode() {
		echo 'getCode';
		$usercontact = $this->params['user_contact'];
		$exist = $this->params['is_exist'];
		$usercontact_type = $this->checkusercontact($usercontact);
		switch ($usercontact_type) {
		case 'phone':
			$this->getCodeByusercontact($usercontact, 'phone', $exist);
			break;
		case 'email':
			$this->getCodeByusercontact($usercontact, 'email', $exist);
			break;
		}
	}

	/**
	 * send code to phone
	 * @param  [string] $usercontact [usercontact]
	 * @param  [string] $type [phone|email]
	 * @param  [int] $exist [usercontact in db?1:0]
	 * @return [json]        [json data]
	 */
	public function getCodeByUsercontact($usercontact, $type, $exist) {
		$this->checkExist($usercontact, $type, $exist); //check usercontact is existed
		if (session("?", $usercontact . '_last_send_time')) {
			if (time() - session($usercontact . '_last_send_time') < 60) {
				$this->returnMsg(400, $type . '验证码每1分钟只能发送一次！');
			}
		} //frequent of code request < 1min
		$code = $this->makeCode(6); //produce phone code
		//$md5_code = md5($usercontact . '_' . md5($code)); //md5 encode phone code
		session($usercontact . '_code', $code); //session md5_code
		session($usercontact . '_last_send_time', time()); //session last code send time
		if ($type == 'phone') {
			$this->sendCodeToPhone($usercontact, $code);
		} //send code
		if ($type == 'email') {
			$this->sendCodeToEmail($usercontact, $code);
		} //send code
	}

	/**
	 * make code
	 * @param  [int] $num [code's length]
	 * @return [int]      [code]
	 */
	public function makeCode($num) {
		$max = pow(10, $num) - 1;
		$min = pow(10, $num - 1);
		return rand($min, $max);
	}

	/**
	 * send code to user by phone
	 * @param  [string] $phone [user phone]
	 * @param  [int] $code  [code]
	 * @return [json]        [send status]
	 */
	public function sendCodeToPhone($phone, $code) {
		echo 'sendCodeToPhone';
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, 'msg api sp url');
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_POST, 1);
		$data = [
			'appid' => '',
			'to' => $phone,
			'project' => '',
			'vars' => '{"code":' . $code . ',"time":"60"}',
			'signature' => '',
		];
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		$res = curl_exec($curl);
		curl_close($curl);
		$res = json_encode($res);
		if ($res->status != 'success') {
			$this->returnMsg(400, $res->msg);
		} else {
			$this->returnMsg(200, '验证码发送成功！');
		}

	}

	/**
	 * send code to user by email
	 * @param  [string] $email [user email]
	 * @param  [int] $code  [code]
	 * @return [json]        [send status]
	 */
	public function sendCodeToEmail($email, $code) {
		echo 'sendCodeToEmail';
		$toemail = $email;
		$mail = new PHPMailer();
		$mail->isSMTP();
		$mail->CharSet = 'utf8';
		$mail->Host = 'smtp.163.com';
		$mail->SMTPAuth = 'true';
		$mail->Username = 'z17719496595@163.com';
		$mail->Password = 'zzz549326';
		$mail->SMTPSecure = 'ssl';
		$mail->Port = 994;
		$mail->setFrom('z17719496595@163.com', 'API test');
		$mail->addAddress($toemail, 'Username');
		$mail->addReplyTo('z17719496595@163.com', 'Reply');
		$mail->Subject = '您有新的验证码';
		$mail->Body = "您的验证码是$code,验证码的有效期为1分钟！勿回！";
		if (!$mail->send()) {
			$this->returnMsg(400, $mail->ErrorInfo);
		} else {
			$this->returnMsg(200, '验证码发送成功！');
		}
	}
}