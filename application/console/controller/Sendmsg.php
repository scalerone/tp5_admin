<?php
namespace app\console\controller ;


class Sendmsg extends ConsoleController {
    protected $orderModel ;
	
	public function __construct() {
	    
	 //   $this->configModel = new ConfigureModel() ;
	}
	
	public function sendAll() {
	
		$this->sendRemindMsg() ;
	}
	
	/**
	 * 消息提醒
	 */
	public function sendRemindMsg(){

	   
	    
	}
	
	
	
	
	
	
	
}