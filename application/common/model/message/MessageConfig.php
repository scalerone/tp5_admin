<?php
namespace app\common\model\message ;

class MessageConfig {

	const REMIND_REPAY 		= 'remind_repay' ;
	const REMIND_CHECK 		= 'remind_check' ;
	const REMIND_ARRIVED 	= 'remind_arrived' ;
	const REMIND_DISPATCH 	= 'remind_dispatch' ;
	const AUDIT_FAIL 		= 'audit_fail' ;
	const AUDIT_SUCCESS 	= 'audit_success' ;
	const USER_CODE_RESET 	= 'user_code_reset' ;

	const VAL_SITE_MSG		= 2 ;
	const VAL_SHORT_MSG		= 3 ;

	private $environment ;

	private $config = [
			'dev'							=>		[
					// 还款
					self::REMIND_REPAY		=>		[
						// 短信ID
						"91551590",
						// 站内信标题，内容模版，跳转地址
						"还款提醒","亲，您的订单%s还款日期为%s，请及时还款。","/bill/listPage",
						// 消息类型 @see SiteMessage.MESSAGE_TYPE
						SiteMessage::MESSAGE_TYPE_TRADE,
					],
					// 对账
					self::REMIND_CHECK		=>		[
						"91551589",
						"对账提醒","亲，您的订单%s请您及时完成对账。","/bill/listPage",
						SiteMessage::MESSAGE_TYPE_TRADE,
					],
					// 到货
					self::REMIND_ARRIVED	=>		[
						"91551588",
						"到货提醒","亲，您的订单%s已于%s到达指定地址，请您及时确认订单。","/order/orderInfo",
						SiteMessage::MESSAGE_TYPE_TRADE,
					],
					// 发货
					self::REMIND_DISPATCH	=>		[
						"91551587",
						"发货提醒","亲，您的订单%s已出库发货，正在运输中，请您耐心等待。","/order/orderInfo",
						SiteMessage::MESSAGE_TYPE_TRADE,
					],
					// 审核未通过
					self::AUDIT_FAIL		=>		[
						"91551578",
						"审核未通过","亲，您创建的项目%s未通过审核。","/project/detail",
						SiteMessage::MESSAGE_TYPE_TRADE,
					],
					// 审核通过
					self::AUDIT_SUCCESS		=>		[
						"91551575",
						"审核通过","亲，您创建的项目%s已于%s通过审核。","/project/detail",
						SiteMessage::MESSAGE_TYPE_TRADE,
					],
					// 修改密码
					self::USER_CODE_RESET	=>		[
						"91551565",
						"修改密码提醒","亲，您的账号%s已于%s重置密码。若非本人操作，请及时修改。","",
						SiteMessage::MESSAGE_TYPE_SYSTEM,
					],
			] ,
	] ;

	public function __construct() {
		$messageConfig = config('message') ;
		$this->environment = $messageConfig['env'] ;
	}

	public function getShortMessageIdByName ($name) {
		$msgConfig = $this->getMessageConfigByName($name) ;
		return $msgConfig[0] ;
	}

	public function getSiteMessageTplByName ($name) {
		$msgConfig = $this->getMessageConfigByName($name) ;
		$messageType = count($msgConfig) == 5 ? $msgConfig[4] : SiteMessage::MESSAGE_TYPE_TRADE ; 
		$data = [
				'title'		=>		$msgConfig[1] ,
				'content'	=>		$msgConfig[2] ,
				'url'		=>		$msgConfig[3] ,
				'type'		=>		$messageType ,
		] ;
		return $data ;
	}

	private function getMessageConfigByName($name) {
		$config = $this->getEnvConfig() ;
		if (key_exists($name, $config)) {
			return $config[$name] ;
		}
		throw new \Exception('Message config not exist .') ;
	}

	private function getEnvConfig () {
		return $this->config[$this->environment] ;
	}

}
