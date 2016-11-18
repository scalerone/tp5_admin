<?php
namespace app\admin\controller ;

use think\Controller;
use think\Request;
use app\common\controller\BaseController;

class AdminController extends /* Controller */BaseController {
	
	public $currentUser , $request;
	
	
	//登陆后先检查权限
	protected $beforeList = [
			'adminUrlFiltered',
			//不检查权限的访问控制器，$this->request->path()
			'checkPopedom'=>
				['except' => 'login,loginajax,loginout,clearcache,updateCache,
							  FileUpload*,Upload*,Ueditor*,getConfig,saveConfig']
	       
	    
	];
	
	/**
	 * 只有地址带admin的才能进入
	 * Author sakura 2016年7月11日上午11:01:57
	 */
	public function adminUrlFiltered(){
		$domain = $this->request->domain();//域名+http://
		$pathInfo = 'g'.$this->request->path();//访问路径无域名
		if (stripos($domain,'admin.') === false/*  || !stripos($pathInfo,'admin/') === 1 */){
			$domain = stristr($domain, '.');
			return $this->redirect('http://www'.$domain);//跳转至www.
		}
	}
	/**
	 *
	 * 记录操作日志-弃用
	 */
  public function manage_log() {
	    
	        $controller = $this->request->controller();
	        if($controller == '' || $controller == 'static' || strtolower($controller) == 'fileupload'  || strtolower($controller) == 'ueditor') {
	            return false;
	        }else {
	            
	            if($this->request->isPost()||$this->request->isAjax()){
	                $da = array();
	                $da['login_ip'] = getIp();
	                $da['admin_id'] = $this->getSessionUid();
	                $da['ctime'] = date('Y-m-d H:i:s');
	                $da['querystring'] =  $this->request->path();
	                $da['controller'] = $controller;
	                $da['action'] = $this->request->action();
	                 
	                db('admin_log')->insert($da);
	            }
	           
	            
	        }
	    
	}
	
	/**
	 * 描述：增加操作日志
	 * 参数：$m-所属菜单 ,$c-操作内容,$id-操作者id可为空
	 * 返回：无
	 */
	function addManageLog($m, $c, $id = ''){
	    if ($id == ''){$id=$this->getSessionUid();}
	    if ($id != ''){
	       
	        $da = array();
	        $da['login_ip'] = getIp();
	        $da['admin_id'] = $id;
	        $da['Createtime'] = date('Y-m-d H:i:s');
	        $da['menu'] =  $m;
	        $da['content'] = $c;
	        db('admin_log')->insert($da);
	
	    }
	}
	
	/**
	 * 检查权限
	 * 通过用户所在角色，检查是否拥有此角色的菜单访问权限
	 * Author sakura 2016年7月5日下午2:20:08
	 */
	public function checkPopedom(){
		//不过滤+不判断登陆
// 		$this->view->assign('menu_now_ses',array('parentShowName'=>'一级菜单','showName'=>'二级菜单'));
// 		if(true){
// 			return '';
// 		}
		////////////
		$pathInfo = strtolower_($this->request->path());
		
		$user = session(SESSION_ADMIN_USER);
		if(!$user){
			//跳转到登陆界面
			return $this->redirect('/login');
		}
		
		//查询当前url是否包含在权限中
		if(!$user['menus']){
			//跳转到登陆界面
			return $this->redirect('/login');
		}
		
		$menu_now_ses = [];//当前菜单
		$hadPopedom = false;//是否有访问权限
		//总览非权限菜单
		if(strpos_($pathInfo,'login/index')){
			$hadPopedom = true;
		}
		/* //超级用户不检查菜单
		if($this->isSuperAdmin()){
			$hadPopedom = true;
		} */
		//
		if(!$hadPopedom){
			foreach ($user['menus'] as $k=>$v){
				if( 
						(iset($v['menuUrl']) && strpos_($pathInfo,strtolower_($v['menuUrl'])))
						 || 
						(iset($v['submitUrl']) && strpos_($pathInfo,strtolower_($v['submitUrl'])))
						){
					
					$hadPopedom = true;
					if(!$this->request->isAjax()){
						//保存当前访问的菜单
						if(iset($v['parentShowName1'])){
							$v['parentShowName'] = $v['parentShowName1'];
						}
						$menu_now_ses = $v;
					}
					break;
				}
			}
		}
		if(!$hadPopedom){
			//跳转到首页
			if($this->request->isAjax()){
				return getJsonStrError('超出权限操作',599,'/login/index');
			}else{
				//return $this->success('超出权限操作','/admin/login/index');
				return $this->redirect('/login/index');
			}
		}
		
		$this->view->assign('menu_now_ses',$menu_now_ses);
	}
	/**
	 * 获取权限菜单
	 * 这里不再做登陆判断
	 * Author sakura 2016年7月7日下午2:26:29
	 */
	public function getPopedomMenus(){
		$menus = session(SESSION_ADMIN_USER_MENUS);
		if(!$menus){
			//设置左边菜单~~~
			$user = session(SESSION_ADMIN_USER);
			$menus = array();
			//var_dump($user);
			if($user && $user['menus']){
				//设置一级菜单
				foreach ($user['menus'] as $k=>$v){
					if($v['parentId']==0 && $v['leftShow']==1){
						if(null == $v['menuIcon'] || '' == $v['menuIcon']){
							$v['menuIcon'] = 'bars';//iset($v['menuIcon'],'bars');
						}
						$v['leftRouters'] = '';
						$menus[$v['menuId']] = $v;
						unset($user['menus'][$k]);//清除一级
					}
				}
				if($menus){
					//设置二级菜单
					foreach ($user['menus'] as $k=>$v){
						if($v['parentId']>0 && iseta($menus, $v['parentId'])){
							if($v['leftShow']==1){//在left.html判断
								$v['leftRouters'] = '';
								$menus[$v['parentId']]['leftMenus'][$v['menuId']] = $v;
								$menus[$v['parentId']]['rightMenus'][$v['menuId']] = $v;
								unset($user['menus'][$k]);//清除二级
							}
							//$menus[$v['parentId']]['rightMenus'][$v['menuId']] = $v;//将当前值放入右侧
						}
					}
					
					////////////
					foreach ($menus as $k=>$v){//一级
						if(iseta($v,'leftMenus')){
							foreach ($v['leftMenus'] as $k_left=>$v_left){//二级
								//设置三级菜单
								foreach ($user['menus'] as $k_r=>$v_r){
									if(iset($v_r['leftRouters']) && strpos_(','.$v_r['leftRouters'].',',','.$k_left.',')){
										$menus[$k]['rightMenus'][$v_r['menuId']] = $v_r;
									}
								}
								//设置高亮
								$leftRouters = array();
								$leftRouters[] = '/'.$v_left['menuUrl'];
								if(iseta($menus[$k],'rightMenus')){
									$right = $menus[$k]['rightMenus'];
									foreach ($right as $k_right=>$v_right){
										if(strpos_(','.$v_right['leftRouters'].',',','.$k_left.',')){
											$leftRouters[] = '/'.$v_right['menuUrl'];
										}
									}
									$menus[$k]['leftMenus'][$k_left]['dataRouters'] = implode(',', $leftRouters);
								}
							}
						}
					}
				}
			}
			session(SESSION_ADMIN_USER_MENUS,$menus);
			unset($user['menus']);
		}
	}
	
	public function _initialize() {
		$this->request = Request::instance() ;
		//$this->getPopedomMenus();
		if(!$this->request->isAjax()){
			//默认第一次取不到session,因为beforeAction在_initialize之后执行
			$user = session(SESSION_ADMIN_USER);
			$menus = session(SESSION_ADMIN_USER_MENUS);
// 			var_dump($menus);
			$this->assign('user_ses',$user);
			$this->assign('menus_ses',$menus);
			/* if($menus){
				foreach ($menus as $k=>$v){
					if(isset($v['leftMenus'])){
						var_dump($v['leftMenus']);
					}
				}
			} */ 
		}
	}
	
	/**
	 * 获取session用户
	 * @return user
	 * Author sakura 2016年7月18日下午4:20:28
	 */
	protected function getSessionUser(){
		return session(SESSION_ADMIN_USER);
	}
	/**
	 * 获取session用户ID
	 * @return uid
	 * Author sakura 2016年7月18日下午4:20:43
	 */
	protected function getSessionUid(){
		$user = $this->getSessionUser();
		return isetna($user,'userId');
	}
	/**
	 * 获取session用户名称
	 * Author sakura 2016年7月29日上午9:33:39
	 */
	protected function getSessionUname(){
		$user = $this->getSessionUser();
		return iseta($user,'userName');
	}
	/**
	 * 判断登陆者是否为超级用户
	 * @return boolean
	 * Author sakura 2016年7月18日下午4:23:24
	 */
	protected function isSuperAdmin(){
		$user = $this->getSessionUser();
		if(isetna($user,'isSuperAdmin',0) == 1){
			return true;
		}else{
			return false;
		}
	}
	
	
}