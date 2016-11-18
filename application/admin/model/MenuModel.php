<?php
namespace app\admin\model ;
use \think\db\Query;
use think\Model;

class MenuModel extends AdminModel {
	
	protected $table = 'core_menu' ;
	protected $pk = 'menuId' ;
	protected $createTime = 'Createtime' ;
	protected $updateTime = false ;
	protected $status	  = 'state' ;
	
	
	public function getList() {
// 	    return $this->all([$this->status=>1]) ;
	    return $this->all(function($query){
    $query->where('state', 1)->order('sort', 'desc');
}) ;
	}
	/**
	 * 获取十条数据
	 */
	public function getPage($currentPage){
	    $query=new Query();
	    $query->table($this->table)
	    ->where('state',1)
	    ->order(['parentId'=>'asc','sort'=>'desc']);
	    return $this->getPaginationByQuery($query, $currentPage) ;
	}
	
	
	/**
	 * 判断是否是二级菜单
	 * @return \think\response\Json
	 */
	public function checkSubMenu ($pid) {
	     
	    $find = $this->where(array("menuId" => $pid))->value("parentId");
	    if ($find) {
	        $find2 = $this->where(array("menuId" => $find))->value("parentId");
	        if($find2==0){
	            return true;
	        }
	         
	    }
	
	    return false;
	
	}
	
	/**
	 * 验证菜单是否超出三级
	 * @param int $parentid
	 * @return boolean
	 */
	public function checkParentid($parentid) {
	    $find = $this->where(array("menuId" => $parentid))->value("parentId");
	    if ($find) {
	        $find2 = $this->where(array("menuId" => $find))->value("parentId");
	        if($find2){
	            $find3 = $this->where(array("menuId" => $find2))->value("parentid");
	            if ($find3==0) {
	                return false;
	            }
	        }
	       
	    }
	    return true;
	}
	/**
	 * 验证action是否重复添加
	 * @param array $data
	 * @return boolean
	 */
	public function checkAction($data) {
	    //检查是否重复添加
	    $find = $this->where($data)->find();
	    if ($find) {
	        return false;
	    }
	    return true;
	}
	/**
	 * 验证action是否重复添加
	 * @param array $data
	 * @return boolean
	 */
	public function checkActionUpdate($data) {
	    //检查是否重复添加
	    $id=$data['menuId'];
	    unset($data['menuId']);
	    $find = $this->field('menuId')->where($data)->find();
	    if (isset($find['menuId']) && $find['menuId']!=$id) {
	        return false;
	    }
	    return true;
	}
}