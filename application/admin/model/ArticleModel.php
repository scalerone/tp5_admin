<?php
namespace app\admin\model ;
use \think\db\Query;
use think\Model;

class ArticleModel extends AdminModel{
	
	protected $table = 'sm_article' ;
	protected $pk = 'id' ;
	protected $createTime = false;
	protected $updateTime = 'Createtime' ;
	protected $insert = ['state'=>1];
	protected $status = 'state' ;
	
	// status查询
	public function scopeStatus($query)
	{
	    $query->where('state', 1);
	}
	
	/**
	 * 获取十条数据
	 */
	public function getPage($currentPage){
	    $query=new Query();
	    $query->table($this->table)
	    ->where('state',1);
	    return $this->getPaginationByQuery($query, $currentPage) ;
	}

	/**分页展示
	 * @param int $page
	 * @param $param
	 * @return \think\paginator\driver\Bootstrap
	 */
	public function getPageList ($page = 1,$param) {
		$where = [] ;
		if(iseta($param,'keywords')){
			//订单号,用户,订单名称,采购人,采购名称查询
			$where['a.Vc_name'] =['like','%'.trim($param['keywords']).'%'];
		}
		if(iseta($param,'classID')){
			$where['a.I_article_classID'] = $param['classID'];
		}
		$query = $this->getMQuery($where) ;
		return $this->getPaginationByQuery($query, $page) ;
	}

	/**
	 * @param array $where
	 * @return Query
	 */
	public function getMQuery ($where = []) {
		$query = new Query() ;
		$where['a.state'] = self::DEFAULT_STATUS_NORMAL;
		$query->table($this->table)->alias('a')
			->field('a.*,ac.Vc_name as classname')
			->join('sm_article_class ac',' a.I_article_classID=ac.id','left')
			->order('a.I_article_classID desc,a.I_order desc,a.D_releasetime desc,a.Createtime desc')
			->where($where);
		return $query ;
	}

	public function getListByClassId($classid){
		$query=new Query();
		return $query->table($this->table)
			->where(['state'=>1,'I_article_classID'=>$classid])->select();
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
	/**
	 * 验证参数是否重复添加
	 * @param array $data
	 * @return boolean
	 */
	public function checkParamUpdate($data) {
	    //检查是否重复添加
	    $id=$data['id'];
	    unset($data['id']);
	    $find = $this->field('id')->where($data)->find();
	    if (isset($find['id']) && $find['id']!=$id) {
	        return false;
	    }
	    return true;
	}
	
}