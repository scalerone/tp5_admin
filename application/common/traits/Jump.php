<?php
namespace app\common\traits ;

use think\Config;
use think\exception\HttpResponseException;
use think\Response;
use think\View as ViewTemplate;

trait Jump {
	
	protected function error($msg = '', $url = null, $data = '', $wait = 3) {
		$code = 0 ;
		if (is_numeric($msg)) {
			$code = $msg;
			$msg  = '';
		}
		if ($data && isset($data['code']) && $this->request->isAjax()) {
			$code = $data['code'] ;
			unset($data['code']) ;
		}
		$result = [
				'code' => $code,
				'msg'  => $msg,
				'data' => $data,
				'url'  => is_null($url) ? 'javascript:history.back(-1);' : $url,
				'wait' => $wait,
		];
		$type = $this->getResponseType();
		if ('html' == strtolower($type)) {
			$result = ViewTemplate::instance(Config::get('template'), Config::get('view_replace_str'))
			->fetch(Config::get('dispatch_error_tmpl'), $result);
		}
		$response = Response::create($result, $type);
		throw new HttpResponseException($response);
	}
	
}