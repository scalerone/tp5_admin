<?php
namespace app\admin\model ;

use think\Model;

class RoleModel extends AdminModel {
	
	protected $table = 'admin_role' ;
	protected $pk = 'roleId' ;
	protected $createTime = false ;
	protected $updateTime = 'Createtime' ;
	protected $status	  = 'state' ;
	protected $insert = ['state'=>1];
	
	
	// status查询
	public function scopeStatus($query)
	{
	    $query->where('state', 1);
	}
	public  function  getList(){
	    
	    $list = $this->all(function($query){
	        $query->where('state', 1)->order('roleId', 'desc');
	    });
	    return $list;
	}
	
	/**
	 * 
	 * Author sakura 2016年7月11日下午5:48:24
	 */
	public function getRoles($uid=0){
		if($uid){
			$list = $this->db($this->table)->alias("a")
				->field('a.*')
				->join('admin_user_role b','b.roleId=a.roleId','left')
				->where('a.state=1 and b.userId='.$uid)
				->select();
			return $list;
		}else{
			$list = $this->db($this->table)->where($this->status,self::DEFAULT_STATUS_NORMAL)
				->select();
		}
		return $list;
	}

	/**
	 * 验证参数是否重复添加
	 * @param array $data
	 * @return boolean
	 */
	public function checkParam($data) {
		//检查是否重复添加
		$find = $this->where($data)->find();
		if ($find) {
			return false;
		}
		return true;
	}
}