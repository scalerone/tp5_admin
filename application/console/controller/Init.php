<?php
namespace app\console\controller ;

use app\admin\model\ConfigureModel;

class Init extends ConsoleController {
	
	/**
	 * php app_root/public/index.php console/init
	 */	
	public function index () {
		$this->initCache() ;
	}
	
	public function initCache() {
		$cache = new ConfigureModel() ;
		$cache->saveCache() ;
	}
	
}