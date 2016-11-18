<?php
namespace app\admin\controller ;
use think\paginator\driver\Bootstrap;
use \app\admin\model\AdminLogModel ;
use think\db\Query;

class Adminlog extends AdminController {
	private $adminLogModel ;
	
	public function _initialize() {
		$this->adminLogModel = new AdminLogModel();
		parent::_initialize();
	}
	
    public function listPage($page=1) {
       
    	$param['uname'] = input('uname/s','');
    	$pageData = $this->adminLogModel->getPage($page,$param);
    	
    	
    	$this->assign([
				'param' => $param,
				'pageData' => $pageData,
		]) ;
		return $this->fetch('index');

    }
   
   
    
}
