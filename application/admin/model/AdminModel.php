<?php
namespace app\admin\model ;

use think\Model;
use think\paginator\driver\Bootstrap;
use think\db\Query;
use think\db\think\db;
use app\common\model\BaseModel;
use app\common\model\page\Mypage;
class AdminModel extends BaseModel {
	
    const DEFAULT_PAGE_SIZE = 10 ;
    
    /**
     * 获取分页结果
     * @param number $currentPage       当前页
     * @param mixed $where              字符串或者闭包
     * @return \think\paginator\driver\Bootstrap
     */
    function getPagination($currentPage=1,$where=null,$pageSize=0) {
    	if(!$pageSize){$pageSize = self::DEFAULT_PAGE_SIZE;}
        $start = ($currentPage-1)*$pageSize ;
        $data = $this->db()->limit($start,$pageSize)->select($where) ;
        $query = $this->db() ;
        if ($where) {
            $query->where($where) ;
        }
        $count = $query->count($this->pk) ;
        return $this->createPagination($data, $currentPage, $count,$pageSize) ;
    }
    
    
    /**
     * 获取Query对象查询的分页结果
     * @param Query $query
     * @param unknown $currentPage
     * @return \think\paginator\driver\Bootstrap
     */
    public function getPaginationByQuery(Query $query, $currentPage,$pageSize=0) {
        $countQuery = clone $query ;
        $total = $countQuery->count() ;
        if(!$pageSize){$pageSize = self::DEFAULT_PAGE_SIZE;}
        if($total%$pageSize==0){
            $totalpage=$total/$pageSize;
        }else{
            $totalpage=intval($total/$pageSize)+1;
        }

        $start = ($currentPage - 1) * $pageSize ;
        $data = $query->limit($start,$pageSize)->select() ;
        return $this->createPagination($data, $currentPage, $total,$pageSize) ;
    }
    /**
     * 获取Query对象查询的分页结果针对having的存在
     * @param Query $query
     * @param unknown $currentPage
     * @return \think\paginator\driver\Bootstrap
     */
    public function getPaginationByQueryExistHaving(Query $query, $currentPage,$pageSize=0) {
        $countQuery = clone $query ;
        if(!$pageSize){$pageSize = self::DEFAULT_PAGE_SIZE;}
        $start = ($currentPage - 1) * $pageSize ;
        $data = $query->limit($start,$pageSize)->select() ;
        $total = count($countQuery->select());
        return $this->createPagination($data, $currentPage, $total,$pageSize) ;
    }
    
    public function createPagination($data, $currentPage, $total,$pageSize=0) {
    	if(!$pageSize){$pageSize = self::DEFAULT_PAGE_SIZE;}
//         return new Bootstrap($data, $pageSize,$currentPage,false,$total) ;
        return new Mypage($data, $pageSize,$currentPage,false,$total) ;
    }
	
	
}