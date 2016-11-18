<?php
namespace app\admin\controller ;
use think\paginator\driver\Bootstrap;
use \app\admin\model\ArticleModel ;
use \app\admin\model\ArticleClassModel ;
use think\db\Query;

class ArticleClass extends AdminController {
	private $articleClassModel,$articleModel ;
	
	public function _initialize() {
		$this->articleClassModel = new ArticleClassModel();
        $this->articleModel = new ArticleModel();
		parent::_initialize();
	}

    public function index() {
    	$currentPage = $this->request->get('page',1) ;
    	$page = $this->articleClassModel->getPageList($currentPage) ;
    	$this->assign('page',$page) ;
    	return $this->fetch("index") ;

//     	http://admin.lightlamp.com/admin/configure/listPage
    }
    public function del() {
       
        $data['id'] =  $this->request->get('id/d',0,'htmlspecialchars');
        $data['state'] = 0;
        $re=$this->articleModel->where(['I_article_classID'=>$data['id'],'state'=>1])->find();
        if($re){
            return getJsonStr(500,'包含使用文章,不能删除');
        }
        $result  = $this->articleClassModel->update($data);
        if($result){
           
            $this->addManageLog('栏目管理', '删除ID为'.$data['id'].'的栏目');
            return getJsonStrSuc('删除成功');
        } else {
          
            return getJsonStr(500,'删除失败');
        }
        
    }
   
    public function add() {
        
        if($this->request->isPost()){
            
            $data['Vc_name'] =  $this->request->post('Vc_name/s','','htmlspecialchars');
            $data['Vc_intro'] =  $this->request->post('Vc_intro/s','','htmlspecialchars');
            $data['I_isLeftMenu'] =  $this->request->post('I_isLeftMenu/d',1);//1帮助菜单  0早报
            $data['I_position'] =  $this->request->post('I_position/d',2);//1底部   2底部上面导航
            $data['I_dodel'] =  $this->request->post('I_dodel/d');
            $data['I_order'] =  $this->request->post('I_order/d');
            if(!$data['Vc_name']){return getJsonStr(500,'栏目名称不能为空！');}
            if(!$data['Vc_intro']){return getJsonStr(500,'简介不能为空！');}
            if(!$data['I_order']){return getJsonStr(500,'排序不能为空！');}
            //验证参数代码是否重复添加
            if(!$this->articleClassModel->checkParam(array('Vc_name'=> $data['Vc_name'],'state'=>1))){
                return getJsonStr(500,'栏目名称已存在！');
            }
            $re1= $this->articleClassModel->save($data);
            $da['id']=$re1;
            $da['Vc_link']="/help/index?class=$re1";
            $re2=$this->articleClassModel->update($da);
            if ($re1 && $re2) {
                $this->addManageLog('栏目管理', '添加栏目ID为'.$re1.'的栏目');
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
            $data['id'] =  $this->request->post('id/d','','htmlspecialchars');
            if(!$data['id']){return getJsonStr(500,'参数有误！');}
              $data['Vc_name'] =  $this->request->post('Vc_name/s','','htmlspecialchars');
              $data['Vc_intro'] =  $this->request->post('Vc_intro/s','','htmlspecialchars');
              $data['I_isLeftMenu'] =  $this->request->post('I_isLeftMenu/d',1);
              $data['I_position'] =  $this->request->post('I_position/d',2);//1底部   2底部上面导航
              $data['I_dodel'] =  $this->request->post('I_dodel/d');
              $data['I_order'] =  $this->request->post('I_order/d');
              if(!$data['Vc_name']){return getJsonStr(500,'栏目名称不能为空！');}
              if(!$data['Vc_intro']){return getJsonStr(500,'简介不能为空！');}
              if(!$data['I_order']){return getJsonStr(500,'排序不能为空！');}
            //验证参数代码是否重复添加
            if(!$this->articleClassModel->checkParamUpdate(array('Vc_name'=> $data['Vc_name'],'state'=>1,'id'=>$data['id']))){
                return getJsonStr(500,'参数代码已存在！');
            }
            $result = $this->articleClassModel->update($data);
            if ($result) {
                $this->addManageLog('栏目管理', '编辑栏目ID为'.$data['id'].'的栏目');
                return getJsonStrSuc('编辑成功');
            } else {
                   return getJsonStr(500,'编辑失败');
            }
        }else{
            $id =  $this->request->get('id/d','','htmlspecialchars');
            $configure = $this->articleClassModel->get($id);
            $this->assign('data',$configure) ;
            return $this->fetch() ;
        }

    }
    
}
