<?php
namespace app\admin\controller ;
use think\paginator\driver\Bootstrap;
use \app\admin\model\LinkModel ;
use think\db\Query;

class Link extends AdminController {
	private $linkModel ;
	
	public function _initialize() {
		$this->linkModel = new LinkModel();
		parent::_initialize();
	}
	
    public function index() {
    	$currentPage = $this->request->get('page',1) ;
    	$page = $this->linkModel->getPageList($currentPage) ;
    	$this->assign([
            'page'=>$page,
            'model'=>$this->linkModel,
        ]) ;
    	return $this->fetch("index") ;

//     	http://admin.lightlamp.com/admin/configure/listPage
    }
    public function del() {
       
        $data['id'] =  $this->request->get('id/d',0,'htmlspecialchars');
       
        $data['state'] = 0;
        $result  = $this->linkModel->update($data);
        
        if($result){
           
            $this->addManageLog('友情链接', '删除ID为'.$data['id'].'的友情链接');
            return getJsonStrSuc('删除成功');
        } else {
          
            return getJsonStr(500,'删除失败');
        }
        
    }
   
    public function add() {
        
        if($this->request->isPost()){
            
            $data['I_type'] =  $this->request->post('I_type/d');
            $data['Vc_name'] =  $this->request->post('Vc_name/s','','htmlspecialchars');
            $data['Vc_logo'] =  $this->request->post('Vc_logo/s','','htmlspecialchars');
            $data['Vc_link'] =  $this->request->post('Vc_link/s','','htmlspecialchars');
            $data['I_order'] =  $this->request->post('I_order/d');
            if(!$data['I_type']){return getJsonStr(500,'未选择栏目！');}
            if(!$data['Vc_name']){return getJsonStr(500,'名称不能为空！');}
            if(!$data['Vc_logo']){return getJsonStr(500,'logo不能为空！');}
//            if(!$data['Vc_link']){return getJsonStr(500,'跳转地址不能为空！');}
            if(!$data['I_order']){return getJsonStr(500,'排序不能为空！');}

            
            //验证友情链接是否重复添加
            if(!$this->linkModel->checkParam(array('Vc_name'=> $data['Vc_name'],'state'=>1))){
                return getJsonStr(500,'名称已存在！');
            }
            
            $result = $this->linkModel->save($data);
            if ($result) {
                $this->addManageLog('友情链接', '添加ID为'.$result.'的友情链接');
                return getJsonStrSuc('新增成功');
               
            } else {
                 return getJsonStr(500,'新增失败');
            }
            
            
        }else{
            $this->assign([
                'model'=>$this->linkModel,
            ]) ;
            return $this->fetch() ;
            
        }

    }
    public function edit() {
          if($this->request->isPost()){
            $data['id'] =  $this->request->post('id/d','','htmlspecialchars');
            if(!$data['id']){return getJsonStr(500,'参数有误！');}
              $data['I_type'] =  $this->request->post('I_type/d');
              $data['Vc_name'] =  $this->request->post('Vc_name/s','','htmlspecialchars');
              $data['Vc_logo'] =  $this->request->post('Vc_logo/s','','htmlspecialchars');
              $data['Vc_link'] =  $this->request->post('Vc_link/s','','htmlspecialchars');
              $data['I_order'] =  $this->request->post('I_order/d');
              if(!$data['I_type']){return getJsonStr(500,'未选择栏目！');}
              if(!$data['Vc_name']){return getJsonStr(500,'名称不能为空！');}
              if(!$data['Vc_logo']){return getJsonStr(500,'logo不能为空！');}
//              if(!$data['Vc_link']){return getJsonStr(500,'跳转地址不能为空！');}
              if(!$data['I_order']){return getJsonStr(500,'排序不能为空！');}
            //验证友情链接是否重复添加
            if(!$this->linkModel->checkParamUpdate(array('Vc_name'=> $data['Vc_name'],'state'=>1,'id'=>$data['id']))){
                return getJsonStr(500,'友情链接已存在！');
            }
            $result = $this->linkModel->update($data);
            if ($result) {
                $this->addManageLog('友情链接', '编辑ID为'.$data['id'].'的友情链接');
                return getJsonStrSuc('编辑成功');
            } else {
                   return getJsonStr(500,'编辑失败');
            }
        }else{
            $id =  $this->request->get('id/d','','htmlspecialchars');
            $configure = $this->linkModel->get($id);
              $this->assign([
                  'model'=>$this->linkModel,
                  'data'=>$configure,
              ]) ;
            return $this->fetch() ;
        }

    }
    
}
