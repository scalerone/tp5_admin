<?php
namespace app\admin\controller ;

use app\admin\controller\AdminController;

class Comm extends AdminController {
	
	
    public function __construct() {
	
		parent::__construct() ;
	}
	public  function  getProvince(){
	    
	    $arr =  db('s_province')->where(['state'=>1])->select();
	    return json($arr);
	
	}
	public  function  getCity($pid){
	     
	    
	    $arr =  db('s_city')->where(['state'=>1,'province_id'=>$pid])->field('id,name')->select();
	    return json($arr);
	
	}
	
	public  function  getDistrict($pid){
	    $arr =  db('s_district')->where(['state'=>1,'city_id'=>$pid])->field('id,name')->select();
	     return json($arr);
	
	}
	
	
}