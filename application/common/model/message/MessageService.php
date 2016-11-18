<?php
namespace app\common\model\message ;

class MessageService {
	
	const SESSION_KEY_RANDCODE = "_rand_code_" ;
	const SESSION_KEY_RANDCODE_TIME = "_rand_code_time_" ;

	private $shortMessage, $siteMessage, $siteUrl, $smsConfig ;

	public function __construct() {
		$this->shortMessage = new ShortMessage() ;
		$this->siteMessage = new SiteMessage() ;
		$this->siteUrl = config('site_url') ;
		$this->smsConfig = config('sms') ;
	}
	
	/**
	 * 发送验证码
	 * @param unknown $phone
	 */
	public function sendCode ($phone) {
		$code = $this->generateRandCode() ;
		session(self::SESSION_KEY_RANDCODE, $code) ;
		session(self::SESSION_KEY_RANDCODE_TIME, time()) ;
		return $this->shortMessage->sendRandCode($phone, $code) ;
	}
	
	/**
	 * 校验验证码
	 * @param string $code
	 * @return boolean
	 */
	public function validateCode ($code) {
		$sessionTime = session(self::SESSION_KEY_RANDCODE_TIME) ;
		$sessionCode = session(self::SESSION_KEY_RANDCODE) ;
		$expTime = $this->smsConfig['exp_time'] ;
		if ($sessionTime + $expTime * 60 < time()) {
			return false ;
		}
		$result = $code == $sessionCode ;
		if ($result) {
// 			session(self::SESSION_KEY_RANDCODE, $this->generateRandCode()) ;
		}
		return $result ;
	}
	
	/**
	 * 清除验证码
	 */
	public function clearCode () {
		session(self::SESSION_KEY_RANDCODE, $this->generateRandCode()) ;
	}

	/**
	 * 对账提醒
	 * 亲，您的订单{param1}已生效，请您及时登陆{param2}"我的账单”完成对账。
	 */
	public function sendRemindCheck ($uid, $orderno, $urlParam) {
		$p1 = $this->buildShortParam($orderno);
		$p2 = $this->buildSiteParam($orderno);
		$this->sendMessage($uid, MessageConfig::REMIND_CHECK, $p1, $p2, $urlParam) ;
	}

	/**
	 * 还款提醒
	 * 亲，您的订单{param1}还款日期为{param2}，请及时还款。详情可登陆{param3}进行查看。
	 */
	public function sendRemindRepay ($uid, $orderno, $date, $urlParam) {
		$p1 = $this->buildShortParam($orderno, $date) ;
		$p2 = $this->buildSiteParam($orderno, $date) ;
		$this->sendMessage($uid, MessageConfig::REMIND_REPAY,$p1, $p2, $urlParam) ;
	}

	/**
	 * 发货提醒
	 * 亲，您的订单{param1}已出库发货，正在运输中，请您耐心等待。详情可登陆{param2}进行查看。
	 */
	public function sendRemindDispatch ($uid, $orderno, $urlParam) {
		$p1 = $this->buildShortParam($orderno);
		$p2 = $this->buildSiteParam($orderno);
		$this->sendMessage($uid, MessageConfig::REMIND_DISPATCH, $p1, $p2, $urlParam) ;
	}

	/**
	 * 到货提醒
	 * 亲，您的订单{param1}已于{param2}到达指定地址，请您及时登陆{param3}核对并确认订单。
	 */
	public function sendRemindArrived ($uid, $orderno, $date, $urlParam) {
		$p1 = $this->buildShortParam($orderno,$date);
		$p2 = $this->buildSiteParam($orderno,$date);
		$this->sendMessage($uid, MessageConfig::REMIND_ARRIVED, $p1, $p2, $urlParam) ;
	}

	/**
	 * 审核未通过
	 * 亲，您创建的项目{param1}未通过审核。请及时登陆{param2}查看详情。
	 */
	public function sendAuditFail ($uid, $projectName, $urlParam) {
		$p1 = $this->buildShortParam($projectName);
		$p2 = $this->buildSiteParam($projectName);
		$this->sendMessage($uid, MessageConfig::AUDIT_FAIL, $p1, $p2, $urlParam) ;
	}

	/**
	 * 审核通过
	 * 亲，您创建的项目{param1}已于{param2}通过审核。请及时登陆{param3}查看详情。
	 */
	public function sendAuditSuccess ($uid, $projectName, $date, $urlParam) {
		$p1 = $this->buildShortParam($projectName,$date);
		$p2 = $this->buildSiteParam($projectName,$date);
		$this->sendMessage($uid, MessageConfig::AUDIT_SUCCESS, $p1, $p2, $urlParam) ;
	}

	/**
	 * 密码重置
	 * 亲，您的账号{param1}已于{param2}重置密码。若非本人操作，请及时登录{param3}进行修改。
	 */
	public function sendUserCodeReset ($uid, $username, $date) {
		$p1 = $this->buildShortParam($username,$date);
		$p2 = $this->buildSiteParam($username,$date);
		$this->sendMessage($uid, MessageConfig::USER_CODE_RESET, $p1, $p2) ;
	}

	protected function sendMessage ($uid, $messageType, $shortMsgParam, $siteMsgParam, $url=[]) {
		$userMessageConfig = new UserMessageConfig($uid) ;
		$userConfig = $userMessageConfig->getConfig() ;
		$messageConfig = $userConfig[$messageType] ;
		if ($messageConfig % MessageConfig::VAL_SHORT_MSG === 0) {
			$this->shortMessage->doSend($uid, $messageType, $shortMsgParam) ;
		}
		if ($messageConfig % MessageConfig::VAL_SITE_MSG === 0) {
			$this->siteMessage->doSend($uid, $messageType, $siteMsgParam, $url) ;
		}
	}

	private function buildShortParam () {
		$vars = func_get_args() ;
		$array = [] ;
		foreach ($vars as  $v) {
			$array[] = $v ;
		}
		$array[] = $this->siteUrl ;
		return $array ;
	}

	private function buildSiteParam () {
		$vars = func_get_args() ;
		$array = [] ;
		foreach ($vars as  $v) {
			$array[] = $v ;
		}
		return $array ;
	}
	
	private function generateRandCode () {
		return substr(mt_rand(1000000,1999999), 1, 6) ;
	}

}
