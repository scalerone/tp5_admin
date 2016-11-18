<?php
namespace app\admin\controller ;
use app\admin\model\RoleModel ;
use app\admin\model\MenuModel ;

class Rbac extends AdminController {
	private $roleModel,$menuModel;
	
	public function _initialize() {
		$this->roleModel = new RoleModel();
		$this->menuModel = new MenuModel();
		parent::_initialize();
	}
	
    public function listPage() {

    	
    	$list = $this->roleModel->getList(); 
    	$this->assign('list',$list) ;
    	return $this->fetch("index") ;

    }
    public function del() {
       
        $data['roleId'] =  $this->request->get('id/d',0,'htmlspecialchars');
       
        $data['state'] = '0';
        $data['operatorId']=$this->getSessionUid();
        $result  = $this->roleModel->update($data);
        
        if($result){
            $this->addManageLog('角色管理', '删除ID为'.$data['roleId'].'的角色');
            return getJsonStrSuc('删除成功');
        } else {
          
            return getJsonStr(500,'删除失败');
        }
        
    }
   
    public function add() {
        
        if($this->request->isPost()){
            
            $data['roleName'] =  $this->request->post('rolename/s','','htmlspecialchars');
            $data['roleDescription'] =  $this->request->post('roledesc/s','','htmlspecialchars');
            if(!$data['roleName']){return getJsonStr(500,'角色名称不能为空！');}
            if(!$data['roleDescription']){return getJsonStr(500,'角色描述不能为空！');}
            $data['operatorId']=$this->getSessionUid();
            //验证参数代码是否重复添加
            if(!$this->roleModel->checkParam(array('roleName'=> $data['roleName']))){
                return getJsonStr(500,'角色已存在！');
            }
            $result = $this->roleModel->create($data);
            if ($result) {
                $this->addManageLog('角色管理', '新增名为'.$data['roleName'].'的角色');
                return getJsonStrSuc('新增成功');
               
            } else {
                 return getJsonStr(500,'新增失败');
            }
            
            
        }else{
            
            return $this->fetch() ;
            
        }

    }
    public function edit() {
        
          if($this->request->isPost()){
            
            $data['roleId'] =  $this->request->post('id/d','','htmlspecialchars');
            $data['roleName'] =  $this->request->post('rolename/s','','htmlspecialchars');
            $data['roleDescription'] =  $this->request->post('roledesc/s','','htmlspecialchars');
            if(!$data['roleId']){return getJsonStr(500,'参数有误！');}
            if(!$data['roleName']){return getJsonStr(500,'角色名称不能为空！');}
            if(!$data['roleDescription']){return getJsonStr(500,'角色描述不能为空！');}
            
            $data['operatorId']=$this->getSessionUid();
            
            $result = $this->roleModel->update($data);
            if ($result) {
                $this->addManageLog('角色管理', '编辑ID为'.$data['roleId'].'的角色');
                return getJsonStrSuc('编辑成功');
            } else {
                   return getJsonStr(500,'编辑失败');
            }
            
        }else{
            $id =  $this->request->get('id/d','','htmlspecialchars');
            
            $data = $this->roleModel->get($id);

            $this->assign('data',$data) ;
            return $this->fetch() ;
            
        }

    }
    /**
     * 角色授权列表
     */
    public function authorize() {
        
        $roleid =  $this->request->get('id/d','','htmlspecialchars');
//         if (!$roleid) {
//             $this->error("参数错误！");
//         }
//         $roleid=1;
        import('Tree', EXTEND_PATH, '.class.php');
        
        $menu = new \Tree();
            $menu->icon = array('│ ', '├─ ', '└─ ');
        $menu->nbsp = '&nbsp;&nbsp;&nbsp;';
        
        $result = $this->menuModel->getList();
        foreach ($result as $k=>$m){
            $result[$k]=$m->toArray();
        
        }
        
//         var_dump($result);exit;
        $priv_data=db('core_menu_role')->where('roleId',$roleid)->column('menuId');//获取角色权限菜单值
       
        $newmenus=array();
        foreach ($result as $m){
            $newmenus[$m['menuId']]=$m;
          
        }
//         var_dump($newmenus);
        
        foreach ($result as $n => $t) {
            $result[$n]['checked'] = ($this->_is_checked($t['menuId'], $priv_data)) ? ' checked' : '';
            $result[$n]['level'] = $this->_get_level($t['menuId'], $newmenus);
            $result[$n]['parentid_node'] = ($t['parentId']) ? ' class="child-of-node-' . $t['parentId'] . '"' : '';
            $result[$n]['parentid']=$t['parentId'];
            $result[$n]['id']=$t['menuId'];
        }
   
        $str = "<tr id='node-\$menuId' \$parentid_node >
                       <td style='padding-left:30px;text-align: left'>\$spacer<input type='checkbox' name='menuid[]' value='\$menuId' level='\$level' \$checked onclick='javascript:checknode(this);'> \$menuName</td>
	    			</tr>";
        $menu->init($result);
//         $categorys = $menu->get_tree_array(0, $str);
        $categorys = $menu->get_tree(0, $str);
        
        $this->assign("categorys", $categorys);
        $this->assign("roleid", $roleid);
        return $this->fetch() ;
        
    }
    
    /**
     * 角色授权
     */
    public function authorize_post() {
      
        if ($this->request->isPost()) {
           $roleid =  $this->request->post('roleid/d','','htmlspecialchars');
           $menuid =  $this->request->post('menuid/a','','htmlspecialchars');
            if(!$roleid){
               
                return getJsonStr(500,'需要授权的角色不存在！');
            }
            if (is_array($menuid) && count($menuid)>0) {
                
                db('core_menu_role')->where('roleId',$roleid)->delete();//删除该角色权限菜单
                
                foreach ($menuid as $vid) {
                    $menu=$this->menuModel->where('menuId',$vid)->find();
                    if($menu){
                        
                       db('core_menu_role')->insert(array("roleId"=>$roleid,"menuId"=>$vid));
                    }
                }
                $this->addManageLog('角色管理', '给ID为'.$roleid.'的角色进行了授权');
                return getJsonStrSuc('授权成功','',url('/admin/rbac/listPage'));
                
                
            }else{
                //当没有数据时，清除当前角色授权
                db('core_menu_role')->where('roleId',$roleid)->delete();//删除该角色权限菜单
                
                return getJsonStr(500,'没有接收到数据，执行清除授权成功！');
            }
        }
    }
    /**
     * 检查指定菜单是否有权限
     * @param int $menuId 菜单id
     * @param array $priv_data 权限菜单数组
     * @return boolean
     */
    private function _is_checked($menuId,  $priv_data) {
         
       
        if($priv_data){
            if (in_array($menuId, $priv_data)) {
                return true;
            } else {
                return false;
            }
        }else{
            return false;
        }
         
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
    
    
    
    
    
    
    
    
    
    
    
    
}
