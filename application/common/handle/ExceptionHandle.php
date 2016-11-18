<?php
namespace app\common\handle ;

use think\exception\Handle;
use Exception;
use think\App;
use think\exception\ClassNotFoundException;
use think\Response;
use think\response\Redirect;

class ExceptionHandle extends Handle {
	
	public function render (Exception $e) {
		if ($e instanceof ClassNotFoundException) {
			$response = new Redirect('/index/404');
			$response->code(404)->params([]);
			return $response ;
		} 
		return parent::render($e) ;
	}
	
}