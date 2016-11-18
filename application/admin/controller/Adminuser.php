<?php
/*
 * Adminuser.php
 * 
 * Copyright Sichuan Great Wall Software Technology Co.,LTD. All Rights Reserved.
 * Author sakura 2016年7月7日上午9:53:37
 */
//////////////////////////////////////////////////////
namespace app\admin\controller;

use app\admin\model\AdminUserModel;
use app\admin\model\TempleModel;
use app\admin\model\RoleModel;

class Adminuser extends AdminController{
	
	private $adminUserModel ,$roleModel ;
	
	public function _initialize() {
		$this->adminUserModel = new AdminUserModel();
		$this->roleModel = new RoleModel();
		parent::_initialize();
	}
	/**
	 * 查询用户类别
	 * Author sakura 2016年7月8日下午3:25:00
	 */
	public function showlist(){
		$currentPage = $this->request->get('page',1);
		$param['type'] = $this->request->get('type','');
		$param['uname'] = $this->request->get('uname','');
		$param['mobile'] = $this->request->get('mobile','');
		
		//非超级用户只能管理自己创建的用户
		if($this->isSuperAdmin())$whereArr['inviterId'] = $this->getSessionUid();
		
		$page = $this->adminUserModel->getUserList($currentPage,$param);
		$arr = [];
		$arr['types'] = $this->adminUserModel->typeArray;
		$this->assign([
				'param' => $param,
				'page' => $page,
		]) ;
		return $this->fetch('list');
	}
	/**
	 * 添加用户
	 * 角色只显示操作用户有权限的角色
	 * Author sakura 2016年7月8日下午3:25:15
	 */
	public function add(){
		$user = ['approved'=>1];
		return $this->form($user);
	}
	/**
	 * 修改用户
	 * Author sakura 2016年7月12日上午9:45:30
	 */
	public function edit(){
		$id = $this->request->get('id',0);
		$user = $this->adminUserModel->getUserById($id);
		unset($user['password']);
		unset($user['passwordSalt']);
		return $this->form($user);
	}
	private function form($user=[]){
		$user['types'] = [];
		$roles = $this->roleModel->getRoles(/* $this->getSessionUid() */);
		$userRoleIds = '';
		if(iseta($user,'userId')){
			$rolesUser = $this->roleModel->getRoles($user['userId']);
			$userRoleIds = ',';
			foreach ($rolesUser as $k=>$v){
				$userRoleIds .= $v['roleId'].',';
			}
// 			$templesUser = $this->templeModel->getTemples($user['userId']);
// 			$userTempleIds = ',';
// 			foreach ($templesUser as $k=>$v){
// 				$userTempleIds .= $v['id'].',';
// 			}
		}
		$user['roleIds'] = $userRoleIds;
		
		$this->assign([
				'user' => $user,
				'types' => $this->adminUserModel->typeArray,
				'roles' => $roles,
		]) ;
		return $this->fetch('form');
	}
	public function checkOne(){
		$id = $this->request->post('id',0);
		$userName=$this->request->post('userName/s');
		$email=$this->request->post('email/s');
		$mobile=$this->request->post('mobile/s');
		$iccard=$this->request->post('iccard/s');
		if($userName){
			$re=$this->adminUserModel->checkOne(array('userName'=>$userName),$id);
			if(!$re){
				return getJsonStr(500,'用户名已存在！');
			}
		}
		if($email){
			$re=$this->adminUserModel->checkOne(array('email'=>$email),$id);
			if(!$re){
				return getJsonStr(500,'邮箱已存在！');
			}
		}
		if($mobile){
			$re=$this->adminUserModel->checkOne(array('mobile'=>$mobile),$id);
			if(!$re){
				return getJsonStr(500,'手机号已存在！');
			}
		}
		if($iccard){
			$re=$this->adminUserModel->checkOne(array('iccard'=>$iccard),$id);
			if(!$re){
				return getJsonStr(500,'IC卡号已存在！');
			}
		}
	}
	/**
	 * 保存提交
	 * POST
	 * Author sakura 2016年7月11日上午10:40:52
	 */
	public function submitAjax(){
		$id = $this->request->post('id',0);
		$user = array(
				'userName' => $this->request->post('userName'),
				'realName' => $this->request->post('realName'),
				'email' => $this->request->post('email'),
				'mobile' => $this->request->post('mobile'),
				'approved' => $this->request->post('approved'),
				'comment' => $this->request->post('comment'),
		);
		if($user['userName']){
		    if($id){
		        
			$re=$this->adminUserModel->checkParamUpdate(array('userName'=>$user['userName'],'userId'=>$id));
		    }else{
		        
			$re=$this->adminUserModel->checkParam(array('userName'=>$user['userName']));
		    }
			if(!$re){return getJsonStr(500,'用户名已存在！');}
		}
		if($user['email']){
		    if($id){
			$re=$this->adminUserModel->checkParamUpdate(array('email'=>$user['email'],'userId'=>$id));
		    }else{
			$re=$this->adminUserModel->checkParam(array('email'=>$user['email']));
		    }
			
			if(!$re){return getJsonStr(500,'邮箱已存在！');}
		}
		if($user['mobile']){
		    
		    if($id){
			 $re=$this->adminUserModel->checkParamUpdate(array('mobile'=>$user['mobile'],'userId'=>$id));
		    }else{
			 $re=$this->adminUserModel->checkParam(array('mobile'=>$user['mobile']));
		    }
			
			if(!$re){return getJsonStr(500,'手机号已存在！');}
		}
		
		$password = $this->request->post('password');
		$roleIds = $this->request->post('roleIds');
		
		if(iset($password)){
		//	$user['passwordSalt'] = getRundomSaltFigure(10);//密盐
			
// 			import('Servlet', EXTEND_PATH, '.class.php');
// 			$Servlet = new \Servlet(config('java_servlet'));
			
// 			$val = $Servlet->getPasswordSys($password,$user['passwordSalt']);
// 			if($val['resCode'] > 0){
// 				return getJsonStrError($val['resInfo']);
// 			}
			$user['password'] = sp_password($password);
		}
		
		$uprow = 0;
		if($id){
			$uprow = $this->adminUserModel->updateDataById($id, $user);
		$this->addManageLog('管理员', '对ID为'.$id.'的管理员进行了修改');
		}else{
			$user['inviterId'] = $this->getSessionUid();
			$user['createIp'] = getIp();
			$uprow = $this->adminUserModel->save($user);
			$id = $uprow;
		$this->addManageLog('管理员', '增加了名为'.$user['userName'].'的管理员');
		}
		$this->adminUserModel->changeUserRoles($id,$roleIds);
// 		$this->adminUserModel->changeUserTemples($id,$templeIds);
		
		/* if($uprow > 0){
			return getJsonStrSuc('提交成功');
		}
		return getJsonStrError('提交失败'); */
		/* unset($user['password']);
		unset($user['passwordSalt']); */
	
		return getJsonStrSuc('提交成功');
	}
	/**
	 * 
	 * Author sakura 2016年7月11日下午6:35:42
	 */
	public function info(){
		$id = $this->request->get('id',0);
		$user = $this->adminUserModel->getUserById($id);
		//
		$types = $this->adminUserModel->typeArray;
		$roles = $this->roleModel->getRoles($user['userId']);
		//
		$this->assign([
				'user' => $user,
				'types' => $types,
				'roles' => $roles,
		]) ;
		return $this->fetch('info');
	}
	
	/**
	 * 禁用/启用
	 * Author sakura 2016年7月11日下午5:58:23
	 */
	public function changeAjax(){
		$id = $this->request->get('id',0);
		$st = $this->request->get('st',0);//默认为禁用
		if(!$id){
			return getJsonStrError('信息未选择');
		}
		
		$uprow = $this->adminUserModel->updateDataById($id, array('approved'=>$st>0?1:0));
		if($uprow > 0){
		    if($st){
		        
		    $this->addManageLog('管理员', '对ID为'.$id.'的管理员进行了启用');
		    }else{
		        
		    $this->addManageLog('管理员', '对ID为'.$id.'的管理员进行了禁用');
		    }
			return getJsonStrSuc('更改成功');
		}
		return getJsonStrError('更改失败');
	}
	/**
	 * 删除用户--改变state状态即可
	 * Author sakura 2016年7月8日下午3:28:21
	 */
	public function delAjax(){
		$id = $this->request->get('id',0);
		if(!$id){
			return getJsonStrError('信息未选择');
		}
		$uprow = $this->adminUserModel->deleteById($id);
		if($uprow > 0){
		    $this->addManageLog('管理员', '删除了ID为'.$id.'的管理员');
			return getJsonStrSuc('删除成功');
		}
		return getJsonStrError('删除失败');
	}
	
	/**
	 * 用户解锁
	 * Author sakura 2016年8月5日上午10:19:50
	 */
	public function changeLockAjax(){
		$id = $this->request->get('id',0);
		$st = $this->request->get('st',0);//默认为解锁
		if(!$id){
			return getJsonStrError('信息未选择');
		}
		
		$errDate = [];
		if($st > 0){//锁定
			$errDate['is_lock'] = 1;
			$errDate['lockDate'] = getDate_();
			$errDate['error_count'] = 0;
			$this->addManageLog('管理员', '对ID为'.$id.'的管理员进行了锁定');
		}else{//解锁
			$errDate['is_lock'] = 0;
			$errDate['lockDate'] = null;
			$errDate['error_count'] = 0;
		    $this->addManageLog('管理员', '对ID为'.$id.'的管理员进行了解锁');
		}
		
		$uprow = $this->adminUserModel->updateDataById($id, $errDate);
		if($uprow > 0){
			return getJsonStrSuc('更改成功');
		}
		return getJsonStrError('更改失败');
	}
}