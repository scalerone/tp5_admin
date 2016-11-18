<?php
namespace app\admin\model ;
use \think\db\Query;
use think\Model;

class ArticleClassModel extends AdminModel {
	
	protected $table = 'sm_article_class' ;
	protected $pk = 'id' ;
	protected $createTime = 'Createtime';
	protected $updateTime = false ;
	protected $insert = ['state'=>1];
	protected $status = 'state' ;
	
	const TYPE_Y  = 'Y' ;
	const TYPE_N  = 'N';
	
	public $pushArray = [
	    self::TYPE_Y		=>'推送',
	    self::TYPE_N		=>'不推送',
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
	        $query->order('I_order desc');
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

	/**获取所有的文章类
	 * @return false|\PDOStatement|string|\think\Collection
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	public function getAllArticleClass(){
		return $this->where('state',1)->select();
	}
	public function getBottonArticles(){
		$re1=$this->field('id,Vc_name,I_position')->where(['state'=>1,'I_isLeftMenu'=>1])->select();
		foreach ($re1 as $k=>$r){
			$query=new Query();
			$re2=$query->field('id,Vc_name')->table('sm_article')->where(['state'=>1,'I_article_classID'=>$r['id']])->select();
			$re1[$k]['contents']=$re2;
		}
		return $re1;
	}

	/**
	 * 获取早报id 一个id
	 */
	public function getNewsId(){
		$re=$this->field('id')->where(['state'=>1,'I_isLeftMenu'=>2])->find();
		return $re['id'];
	}
}