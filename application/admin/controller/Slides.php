<?php
namespace app\admin\controller ;
use think\paginator\driver\Bootstrap;
use app\common\model\SlidesModel;

class Slides extends AdminController {
	private $slidesModel ;
	
	public function _initialize() {
		$this->slidesModel = new SlidesModel();
		parent::_initialize();
	}
    public function index() {
    	$currentPage = $this->request->get('page',1) ;
    	$param['typeStatus'] = input('typeStatus/d',0);
    	$page = $this->slidesModel->getPage($currentPage,$param) ;
    	$this->assign([
    	    'model' =>$this->slidesModel,
    	    'page' =>$page,
    	    'param'=>$param,
    	]) ;
    	return $this->fetch("index") ;

    }
    public function del() {
       
        $data['id'] =  $this->request->get('id/d',0,'htmlspecialchars');
       
        $data['state'] = 0;
        $result  = $this->slidesModel->update($data);
        
        if($result){
           
            $this->addManageLog('轮播图设置', '删除ID为'.$data['id'].'的轮播图');
            return getJsonStrSuc('删除成功');
        } else {
          
            return getJsonStr(500,'删除失败');
        }
        
    }
   
    public function add() {
        
        if($this->request->isPost()){
            
            $data['Vc_name'] =  $this->request->post('Vc_name/s','','htmlspecialchars');
            $data['I_type'] =  $this->request->post('I_type/d',0);
            $data['Vc_path'] =  $this->request->post('Vc_pic/s','','htmlspecialchars');
            $data['Vc_linkurl'] =  $this->request->post('Vc_linkurl/s','','htmlspecialchars');
            $data['I_blank'] =  $this->request->post('I_blank/d',0);
            $data['I_order'] =  $this->request->post('I_order/d',0);
            if(!$data['Vc_name']){return getJsonStr(500,'图片名称不能为空！');}
            if(!$data['I_type']){return getJsonStr(500,'未选择所属页面！');}
            if(!$data['Vc_path']){return getJsonStr(500,'图片未上传！');}
            if(!$data['I_order']){return getJsonStr(500,'排序不能为空！');}

           
            
            $result = $this->slidesModel->save($data);
            if ($result) {
                $this->addManageLog('轮播图设置', '添加ID为'.$result.'的轮播图');
                return getJsonStrSuc('新增成功');
               
            } else {
                 return getJsonStr(500,'新增失败');
            }
            
            
        }else{
            
            
            $this->assign([
                'model' =>$this->slidesModel,
            ]) ;
            return $this->fetch() ;
            
        }

    }
    public function edit() {
          if($this->request->isPost()){
            $data['id'] =  $this->request->post('id/d','','htmlspecialchars');
            if(!$data['id']){return getJsonStr(500,'参数有误！');}
            
            $data['Vc_name'] =  $this->request->post('Vc_name/s','','htmlspecialchars');
            $data['I_type'] =  $this->request->post('I_type/d',0);
            $data['Vc_path'] =  $this->request->post('Vc_pic/s','','htmlspecialchars');
            $data['Vc_linkurl'] =  $this->request->post('Vc_linkurl/s','','htmlspecialchars');
            $data['I_blank'] =  $this->request->post('I_blank/d',0);
            $data['I_order'] =  $this->request->post('I_order/d',0);
            if(!$data['Vc_name']){return getJsonStr(500,'图片名称不能为空！');}
            if(!$data['I_type']){return getJsonStr(500,'未选择所属页面！');}
            if(!$data['Vc_path']){return getJsonStr(500,'图片未上传！');}
            if(!$data['I_order']){return getJsonStr(500,'排序不能为空！');}

            $result = $this->slidesModel->update($data);
            if ($result) {
                $this->addManageLog('轮播图设置', '编辑ID为'.$data['id'].'的轮播图');
                return getJsonStrSuc('编辑成功');
            } else {
                   return getJsonStr(500,'编辑失败');
            }
        }else{
            
            $id =  $this->request->get('id/d','','htmlspecialchars');
            $data = $this->slidesModel->getById($id);
            $this->assign([
                'model' =>$this->slidesModel,
                'data' =>$data,
            ]) ;
            return $this->fetch() ;
        }

    }
    
}
