<?php
namespace app\admin\controller ;
use think\paginator\driver\Bootstrap;
use \app\common\model\SysconfigModel ;
use think\db\Query;

class Sysconfig extends AdminController {
	private $sysconfigModel ;
	
	public function _initialize() {
		$this->sysconfigModel = new SysconfigModel();
		parent::_initialize();
	}
	
	
	public function index($type='userReg'){
	    //$type 的类型，会员注册设置（userReg）、会员认证设置（companyAuth）、项目申请设置 （projApply）、订单创建设置（orderCreate）
	
	    switch ($type) {
	        case 'userReg':
	            return $this->userlist($type);
	            break;
	        case 'companyAuth':
	            return $this->companylist($type);
	            break;
	        case 'projApply':
	             return $this->projlist($type);
	            break;
	        case 'orderCreate':
	            return $this->orderlist($type);
	            break;
	             
	    }
	
	}
	////////////////////////////////会员注册设置///////////////////////////////////////////
	
	
	public function userlist($type){
	    $map['Vc_table']='sm_user';

	    $params=$this->sysconfigModel->getDataArr($map);
// 	    echo $this->sysconfigModel->getLastSql();
// 	    dump($params);die;
	    $this->assign([
	        'type'=>$type,
	        'params'=>$params,
	    ]) ;
	    return $this->fetch('userlist');
	    
	}
	public function subUserAjax(){

	    if($this->request->isPost()){
	        
	      $showArr =input('show/a','');
	      $requireArr=input('require/a','');
	 
	      if(empty($requireArr)){
	          return getJsonStr(500,'至少选择一项为必填项');
	      }
	       
	      $showArr = array_merge($showArr,$requireArr);
	      $da = array();
	      if(!empty($showArr)){
	          $da['Vc_showfields']=join(",", $showArr);
	      }
	      if(!empty($requireArr)){
	          $da['Vc_requirefields']=join(",", $requireArr);
	      }
	      $map['Vc_table']='sm_user';
	      $result=$this->sysconfigModel->update($da,$map);
	      	//    echo $this->sysconfigModel->getLastSql();die;

	       if ($result) {
                $this->addManageLog('信息项设置', '进行了会员注册设置');
                return getJsonStrSuc('更新成功');
            } else {
                   return getJsonStr(500,'更新失败');
            }
	      
	    }
	        
	    
	}
	////////////////////////////////会员认证设置///////////////////////////////////////////
	
	
	public function companylist($type){
	    $map['Vc_table']='sm_company';
	
	    $params=$this->sysconfigModel->getDataArr($map);
	  
	    $this->assign([
	        'type'=>$type,
	        'params'=>$params,
	    ]) ;
	    return $this->fetch('companylist');
	     
	}
	
	
	public function subCompanyAjax(){
	    if($this->request->isPost()){
	         
	        $showArr =input('show/a','');
	        $requireArr=input('require/a','');
	        if(empty($requireArr)){
	            
	            return getJsonStr(500,'至少选择一项为必填项');
	            
	        }
	        
	        $showArr = array_merge($showArr,$requireArr);
	        $da = array();
	        if(!empty($showArr)){
	            $da['Vc_showfields']=join(",", $showArr);
	        }
	        if(!empty($requireArr)){
	            $da['Vc_requirefields']=join(",", $requireArr);
	        }
	        $map['Vc_table']='sm_company';
	        $result=$this->sysconfigModel->update($da,$map);
	        //dump($da);
	
	        if ($result) {
	            $this->addManageLog('信息项设置', '进行了会员认证设置');
	            return getJsonStrSuc('更新成功');
	        } else {
	            return getJsonStr(500,'更新失败');
	        }
	         
	    }
	     
	     
	}
	
	
	////////////////////////////////项目申请设置///////////////////////////////////////////
	
	public function projlist($type){
	    $map['Vc_table']='se_project';
	
	    $params=$this->sysconfigModel->getDataArr($map);
	     
	    $this->assign([
	        'type'=>$type,
	        'params'=>$params,
	    ]) ;
	    return $this->fetch('projlist');
	
	}
	
	
	
	
	public function subProjAjax(){
	    if($this->request->isPost()){
	
	        $showArr =input('show/a','');
	        $requireArr=input('require/a','');
	        if(empty($requireArr)){
	            return getJsonStr(500,'至少选择一项为必填项');
	        }
	         
	        $showArr = array_merge($showArr,$requireArr);
	        $da = array();
	        if(!empty($showArr)){
	            $da['Vc_showfields']=join(",", $showArr);
	        }
	        if(!empty($requireArr)){
	            $da['Vc_requirefields']=join(",", $requireArr);
	        }
	        $map['Vc_table']='se_project';
	        $result=$this->sysconfigModel->update($da,$map);
	        if ($result) {
	            $this->addManageLog('信息项设置', '进行了项目申请设置');
	            return getJsonStrSuc('更新成功');
	        } else {
	            return getJsonStr(500,'更新失败');
	        }
	
	    }
	
	
	}
	
	
	
	
	
	
	
	
	////////////////////////////////订单创建设置///////////////////////////////////////////
	
	
	
	public function orderlist($type){
	    $map['Vc_table']='se_project_order';
	
	    $params=$this->sysconfigModel->getDataArr($map);
	
	    $this->assign([
	        'type'=>$type,
	        'params'=>$params,
	    ]) ;
	    return $this->fetch('orderlist');
	
	}
	
	
	
	
	public function subOrderAjax(){
	    if($this->request->isPost()){
	
	        $showArr =input('show/a','');
	        $requireArr=input('require/a','');
	        if(empty($requireArr)){
	            return getJsonStr(500,'至少选择一项为必填项');
	        }
	
	        $showArr = array_merge($showArr,$requireArr);
	        $da = array();
	        if(!empty($showArr)){
	            $da['Vc_showfields']=join(",", $showArr);
	        }
	        if(!empty($requireArr)){
	            $da['Vc_requirefields']=join(",", $requireArr);
	        }
	        $map['Vc_table']='se_project_order';
	        $result=$this->sysconfigModel->update($da,$map);
	        if ($result) {
	            $this->addManageLog('信息项设置', '进行了订单创建设置');
	            return getJsonStrSuc('更新成功');
	        } else {
	            return getJsonStr(500,'更新失败');
	        }
	
	    }
	
	
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
  
   
         
    
}
