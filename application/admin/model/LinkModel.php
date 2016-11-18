<?php
namespace app\admin\model ;
use \think\db\Query;
use think\Model;

class LinkModel extends AdminModel{

	protected $table = 'sm_link' ;
	protected $pk = 'id' ;
	protected $createTime = false;
	protected $updateTime = 'Createtime' ;
	protected $insert = ['state'=>1];
	protected $status = 'state' ;

	const TYPE_Y  = 'Y' ;
	const TYPE_N  = 'N';

	const LINK_FIND_MONEY  = 1 ;
	const LINK_FIND_GOODS  = 2 ;
	const LINK_FIND_CARS  	= 3 ;
	const LINK_MAX_NUMBER  	= 12 ;

	public $pushArray = [
		self::TYPE_Y		=>'推送',
		self::TYPE_N		=>'不推送',
	] ;
	public $typeArray = [
		self::LINK_FIND_MONEY		=>'找资金',
		self::LINK_FIND_GOODS		=>'找货',
		self::LINK_FIND_CARS		=>'找车',
	] ;

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

	public function getPageList ($page = 1) {
		return $this->getPagination ($page, function (Query $query) {
			$query->where($this->status,'=',parent::DEFAULT_STATUS_NORMAL) ;
			$query->order('Createtime desc');
		}) ;
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

	/**
	 * 获取四个友情链接
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	public function getFour ($type) {
		$query=new Query();
		return $query->table($this->table)
			->where(['state'=>1,'I_type'=>$type])
			->limit(self::LINK_MAX_NUMBER)
			->order('I_order', 'desc')
			->select();
	}
}