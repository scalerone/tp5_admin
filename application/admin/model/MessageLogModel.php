<?php
namespace app\admin\model ;

use think\db\Query;

class MessageLogModel extends AdminModel {
	
	protected $table 	= 'sm_short_message' ;
	protected $pk 		= 'id' ;
	
	public function getPage($page=1, $phone, $username, $type) {
		$where = [] ;
		if ($phone) {
			$where['Vc_phone'] = $phone ;
		}
		if ($username) {
// 			$where
		}
		if ($type) {
		}
		$query = new Query() ;
		$query->table($this->table)->order('id desc')->where($where);
		return $this->getPaginationByQuery($query, $page) ;
	}
	
	public function parseJson ($json) {
		$str = "" ;
		if ($json) {
			$obj = json_decode($json) ;
			if ($obj) {
				foreach ($obj as $k => $v) {
					$str .= "[$k:$v] " ;
				}
			}
		}
		return $str ;
	}
	
	public function getName ($tpl) {
		$arr = [
				'remind_check'			=>		'对账提醒',
				'remind_repay'			=>		'还款提醒',
				'remind_dispatch'		=>		'发货提醒',
				'remind_arrived'		=>		'到货提醒',
				'audit_fail'			=>		'审核未通过',
				'audit_success'			=>		'审核通过',
				'user_code_reset'		=>		'密码重置',
		] ;
		if (key_exists($tpl, $arr)) {
			return $arr[$tpl] ;
		}
		return '' ;
	}
	
}