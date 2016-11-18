<?php
namespace app\common\model ;

use think\Model;
use think\db\Query;
use think\db\think\db;
class BaseModel extends Model {
	
	const DEFAULT_STATUS_NORMAL = 1 ;
    const DEFAULT_STATUS_DELETE = 0 ;
    
    protected $pk 			= 'id' ;
    protected $status 		= 'state' ;
    protected $updateTime 	= false ;
    protected $deleteTime 	= false ;
    protected $createTime	= 'Createtime' ;
    
    public function getStatusAttr ($value) {
    	$status = [
    			self::DEFAULT_STATUS_DELETE		=>		'删除',
    			self::DEFAULT_STATUS_NORMAL		=>		'正常',
    	] ;
    	return $status[$value] ;
    }
    
    /**
     * 获取Query对象
     * @return \think\db\Query
     */
    protected function getQuery() {
        return new Query() ;
    }
    
    /**
     * 根据id查找未被逻辑删除的数据
     * @param unknown $id
     * @return \think\static
     */
    public function getById ($id) {
        return $this->get([
            $this->pk       =>      $id,
            $this->status   =>      self::DEFAULT_STATUS_NORMAL,
        ]) ;
    }
    
    /**
     * 获取全部有效的数据
     * @return Ambigous <multitype:\think\static , \think\false>
     */
    public function getAll() {
        return $this->all(function(Query $query) {
            $query->where([$this->status=>1,'I_isLeftMenu'=>1])
            ->order('I_order','desc');
        }) ;
    }
    
    /**
     * 执行逻辑删除       
     * @param int $id
     */
    public function deleteById ($id) {
        $this->save([$this->status=>self::DEFAULT_STATUS_DELETE],[$this->pk=>$id]) ;
    }
    
    /**
     * 修改数据
     * @param unknown $id
     * @param unknown $data
     * Author sakura 2016年7月11日下午4:35:59
     */
    public function updateDataById($id,$data){
        return $this->where($this->pk,$id)->setField($data);
    }
    /**
     * 修改数据
     * @param unknown $ids 用,连接的字符串
     * @param unknown $data
     * Author sakura 2016年7月20日上午9:48:12
     */
    public function updateDataByIds($ids,$data){
    	return $this->where($this->pk,'in',$ids)->setField($data);
    }
	    
    /**
     * 保存数据
     * @param unknown $data
     * Author sakura 2016年7月11日下午6:39:13
     */
    public function saveData($data){
        return $this->save($data);
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
     * 编辑时验证参数是否重复添加
     * @param array $data
     * @return boolean
     */
    public function checkParamUpdate($data) {
    	//检查是否重复添加
    	$id=$data[$this->pk];
    	unset($data[$this->pk]);
    	$find = $this->field($this->pk)->where($data)->find();
    	if (isset($find[$this->pk]) && $find[$this->pk]!=$id) {
    		return false;
    	}
    	return true;
    }
	
}