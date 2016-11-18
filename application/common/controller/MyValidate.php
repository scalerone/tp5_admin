<?php
namespace app\common\controller ;

use think\Validate;

class MyValidate extends Validate {

	/**
	 * 验证最大字符长度
	 * @param string $str
	 * @param $maxLen
	 * @return bool
	 */
	public function strLenMax ($str='',$rule) {
		$length = mb_strlen($str,'UTF-8') ;
		return $length <= $rule;
	}


	/**
	 * @param string $str
	 * @param $rule
	 * @return bool
	 */
	public function phone ($value, $rule) {
		return (preg_match("/^0\d{2,3}-?\d{7,8}$/",$value)||preg_match("/^(\+86)?1[3|4|5|7|8]\d{9}$/",$value))==1?true:false;
	}
}