<?php
namespace app\admin\controller ;
use \app\admin\model\ConfigureModel ;

class Configure extends AdminController {
	private $configureModel ;

	public function _initialize() {
		$this->configureModel = new ConfigureModel();
		parent::_initialize();
	}
	
	public function updateCache() {
		if ($this->configureModel->saveCache()) {
			return getJsonStrSucNoMsg() ;
		}
		return getJsonStrError() ;
	}

    public function listPage() {

    	$currentPage = $this->request->get('page',1) ;
    	$code = $this->request->get('code','','htmlspecialchars') ;
    	$page = $this->configureModel->getPageList($code,$currentPage) ;
    	$this->assign('page',$page) ;
    	$this->assign('code',$code) ;
    	return $this->fetch("index") ;
    }

    public function edit() {

          if($this->request->isPost()){
            $data['id'] =  $this->request->post('id/d','','htmlspecialchars');
            $data['code'] =  $this->request->post('code/s','','htmlspecialchars');
            $data['value'] =  $this->request->post('value/s','','htmlspecialchars');
            if(!$data['id']){return getJsonStr(500,'参数有误！');}
            $result = $this->configureModel->where('id',$data['id'])->update($data);
            if ($result) {
                cache($data['code'],$data['value']);
                $this->addManageLog('系统参数设置', '编辑ID为'.$data['id'].'系统参数');
                return getJsonStrSuc('编辑成功');
            } else {
                   return getJsonStr(500,'编辑失败');
            }


        }else{
            $id =  $this->request->get('id/d','','htmlspecialchars');

            $configure = $this->configureModel->get(['id'=>$id]);

            $this->assign('data',$configure) ;
            return $this->fetch() ;

        }

    }

}
