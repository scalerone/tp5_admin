<?php
namespace app\console\controller ;

use think\Controller;
/**
 * 命令行
 * @author Chenjx
 *
 */
class ConsoleController extends Controller {
	
	protected $app;
	
	protected $beforeActionList = ['cliFilter'] ;
	
	public function cliFilter() {
		if (!IS_CLI && !config('app_debug')) {
			$domain = $this->request->domain();//域名+http://
			$pathInfo = 'g'.$this->request->path();//访问路径无域名
			$domain = stristr($domain, '.');
			return $this->redirect('http://www'.$domain);//跳转至www.
		}
	}
	
	public function _initialize() {
		set_time_limit(0);
	}
	

	
	
	
	
	
	
}