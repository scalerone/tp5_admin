<?php
namespace app\common\model\message ;

use utils\Http ;
use app\common\model\BaseModel;
use think\Model;
use app\common\model\UserModel;
class ShortMessage extends BaseModel {
	
	const CACHE_ACCTSS_TOKEN 		= "accessToken_189" ;
	const CACHE_ACCESS_TOKEN_EXPIRE = "accessToken_189_expire" ;
	const URL_GET_AT 				= "https://oauth.api.189.cn/emp/oauth2/v3/access_token" ;
	const URL_GET_TOKEN				= "http://api.189.cn/v2/dm/randcode/token" ;
	const URL_SEND_SMS				= "http://api.189.cn/v2/emp/templateSms/sendSms" ;
	const URL_SEND_CODE				= "http://api.189.cn/v2/dm/randcode/sendSms" ;
	
	const SEND_API_SUCCESS			= 0 ;
	
	const SEND_RESULT_SUCCESS 		= 1 ;
	const SEND_RESULT_ERROR			= 2 ;
	
	private $http , $config , $messageConfig , $userModel;
	
	protected $table = 'sm_short_message' ;
	
	public function __construct() {
		parent::__construct() ;
		$this->http = new Http() ;
		$this->config = config('sms') ;
		$this->messageConfig = new MessageConfig() ;
		$this->userModel = new UserModel() ;
	}
	
	public function doSend ($uid, $templateType, $param) {
		$templateId = $this->getTemplate($templateType) ;
		$phone = $this->userModel->get($uid)->Vc_mobile ;
		$json = json_encode($this->parse($param)) ;
		$data = [
				'template_id'		=>		$templateId ,
				'acceptor_tel'		=>		$phone ,
				'template_param'	=>		$json ,
		] ;
		$mid = $this->saveMsg($uid, $phone, $templateType, $json) ;
		$result = $this->post(self::URL_SEND_SMS, $data) ;
		$this->setSendStatus($mid, $result->res_code) ;
		$this->mlog($phone, $templateType, $param, $result->res_code) ;
		return $result ;
	}
	
	public function sendRandCode ($phone ,$code) {
		$result = null ;
		$token = $this->getToken() ;
		if ($token) {
			$data = [
					'token'		=>		$token ,	
					'phone'		=>		$phone ,
					'randcode'	=>		$code ,
					'exp_time'	=>		$this->config['exp_time'] ,
			] ;
			$result = $this->post(self::URL_SEND_CODE, $data) ;
			$this->mlog($phone, 'RAND_CODE', $code, $result->res_code) ;
			$date = date('Y-m-d H:i:s') ;
			file_put_contents(LOG_PATH.'code.log',"$date : $phone : $code \r\n" , FILE_APPEND) ;
		}
		return $result ;
	}
	
	private function getTemplate ($name) {
		return $this->messageConfig->getShortMessageIdByName($name) ;
	}
	
	private function post ($url , $data=[]) {
		$data['app_id'] = $this->config['app_id'] ;
		$data['access_token'] = $this->getAccessToken() ;
		$data['timestamp'] = date('Y-m-d H:i:s') ;
		$data['sign'] = $this->getSign($data) ;
		$result = $this->http->post($url, $data) ;
		return json_decode($result) ;
	}
	
	private function getToken () {
		$result = $this->post(self::URL_GET_TOKEN) ;
		if ($result && self::SEND_API_SUCCESS === $result->res_code) {
			return $result->token ;
		} 
	}
	
	private function parse ($param) {
		$result = [] ;
		for ($i = 1 ; $i <= count($param) ; $i ++) {
			$result['param'.$i] = $param[$i-1] ;
		}
		return $result ;
	}
	
	private function getAccessToken () {
		$expireIn = config(self::CACHE_ACCESS_TOKEN_EXPIRE) ;
		$accessToken = config(self::CACHE_ACCTSS_TOKEN) ;
		if ($expireIn && $accessToken && $expireIn <= time()) {
			// nothing ...
		} else {
			$config = [
					'app_id'		=>		$this->config['app_id'],
					'app_secret'	=>		$this->config['app_secret'],
					'grant_type'	=>		$this->config['grant_type'],
			] ;
			$result = $this->http->post(self::URL_GET_AT, $config) ;
			$result = json_decode($result) ;
			if ($result && self::SEND_API_SUCCESS == $result->res_code) {
				$accessToken = $result->access_token ;
				$expireIn = $result->expires_in ;
				cache(self::CACHE_ACCTSS_TOKEN,$accessToken) ;
				cache(self::CACHE_ACCESS_TOKEN_EXPIRE,$expireIn + time()) ;
			}
		}
		return $accessToken ;
	}
	
	private function getSign($pm){
		ksort($pm);
		$pm_tm=array();
		foreach ($pm as $k=>$v) {
			if($k=='sign')continue;
			$pm_tm[] = $k.'='.$v;
		}
		$pm_str = join('&', $pm_tm);
		return base64_encode(hash_hmac('sha1', $pm_str, $this->config['app_secret'], true));
	}
	
	private function saveMsg ($uid,$phone,$tpl,$json) {
		$data = [
				'I_userID'		=>		$uid,
				'Vc_template'	=>		$tpl,
				'Vc_phone'		=>		$phone,
				'Vc_json'		=>		$json,
		] ;
		return $this->create($data)->id ;
	}
	
	private function setSendStatus ($mid, $resCode) {
		$status = ($resCode == self::SEND_API_SUCCESS ? 
				self::SEND_RESULT_SUCCESS : self::SEND_RESULT_ERROR) ;
		$date = date('Y-m-d H:i:s') ;
		parent::update(['I_deal'=>$status,'Dt_send'=>$date],['id'=>$mid]) ;
	}
	
	private function generateCode () {
		return substr(rand(), 1, 4) ;
	}
	
	private function mlog($phone, $type, $param, $result) {
		$json = json_encode($param) ;
		$date = date('Y-m-d H:i:s') ;
		$str = "{$date} : [SNS log] [Number:{$phone}] [Type:{$type}] [Param:{$json}] [Result:{$result}]" ;
		trace($str) ;
	}
	
}