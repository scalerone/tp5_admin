<?php
namespace app\common\model ;
use \think\db\Query;
use app\admin\model\AdminModel;
class UserModel extends AdminModel {
	
	protected $table = "sm_user" ;
	protected $pk = 'id' ;
	protected $createTime = 'Createtime' ;
	protected $updateTime = 'Createtime' ;
	protected $status = 'state' ;
	
	const STATUS_INPROGRESS  			= 1 ;
	const STATUS_REJECT  		    	= 2 ;
	const STATUS_FINISH  		    	= 3 ;
	const STATUS_UNAPPLY 				= 4 ;
	
	
	const USER_LOCK 				= 1;
	const USER_NOT_LOCK				= 0;
	
	
	public $statusArray = [
			self::STATUS_INPROGRESS		=>'认证中',
			self::STATUS_REJECT		    =>'认证不通过',
			self::STATUS_FINISH		    =>'认证通过',
			self::STATUS_UNAPPLY		=>'未认证',
	] ;
	
	public function getPage ($currentPage,$param=[]) {
	    $where = [] ;
	     
	    if(iseta($param,'uname')){
	        
	        if(preg_match('/^\d+$/', $param['uname'])){//手机号
	            
	           $where['a.Vc_mobile'] =['like','%'.trim($param['uname']).'%'];
	           
	        }else{//用户名
	            
	           $where['a.Vc_name'] =['like','%'.trim($param['uname']).'%'];
	        }
	        
	    }
	    
        if(iseta($param,'certifyStatus')){
            if($param['certifyStatus']==4){
                
             $where['e.I_status'] = ['exp','is null'];
             
            }else{
                
              $where['e.I_status'] = $param['certifyStatus'];
                
            }
            
            
        }
	     
	    $query = $this->getMQuery($where) ;
	    return $this->getPaginationByQuery($query, $currentPage) ;
	}
	
	public function getCompanyPage ($currentPage,$param=[]) {
	    $where = [] ;
	     
	    if(iseta($param,'uname')){
	        
	        if(preg_match('/^\d+$/', $param['uname'])){//手机号
	            
	           $where['e.Vc_mobile'] =['like','%'.trim($param['uname']).'%'];
	           
	        }else{//用户名/公司名
	            
	           $where['e.Vc_name|a.Vc_name|a.Vc_applicantName'] =['like','%'.trim($param['uname']).'%'];
	        }
	        
	    }
	    
        if(iseta($param,'certifyStatus')){
           
              $where['a.I_status'] = $param['certifyStatus'];
            
        }
	     
	    $query = $this->getCompanyQuery($where) ;
	    return $this->getPaginationByQuery($query, $currentPage) ;
	}
	
	public function getById ($id) {
	    return $this->getMQuery(['a.id'=>$id])->find() ;
	}
	public function getCompanyById ($uid) {
	    return $this->getCompanyQuery(['a.I_userID'=>$uid])->find() ;
	}
	private function getMQuery ($where = []) {
	    $query = new Query() ;
	    $where['a.state'] = self::DEFAULT_STATUS_NORMAL;
	
	    $query->table($this->table)->alias('a')
	    ->field('a.Vc_name uname,a.id uid,a.*,b.name proname,c.name cityname,d.name areaname,IFNULL(e.I_status,4) I_status')
	    ->join('s_province b','b.id=a.I_provinceID','left')
	    ->join('s_city c','c.id=a.I_cityID','left')
	    ->join('s_district d','d.id=a.I_districtID','left')
	    ->join('sm_user_company e','e.I_userID = a.id','left')
	    ->where($where)
	    ->order(['a.Createtime'=>'desc']);
	    return $query ;
	}
	
	private function getCompanyQuery ($where = []) {
	    $query = new Query() ;
	    $where['a.state'] = self::DEFAULT_STATUS_NORMAL;
	    $where['e.state'] = self::DEFAULT_STATUS_NORMAL;
	
	    $query->table('sm_user_company')->alias('a')
	    ->field('a.*,b.name proname,c.name cityname,d.name areaname,e.Vc_name uname,e.Vc_mobile umobile')
	    ->join('s_province b','b.id=a.I_provinceID','left')
	    ->join('s_city c','c.id=a.I_cityID','left')
	    ->join('s_district d','d.id=a.I_districtID','left')
	    ->join('sm_user e','e.id = a.I_userID','left')
	    ->where($where)
	    ->order(['a.Createtime'=>'desc']);
	    return $query ;
	}
	
	
	
	
	
	
	
	
	
}