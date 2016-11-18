<?php
namespace app\common\validate ;

use think\Validate;

class User extends Validate {
	
	protected $rule = [
			'Vc_name'			=>		'max:20|min:2',
			'Vc_password'		=>		'require|max:20|min:2',
			'Vc_Email'			=>		'',
			'Dt_birthday'		=>		'',
			'Vc_address'		=>		'',
			'Vc_mobile'			=>		'require|max:20|min:2',
	] ;
	
	protected $message = [
			
	] ;
	
	protected $scene = [
			'reg'				=>		['Vc_password','Vc_mobile'],
	] ;
	
}