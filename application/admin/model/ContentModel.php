<?php
namespace app\admin\model ;
use think\db\Query;
use think\Model;
class ContentModel extends AdminModel {

	protected $table = 'content' ;
	protected $pk = 'id' ;
	protected $createTime = 'create_date' ;
	protected $updateTime = 'create_date' ;
	protected $status	  = 'state' ;
	protected $insert     = ['state'=>1];
	const CONTENT_HELP_CODE = 'HELP';
	
	/**
	 * 获取十条数据
	 */
	public function getPage($currentPage){
		$query=new Query();
		$where=['a.state'=>1];
		$query->table($this->table)->alias('a')
			->order('id desc')
			->where($where);
		return $this->getPaginationByQuery($query, $currentPage) ;
	}
	
	/**
	 * 获取帮助信息
	 * 
	 * Author sakura 2016年7月30日下午4:45:10
	 */
	public function getOneByHelp(){
		return $this->get(['code'=>self::CONTENT_HELP_CODE,$this->status=>1]);
	}
	
	public function getOneByCode($code){
		
	}
	
	/**
	 * 获取一条数据
	 */
	public function getOneSeminarInfo($id){
		$da = db('seminar')->alias('a')
			->field('a.*,b.name as tname')
			->join('temple b','b.id=a.temple_id','left')
			->where('a.state=1 and a.id='.$id)
			->find();
		$da['pic']=explode(',',$da['pic']);
		return $da;
	}
	public function check($id){
		$re= db('palace_pic')->where('state=1 and type=3 and palace_id='.$id)->find();
		if($re){
			return true;
		}
	}
}