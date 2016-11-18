<?php
namespace app\admin\model ;
use \think\db\Query;
class AdminLogModel extends AdminModel {
	
	protected $table 				= 'admin_log' ;
	protected $pk 					= 'id' ;
	protected $createTime 			= 'ctime' ;
	protected $updateTime 			= false ;
	protected $status	  			= 'state' ;
	
	
	public function getPage ($currentPage,$param=[]) {
	    $where = [] ;
	  
		if(iseta($param,'uname')){
		    $where['b.userName'] =['like','%'.trim($param['uname']).'%']; 
		}
	    
	    $query = $this->getMQuery($where) ;
	    return $this->getPaginationByQuery($query, $currentPage) ;
	}
	
	
	public function getById ($id) {
	    return $this->getMQuery(['a.id'=>$id])->find() ;
	}
	
	private function getMQuery ($where = []) {
	    $query = new Query() ;
	    $where['a.state'] = self::DEFAULT_STATUS_NORMAL;

	    $query->table($this->table)->alias('a')
	    ->field('a.*,b.userName uname')
	    ->join('admin_user b','b.userId = a.admin_id','left')
	    ->where($where) 
	    ->order(['a.Createtime'=>'desc']);
	    return $query ;
	}
	
	
	
	
}