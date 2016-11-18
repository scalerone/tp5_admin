<?php
namespace app\common\model ;
use \think\db\Query;
use app\admin\model\AdminModel;
class SlidesModel extends AdminModel {
	
	protected $table = "sm_slides" ;
	protected $pk = 'id' ;
	protected $createTime = 'Createtime' ;
	protected $updateTime = 'Createtime' ;
	protected $status = 'state' ;
	
	const STATUS_HOME  			        = 1 ;
	const STATUS_MONEY  		    	= 2 ;
	const STATUS_GOODS  		    	= 3 ;
	const STATUS_CARS 				    = 4 ;


	public $statusArray = [
			self::STATUS_HOME		    =>'首页',
			self::STATUS_MONEY		    =>'找资金',
			self::STATUS_GOODS		    =>'找货',
			self::STATUS_CARS		    =>'找车',
	] ;
	
	public function getPage ($currentPage,$param=[]) {
	    $where = [] ;
	     
        if(iseta($param,'typeStatus')){
            if(array_key_exists($param['typeStatus'], $this->statusArray)){
                
             $where['I_type'] = $param['typeStatus'];
             
            }
            
        }
	     
	    $query = $this->getMQuery($where) ;
	    return $this->getPaginationByQuery($query, $currentPage) ;
	}
	
	
	
	public function getById ($id) {
	    return $this->getMQuery(['id'=>$id])->find() ;
	}
	
	
	private function getMQuery ($where = []) {
	    $query = new Query() ;
	    $where['state'] = self::DEFAULT_STATUS_NORMAL;
	    $query->table($this->table)
	    ->field('*')
	    ->where($where)
	    ->order(['I_type'=>'desc','I_order'=>'desc']);
	    return $query ;
	}
	public function getSlideByType($type){
		$re= $this->getMQuery(['I_type'=>$type])->select();
		return $re;
	}
	
	
	
	
	
	
	
	
}