<?php
namespace app\common\model\message ;

use app\common\model\BaseModel;
use think\Db;
use think\Model;

class UserMessageConfig extends BaseModel {
	
	protected $table = "sm_message_config" ;
	
	private $uid ;
	
	public function __construct($uid) {
		$this->uid = $uid ;
		parent::__construct() ;
	}
	
	public function createConfig () {
		$initVal = MessageConfig::VAL_SHORT_MSG * MessageConfig::VAL_SITE_MSG ;
		$sql = "insert into sm_message_config (user_id,remind_repay,remind_check,remind_arrived,
				remind_dispatch,audit_fail,audit_success,user_code_reset,state,Createtime) 
				select ?,?,?,?,?,?,?,?,1,now() from dual where not exists 
				(select * from sm_message_config where user_id=?) " ;
		Db::query($sql,[$this->uid,$initVal,$initVal,$initVal,$initVal,$initVal,
				$initVal,$initVal,$this->uid]) ;
	}
	
	public function getConfig() {
		return Db::table($this->table)->where('user_id',$this->uid)->find();
	}
	
	public function saveConfig ($config) {
		$this->save($config, ['user_id'=>$this->uid]) ;
	}
	
	
}