<?php
/*
 * BaseController.php
 *
 * Copyright Sichuan Great Wall Software Technology Co.,LTD. All Rights Reserved.
 * Author sakura 2016年8月4日上午10:36:02
 */
//////////////////////////////////////////////////////
namespace app\common\controller;

use think\Controller;
use think\Request;

class BaseController extends Controller{

	/**
	 * 前置操作控制器或方法列表
	 * @var array $beforeList
	 * @access protected
	 */
	protected $beforeList = [];

	public function __construct(Request $request = null) {
		parent::__construct($request);

		// 前置操作方法
		if ($this->beforeList) {
			foreach ($this->beforeList as $method => $options) {
				is_numeric($method) ?
				$this->beforeTo($options) :
				$this->beforeTo($method, $options);
			}
		}
	}

	/**
	 * 前置操作
	 * @access protected
	 * @param string    $method     前置操作方法名
	 * @param array     $options    调用参数 ['only'=>[...]] 或者['except'=>[...]]
	 */
	protected function beforeTo($method, $options = [])
	{
		$b = false;
		$c = '*'.strtolower_($this->request->controller())."*";
		$a = strtolower_($this->request->action());
		if (isset($options['only'])) {//只过滤XX
			if (is_string($options['only'])) {
				$v = $options['only'];
				$v = str_replace("\r", "",$v);
				$v = str_replace("\n", "",$v);
				$v = str_replace("\t", "",$v);
				$v = str_replace(" ", "",$v);
				$v = trim($v);
				$only = explode(',', $v);
			}
			foreach ($only as $v){
				if(!iset($v))continue;
				$v = strtolower_(trim($v));
				if(strpos_($v, '*')){
					$v = "*".$v;
					if($v == $c){
						$b = true;
						break;
					}
				}else{
					if($v==$a){
						$b = true;
						break;
					}
				}
			}
			if(!$b){
				return;
			}
		} elseif (isset($options['except'])) {//不过滤XX
			if (is_string($options['except'])) {
				$v = $options['except'];
				$v = str_replace("\r", "",$v);
				$v = str_replace("\n", "",$v);
				$v = str_replace("\t", "",$v);
				$v = str_replace(" ", "",$v);
				$v = trim($v);
				$except = explode(',', $v);
			}
			foreach ($except as $v){
				if(!$v)continue;
				$v = strtolower_(trim($v));
				if(strpos_($v, '*')){
					$v = "*".$v;
					if($v == $c){
						$b = true;
						break;
					}
				}else{
					if($v == $a){
						$b = true;
						break;
					}
				}
			}
			if($b){
				return;
			}
		}

		if (method_exists($this, $method)) {
			call_user_func([$this, $method]);
		}
	}

}
