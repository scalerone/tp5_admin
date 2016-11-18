<?php
namespace app\common\model ;
use \think\db\Query;
use app\admin\model\AdminModel;
class FindCarsModel extends AdminModel {
	
	protected $table = "sm_findcars" ;
	protected $pk = 'id' ;
	protected $createTime = 'Createtime' ;
	protected $updateTime = 'D_updatetime' ;
	protected $status = 'state' ;
	
	const STATUS_CHECKING  			    = 1 ;
	const STATUS_PASS  			        = 2 ;
	const STATUS_REJECT  		    	= 3 ;
	const STATUS_CANCEL		  			= 4 ;

	
	public $statusArray = [
			self::STATUS_CHECKING		=>'待审核',
			self::STATUS_PASS		    =>'审核通过',
			self::STATUS_REJECT		    =>'审核未通过',
			self::STATUS_CANCEL			=>'已取消',
	] ;
	
	public function getPage ($currentPage,$param=[]) {
	    $where = [] ;
	     
	    if(iseta($param,'keywords')){
	        
	           $where['a.Vc_orderSn|a.I_userID|a.Vc_senter|a.Vc_receiver'] =['like','%'.trim($param['keywords']).'%'];
	        
	        
	    }
	    
        if(array_key_exists($param['applystatus'],$this->statusArray)){
            
              $where['a.I_status'] = $param['applystatus'];
            
        }
	     
	    $query = $this->getMQuery($where) ;
	    return $this->getPaginationByQuery($query, $currentPage) ;
	}
	
	
	public function getById ($id) {
	    return $this->getMQuery(['a.id'=>$id])->find() ;
	}
	
	public function getCarsListById ($fid) {
	    return $this->getContactQuery(['a.I_findcarsID'=>$fid])->select() ;
	}
	private function getContactQuery ($where = []) {
	    $query = new Query() ;
	    $where['a.state'] = self::DEFAULT_STATUS_NORMAL;
	    $query->table('sm_findcars_list')->alias('a')
	    ->field('a.*')
	    ->where($where)
	    ->order(['a.Createtime'=>'asc']);
	    return $query ;
	}
	
	private function getMQuery ($where = []) {
	    $query = new Query() ;
	    $where['a.state'] = self::DEFAULT_STATUS_NORMAL;
	    $query->table($this->table)->alias('a')
	    ->field('a.*,si.Vc_name industryname,su.Vc_name username,sp1.name proname1,sp2.name proname2,
	    sc1.name cityname1,sc2.name cityname2,sd1.name areaname1,sd2.name areaname2')
	    ->join('sm_industry si','si.id=a.I_industryID','left')
	    ->join('sm_user su','su.id = a.I_userID','left')
	    ->join('s_province sp1','sp1.id=a.I_sent_provinceID','left')
	    ->join('s_province sp2','sp2.id=a.I_receive_provinceID','left')
	    ->join('s_city sc1','sc1.id=a.I_sent_cityID','left')
	    ->join('s_city sc2','sc2.id=a.I_receive_cityID','left')
	    ->join('s_district sd1','sd1.id=a.I_sent_districtID','left')
	    ->join('s_district sd2','sd1.id=a.I_receive_districtID','left')
	    ->where($where)
	    ->order(['a.Createtime'=>'desc']);
	    return $query ;
	}
	
	
	
	
	
	
	
	
	
}