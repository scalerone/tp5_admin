<?php
namespace app\admin\model ;
use \think\db\Query;
use think\Model;

class ConfigureModel extends AdminModel {

	protected $table = 'configure' ;
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

	public function getPageList ($code='',$page = 1) {
	    return $this->getPagination ($page, function (Query $query) use ($code){
	        $query->where($this->status,'=',parent::DEFAULT_STATUS_NORMAL) ;
	        if ($code) {
	            $query->where("code","like","%{$code}%") ;
	        }
	        $query->order('code');
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
	
	public static function update($data = [], $where = []) {
		$result = parent::update($data , $where) ;
		if ($result) {
			$config = self::get($data['id']) ;
			cache($config->code, $config->value) ;
		}
		return $result ;
	}
	
	public function saveCache() {
		$configs = $this->all(function(Query $query) {
			$query->where('state','=',1) ;
		}) ;
		foreach ($configs as $cfg) {
			cache($cfg->code,$cfg->value) ;
		}
		return true ;
	}
	
	public function getValue ($key, $isUpdateCache = false) {
		if ($isUpdateCache) {
			$value = parent::where('code',$key)->value('value') ;
			cache($key, $value) ;
		}
		return cache($key) ;
	}
 
}
