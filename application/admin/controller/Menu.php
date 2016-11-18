<?php
namespace app\admin\controller ;

use \app\admin\model\MenuModel ;

class Menu extends AdminController {
	
	private $menuModel ;
	
	public function _initialize() {
		$this->menuModel = new MenuModel();
		parent::_initialize();
	}
	
	/**
	 * 判断是否是二级菜单
	 * @return \think\response\Json
	 */
	public function isSubMenu ($pid=0) {
	    
	    $rs = $this->menuModel->checkSubMenu($pid);
	    if($rs){
	        return getJsonStrSuc();
	    }else{
	        return getJsonStr(500);
	    }
	     
	}
	
    public function index() {
        
    	$currentPage = $this->request->get('page',1) ;
    
    	// 查询状态为1的用户数据 并且每页显示10条数据
    	$list = $this->menuModel->getPage($currentPage);
    	// 把分页数据赋值给模板变量list
    	$this->assign('list', $list);
    	
    	return $this->fetch("index") ;
    }
    /**
     *  显示菜单
     */
    public function listPage() {
        //得到所有菜单数组
        $result = $this->menuModel->where(array('state'=>1))->order(["sort" => "desc"])->select();
        foreach ($result as $k=>$m){
            $result[$k]=$m->toArray();
        }
        
        import('Tree', EXTEND_PATH, '.class.php');
        
        $tree = new \Tree();
        $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
        
        $newmenus=array();
        foreach ($result as $m){
            $newmenus[$m['menuId']]=$m;
        
        }
        foreach ($result as $n=> $r) {
             
            $result[$n]['level'] = $this->_get_level($r['menuId'], $newmenus);
            $result[$n]['parentid_node'] = ($r['parentId']) ? ' class="child-of-node-' . $r['parentId'] . '"' : '';
//             if($r['parentId']==0){
            $result[$n]['str_manage'] = '--';
            if($r['notedit']==0){
                $result[$n]['str_manage'] = '<a class="btn btn-primary btn-xs" href="' . url('admin/menu/add',['id'=>$r['menuId']]) . '">'.'<i class="fa fa-plus-square"></i>添加子菜单'.'</a>
                 <a class="btn btn-info btn-xs" href="' . url('admin/menu/edit',['id'=>$r['menuId']]) . '">'.'<i class="fa fa-pencil"></i>编辑'.'</a>
                 <a class="btn btn-danger btn-xs js-ajax-delete"  data-href="' . url('admin/menu/del',['id'=>$r['menuId']]). '">'.'<i class="fa fa-trash-o"></i>删除'.'</a> ';
            }
//             }else{
//             $result[$n]['str_manage'] = '<a class="btn btn-primary btn-xs" style="visibility:hidden;"><i class="fa fa-plus-square"></i>添加子菜单</a>'.'
//                  <a class="btn btn-info btn-xs" href="' . url('admin/menu/edit',['id'=>$r['menuId']]) . '">'.'<i class="fa fa-pencil"></i>编辑'.'</a> 
//                  <a class="btn btn-danger btn-xs js-ajax-delete"  data-href="' . url('admin/menu/del',['id'=>$r['menuId']]). '">'.'<i class="fa fa-trash-o"></i>删除'.'</a> ';
//             }
            $result[$n]['status'] = $r['leftShow'] ? '是' : '否';
            
            $result[$n]['app']=$r['menuUrl'];
            $result[$n]['parentid']=$r['parentId'];
            $result[$n]['id']=$r['menuId'];
        }
        
        $tree->init($result);
        $str = "<tr id='node-\$id' \$parentid_node>
					<td style='padding-left:20px;'><input name='listorders[\$id]' type='text' size='3' value='\$sort' class='input input-order'></td>
					<td>\$id</td>
        			<td>\$app</td>
					<td style='text-align: left'>\$spacer\$menuName</td>
        			<td>\$showName</td>
				    <td>\$status</td>
				    <td>\$Createtime</td>
					<td>\$str_manage</td>
				</tr>";
        $categorys = $tree->get_tree(0, $str);
        $this->assign("categorys", $categorys);
    	return $this->fetch('listPage') ;
    }
    
    
    /**
     * 获取菜单深度
     * @param $id
     * @param $array
     * @param $i
     */
    protected function _get_level($id, $array = array(), $i = 0) {
    
        if ($array[$id]['parentId']==0 || empty($array[$array[$id]['parentId']]) || $array[$id]['parentId']==$id){
            return  $i;
        }else{
            $i++;
            return $this->_get_level($array[$id]['parentId'],$array,$i);
        }
    
    }
    
    
    
    
    public function del($id=0) {
         
        $data['menuId'] = $id;
         
        $data['state'] = '0';
        $data['operatorId']=$this->getSessionUid();
        $result  = $this->menuModel->update($data);
    
        if($result){
            $this->addManageLog('后台菜单', '删除ID为'.$id.'的参数');
            return getJsonStrSuc('删除成功');
        } else {
    
            return getJsonStr(500,'删除失败');
        }
    
    }
    /**
     * 
     * 获取同级高亮显示菜单
     */
    public function getChildNode(){
        
        $menuId=input('menuId/d');
        $id=input('id/d',0);
        if($id){
            $result = $this->menuModel->where(array('state'=>1,'parentId'=>$menuId,'leftShow'=>1))->where('menuId!='.$id)->field('menuId,showName')->order(["sort" => "desc"])->select();
            
        }else{
        $result = $this->menuModel->where(array('state'=>1,'parentId'=>$menuId,'leftShow'=>1))->field('menuId,showName')->order(["sort" => "desc"])->select();
            
        }
        foreach ($result as $k=>$m){
            $result[$k]=$m->toArray();
        
        }
        return getJsonStrSuc('',$result);
        
    }
    public function add() {
    
        import('Tree', EXTEND_PATH, '.class.php');
  
        $tree = new \Tree();
        $parentid =  $this->request->get('id/d');

        $result = $this->menuModel->where(array('state'=>1))->order(["sort" => "desc"])->select();
        foreach ($result as $k=>$m){
            $result[$k]=$m->toArray();
        
        }
        $array=array();
        foreach ($result as $r) {
            $r['selected'] = $r['menuId'] == $parentid ? 'selected' : '';
            $r['parentid'] = $r['parentId'];//tree要用，必传
            $r['id'] = $r['menuId'];//tree要用，必传
            $array[] = $r;
        }
        $str = "<option value='\$menuId' \$selected>\$spacer \$menuName</option>";
        $tree->init($array);
        $select_categorys = $tree->get_tree(0, $str);
        $this->assign("select_categorys", $select_categorys);
        
         return $this->fetch() ;
    
    
    }
    
    public function add_post() {
       
        if($this->request->isPost()){
        
            $data['parentId'] =  input('post.parentId/d');
            $data['menuName'] =  input('post.menuName/s');
            $data['showName'] =  input('post.showName/s');
            $data['menuUrl'] =  strtolower(input('post.menuUrl/s'));
            $data['submitUrl'] =  strtolower(input('post.submitUrl/s'));
            $data['menuIcon'] =  input('post.menuIcon/s');
            $data['intro'] =  input('post.intro/s');
            $data['leftShow'] =  input('post.leftShow/d',0);
            $data['sort'] =  input('post.sort/d');
            $isleftRouters =  input('post.isleftRouters/d','');
            $data['operatorId']=$this->getSessionUid();
            if(!$data['menuName']){return getJsonStr(500,'菜单名称不能为空！');}
            if(!$data['showName']){return getJsonStr(500,'显示名称不能为空！');}
            if(!$data['menuUrl']){return getJsonStr(500,'控制器/方法 不能为空！');}
            
             //验证菜单是否超出三级
            if(!$this->menuModel->checkParentid($data['parentId'])){
                
                return getJsonStr(500,'菜单不能超出三级！');
            }
            //验证菜单url是否重复添加
            if(!$this->menuModel->checkAction(array('menuUrl'=> $data['menuUrl'],'state'=>1))){
            
                return getJsonStr(500,'控制器/方法 已存在！');
            }
    
            //父级是否是二级菜单
             if($this->menuModel->checkSubMenu($data['parentId'])){
                 
                 $data['leftRouters'] = $data['parentId'];
                 
             }
            
            $result = $this->menuModel->save($data);//返回自增ID
            if ($result) {
                if($isleftRouters){
                    if(is_numeric($isleftRouters)){//同级高亮
                        
                        $menu = $this->menuModel->get($result);
                        
                        $menu->leftRouters=$isleftRouters;
                      
                        $menu->save();
                        
                        
                    }
                }
                $this->addManageLog('后台菜单', '增加名为'.$data['menuName'].'的菜单');
                return getJsonStrSuc('新增成功');
                 
            } else {
                return getJsonStr(500,'新增失败');
            }
    
    
        }
    
    }
    
    public function edit() {
    
        import('Tree', EXTEND_PATH, '.class.php');
    
        $tree = new \Tree();
        $menuId =  $this->request->get('id/d');
        $rs = $this->menuModel->where('menuId',$menuId)->find();
        $rs = $rs->toArray();
        
        
        $rs2 = $this->menuModel->where(array('state'=>1,'parentId'=>$rs['parentId'],'leftShow'=>1))->where('menuId!='.$menuId)->field('menuId,showName')->order(["sort" => "desc"])->select();

        foreach ($rs2 as $k=>$m){
            $rs2[$k]=$m->toArray();
        
        }
        
//         var_dump($rs);die;
        $result = $this->menuModel->where(array('state'=>1))->order(["sort" => "desc"])->select();
        foreach ($result as $k=>$m){
            $result[$k]=$m->toArray();
    
        }
        $array=array();
        foreach ($result as $r) {
            $r['selected'] = $r['menuId'] == $rs['parentId'] ? 'selected' : '';
            $r['parentid'] = $r['parentId'];//tree要用，必传
            $r['id'] = $r['menuId'];//tree要用，必传
            $array[] = $r;
        }
        $str = "<option value='\$menuId' \$selected>\$spacer \$menuName</option>";
        $tree->init($array);
        $select_categorys = $tree->get_tree(0, $str);
        $this->assign("select_categorys", $select_categorys);
        $this->assign("data", $rs);
        $this->assign("selectcategorys", $rs2);
        return $this->fetch() ;
    
    
    }
    
    
    public function edit_post() {
         
        if($this->request->isPost()){
    
            $data['menuId'] =  input('post.id/d');
            $data['parentId'] =  input('post.parentId/d');
            $data['menuName'] =  input('post.menuName/s');
            $data['showName'] =  input('post.showName/s');
            $data['menuUrl'] =  strtolower(input('post.menuUrl/s'));
            $data['menuIcon'] =  input('post.menuIcon/s');
            $data['intro'] =  input('post.intro/s');
            $data['leftShow'] =  input('post.leftShow/d');
            $data['sort'] =  input('post.sort/d');
            $isleftRouters =  input('post.isleftRouters/d','');
            $data['operatorId']=$this->getSessionUid();
            if(!$data['menuName']){return getJsonStr(500,'菜单名称不能为空！');}
            if(!$data['showName']){return getJsonStr(500,'显示名称不能为空！');}
            if(!$data['menuUrl']){return getJsonStr(500,'控制器/方法 不能为空！');}
    
            //验证菜单是否超出三级
            if(!$this->menuModel->checkParentid($data['parentId'])){
    
                return getJsonStr(500,'菜单不能超出三级！');
            }
            //验证菜单url是否重复添加
            if(!$this->menuModel->checkActionUpdate(array('menuUrl'=> $data['menuUrl'],'menuId'=>$data['menuId'],'state'=>1))){
    
                return getJsonStr(500,'控制器/方法 已存在！');
            }
    
            $result = $this->menuModel->update($data);//
            if (false !== $result) {
                if($isleftRouters){
                    if(is_numeric($isleftRouters)){//同级高亮
    
                        $menu = $this->menuModel->get($data['menuId']);
    
                        $menu->leftRouters=$isleftRouters;
    
                        $menu->save();
    
    
                    }
                }
                $this->addManageLog('后台菜单', '编辑ID为'. $data['menuId'].'的菜单');
                return getJsonStrSuc('编辑成功');
                 
            } else {
                return getJsonStr(500,'编辑失败');
            }
    
    
        }
    
    }
    
    
    
    
    
    
}
