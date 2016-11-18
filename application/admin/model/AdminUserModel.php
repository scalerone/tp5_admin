<?php
/*
 * AdminUserModel.php
 * 
 * Copyright Sichuan Great Wall Software Technology Co.,LTD. All Rights Reserved.
 * Author sakura 2016年7月5日下午4:22:17
 */
//////////////////////////////////////////////////////
namespace app\admin\model;

use think\Model;
use think\db\Query;
use \think\Db;
class AdminUserModel extends AdminModel {
	
	protected $table = 'admin_user' ;
	protected $pk = 'userId' ;
	protected $createTime = 'Createtime' ;
	protected $updateTime = false ;
	protected $status = 'state' ;
	
// 	const TYPE_CASHIER 				= 'Cashier' ;
// 	const TYPE_MANAGER  			= 'Manager' ;
// 	const TYPE_ADMIN  		    	= 'Admin' ;
	
	const USER_LOCK 				= 1;
	const USER_NOT_LOCK				= 0;
	
// 	public $typeArray = [
// 			self::TYPE_CASHIER		=>'收银',//收银（现场收现供灯）
// 			self::TYPE_MANAGER		=>'管理者',//管理者（现场收现供灯、一体机维护、后台维护）
// 			self::TYPE_ADMIN		=>'超级用户',//超级用户（一体机维护、后台维护）
// 	] ;

	//不可登陆管理后台的用户类型
//	public $cannotLogins = [null,'',self::TYPE_CASHIER];
	
	/**
	 * 通过用户名获取用户信息
	 * @param unknown $name
	 * Author sakura 2016年7月6日下午4:33:17
	 */
	public function getUserByName($name){
		$user = $this->get(['userName'=>$name,$this->status=>self::DEFAULT_STATUS_NORMAL]);
		if($user){
			return $user->toArray();
		}
		return null;
	}
	/**
	 * 
	 * @param unknown $id
	 * @return \think\db\false|PDOStatement|string|\think\Model
	 * Author sakura 2016年7月11日下午8:27:26
	 */
	public function getUserById($id){
		$user = $this->db($this->table)->alias('a')
			->field('a.*')
			->where('a.state=1 and a.userId='.$id)
			->find();
		return $user;
	}
	/**
	 * 获取用户有效权限
	 * @param unknown $uid
	 * @return NULL
	 * Author sakura 2016年7月6日下午4:33:32
	 */
	public function getUserMenus($uid=0){
		$menus = [];
		if($uid){
			$menus = db('core_menu')->alias('a')
				->field('a.*,e.showName parentShowName,f.showName parentShowName1')
				->join('core_menu_role b','b.menuId=a.menuId ','left')
				->join('admin_user_role c','c.roleId=b.roleId','left')
				->join('admin_role d','d.roleId=c.roleId and d.state=1','left')
				->join('core_menu e','e.menuId=a.parentId','left')
				->join('core_menu f','f.menuId=e.parentId','left')
				->where('a.state=1 and c.userId='.$uid)
				->order(['a.parentId'=>'asc','a.leftShow'=>'desc','a.sort'=>'desc'])
				->select();
		}else{
			$menus = db('core_menu')->alias('a')
				->field('a.*,e.showName parentShowName,f.showName parentShowName1')
				->join('core_menu e','e.menuId=a.parentId','left')
				->join('core_menu f','f.menuId=e.parentId','left')
				->where('a.state=1')
				->order(['a.parentId'=>'asc','a.sort'=>'desc'])
				->select();
		}
		return $menus;
	}
	/**
	 * 获取用户列表
	 * Author sakura 2016年7月7日下午3:24:56
	 */
	public function getUserList($page,$whereArr=[]){
		$query = new Query();
		$query->table($this->table)->alias('a')
			->field('a.*,b.userName inviterUserName')
			->join('admin_user b','b.userId=a.inviterId','left')
			->where('a.state','=',self::DEFAULT_STATUS_NORMAL);
			if($whereArr){
				if(iseta($whereArr,'type'))
					$query -> where('a.accountType','=',$whereArr['type']);
				if(iseta($whereArr,'uname'))
					$query -> where('a.userName','like',"%{$whereArr['uname']}%");
				if(iseta($whereArr,'mobile'))
					$query -> where('a.mobile','like',"%{$whereArr['mobile']}%");
				if(iseta($whereArr,'userId'))
					$query -> where('a.inviterId','=',$whereArr['inviterId']);
				
			}
			$query->order(['a.Createtime'=>'desc']);
		return $this->getPaginationByQuery($query,$page);
		
		
	}
	/**
	 * 记录最后登录信息
	 * @param unknown $user
	 * Author sakura 2016年7月8日下午6:03:47
	 */
	public function updateLogin($user){
		$data=array(
				'loginCount'=>++$user['loginCount'],
				'lastLoginIp'=>getIp(),
				'lastLoginDate'=>getDate_(),
		);
		return $this->updateDataById($user[$this->pk], $data);
	}
	/**
	 * 用户错误登陆--锁定
	 * @param unknown $user
	 * Author sakura 2016年8月5日上午10:14:31
	 */
	public function updateLoginErr($user){
		$data=array(
				'error_count'=>$user['error_count'],
				'lockDate'=>getDate_(),
		);
		if(isetn($data['error_count'])>=5){
			$data['is_lock'] = 1;
		}
		return $this->updateDataById($user[$this->pk], $data);
	}
	/**
	 * 更新管理角色
	 * 先物理删除后添
	 * @param unknown $uid
	 * @param string $roleIds
	 * @return number
	 * Author sakura 2016年7月12日下午1:55:16
	 */
	public function changeUserRoles($uid,$roleIds=''){
		if($uid && $roleIds){
			db('admin_user_role')->where('userId',$uid)->delete();
			if(iset($roleIds)){
				$roleIdArr = explode(',', $roleIds);
				if($roleIdArr){
					foreach ($roleIdArr as $v){
						if(iset($v)){
							$data[] = array('userId'=>$uid,'roleId'=>$v);
						}
					}
					if($data){
						db('admin_user_role')->insertAll($data);
					}
				}
			}
			return 1;
		}else{
			return 0;
		}
	}

	/**
	 * 验证参数是否重复添加
	 * @param array $data
	 * @return boolean
	 */
	public function checkOne($data,$id) {
		$data['state']=1;
		//检查是否重复添加
		if($id!=0){
			$find=Db::table('admin_user')->where($data)->where('userId','<>',$id)->find();
		}else{
			$find=$this->where($data)->find();
		}
		if ($find) {
			return false;
		}
		return true;
	}
}