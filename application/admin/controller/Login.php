<?php
/*
 * Login.php
 * 
 * Copyright Sichuan Great Wall Software Technology Co.,LTD. All Rights Reserved.
 * Author sakura 2016年7月5日下午3:23:11
 */

namespace app\admin\controller ;

use app\admin\model\AdminUserModel;
use app\admin\model\MenuModel;
use app\admin\model\RoleModel;
use think\Cache;
class Login extends AdminController{
	
	private $adminUserModel , $roleModel , $menuModel ;
		
	public function _initialize() {
		$this->adminUserModel = new AdminUserModel();
		$this->roleModel = new RoleModel();
		$this->menuModel = new MenuModel();
		parent::_initialize();
	}
	
	public function login(){
		return $this->fetch('/login');
	}
	/**
	 * 后台登陆提交
	 * @param string $name
	 * @param string $password
	 * Author sakura 2016年7月6日下午4:08:22
	 */
	public function loginAjax(){
		$name = $this->request->get('name');
		$password = $this->request->get('password');

		if(!iset($name)){
			return getJsonStr(511,'用户名不能为空');
		}
		if(!iset($password)){
			return getJsonStr(511,'密码不能为空');
		}
		//连续5次登陆判断
		$errorSes = session(SESSION_ADMIN_LOGIN_ERROR);
		if($errorSes){
			if(iset($errorSes['err_count'])>5){
				return getJsonStr(511,'用户已锁定');
			}
		}
		$user = $this->adminUserModel->getUserByName($name);
				
		if(!$user){
			return $this->_loginMsg(null,'用户错误');
		}
		if(isetn($user['error_count'])>=5){
			return $this->_loginMsg(null,'用户已锁定');
		}
		

		if(!sp_compare_password($password,$user['password'])){
			return $this->_loginMsg($user,'用户与密码不符');
		}
		if(1 != $user['approved']){
			return $this->_loginMsg($user,'用户未启用');
		}
		

		$this->adminUserModel->updateLogin($user);
		unset($user['password']);
		unset($user['passwordSalt']);
		//查看此角色--如果是超级管理员，就显示所有菜单列
		//先判断用户是否拥有超级管理员角色，如果有，就获取所有
		$user['isSuperAdmin'] = 0;
		$roles = $this->roleModel->getRoles($user['userId']);
		if($roles){
			foreach ($roles as $k=>$v){
				if(1===isetn($v['roleId'])){
					$user['isSuperAdmin'] = 1;
					break;
				}
			}
		}
		//获取菜单权限
		if(isetn($user['isSuperAdmin'])){
			$user['menus'] = $this->adminUserModel->getUserMenus();
			
		}else{
			$user['menus'] = $this->adminUserModel->getUserMenus($user['userId']);

		}
		
		session(SESSION_ADMIN_LOGIN_ERROR,null);
		//这里来查权限---权限保存在session中，
		session(SESSION_ADMIN_USER,$user);
		//设置权限菜单SESSION+排序
		$this->getPopedomMenus();
		$this->addManageLog('登录操作', '登录成功');
		return $this->_loginMsg(null,'登陆成功',200);
	}
	/**
	 * 总览
	 * Author sakura 2016年7月7日下午3:44:10
	 */
	public function index(){
		return $this->fetch('/index');
	}
	/**
	 * 退出登录
	 * Author sakura 2016年7月7日下午3:44:23
	 */
	public function loginout(){
		session(SESSION_ADMIN_USER,null);
		session(SESSION_ADMIN_USER_MENUS,null);
		session(null);
		return $this->fetch('/login');
	}
	//////////////////////////////////////////////////////
	/**
	 * 登陆操作消息
	 * @param string $msg
	 * @param number $code
	 * Author sakura 2016年7月8日上午9:52:41
	 */
	private function _loginMsg($user,$msg='登陆错误',$code=511){
		if($code != 200){
			$errorSes = session(SESSION_ADMIN_LOGIN_ERROR);
			if($errorSes){
				$errorSes['err_count'] ++; 
			}else{
				$errorSes = array('err_count'=>1);
			}
			
			if($user){
				$user['error_count']=isetn($errorSes['err_count']);
				//记录用户错误登陆---锁定用户
				$this->adminUserModel->updateLoginErr($user);
			}
			session(SESSION_ADMIN_LOGIN_ERROR,$errorSes);
		}
		return getJsonStr($code,$msg);
	}
	/**
	 * 清除缓存
	 */
	public function clearcache(){
	    Cache::clear();
	    return $this->success('清理缓存成功！');
	}
}