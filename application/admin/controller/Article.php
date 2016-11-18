<?php
namespace app\admin\controller ;
use think\paginator\driver\Bootstrap;
use \app\admin\model\ArticleModel ;
use \app\admin\model\ArticleClassModel ;
use think\db\Query;

class Article extends AdminController {
	private $articleModel,$articleClassModel ;

	public function _initialize() {
		$this->articleModel = new ArticleModel();
		$this->articleClassModel = new ArticleClassModel();
		parent::_initialize();
	}

    public function index() {
    	$currentPage = $this->request->get('page',1) ;
        $param['classID'] = input('classID/d',0);
        $param['keywords'] = input('keywords/s','');
    	$page = $this->articleModel->getPageList($currentPage,$param) ;
    	$this->assign([
            'page'=>$page,
            'param'=>$param,
            'class'=>$this->articleClassModel->getAllArticleClass(),
        ]) ;
    	return $this->fetch("index") ;

//     	http://admin.lightlamp.com/admin/configure/listPage
    }
    public function del() {

        $data['id'] =  $this->request->get('id/d',0,'htmlspecialchars');

        $data['state'] = 0;
        $result  = $this->articleModel->update($data);

        if($result){

            $this->addManageLog('平台内容管理', '删除ID为'.$data['id'].'的平台内容');
            return getJsonStrSuc('删除成功');
        } else {

            return getJsonStr(500,'删除失败');
        }

    }

    public function add() {

        if($this->request->isPost()){

            $data['I_article_classID'] =  $this->request->post('I_article_classID/d');
            $data['Vc_name'] =  $this->request->post('Vc_name/s','','htmlspecialchars');
            $data['Vc_intro'] =  $this->request->post('Vc_intro/s','','htmlspecialchars');
            $data['T_content'] =  $this->request->post('T_content/s','');
            $data['D_releasetime'] =  $this->request->post('D_releasetime/s','','htmlspecialchars');
            $data['I_dodel'] =  $this->request->post('I_dodel/d');
            $data['I_order'] =  $this->request->post('I_order/d');
            if(!$data['I_article_classID']){return getJsonStr(500,'栏目不能为空！');}
            if(!$data['Vc_name']){return getJsonStr(500,'不能为空！');}
            if(!$data['Vc_intro']){return getJsonStr(500,'简介不能为空！');}
            if(!$data['D_releasetime']){return getJsonStr(500,'发布时间不能为空！');}
            if(!$data['I_order']){return getJsonStr(500,'排序不能为空！');}
            if(!$data['T_content']){return getJsonStr(500,'内容不能为空！');}
            if(mb_strlen($data['T_content'],'UTF8')>50000){return getJsonStr(500,'内容超过50000字符限制！');}
            //验证文章是否重复添加
            if(!$this->articleModel->checkParam(array('Vc_name'=> $data['Vc_name'],'I_article_classID'=> $data['I_article_classID'],'state'=>1))){
                return getJsonStr(500,'已存在！');
            }

            $result = $this->articleModel->save($data);
            if ($result) {
                $this->addManageLog('平台内容管理', '添加ID为'.$result.'的平台内容');
                return getJsonStrSuc('新增成功');

            } else {
                return getJsonStr(500,'新增失败');
            }


        }else{
            $articleclass=$this->articleClassModel->getAllArticleClass();
            $this->assign([
                'articleclass'=>$articleclass,
            ]) ;
            return $this->fetch() ;

        }

    }
    public function edit() {
          if($this->request->isPost()){
            $data['id'] =  $this->request->post('id/d','','htmlspecialchars');
            if(!$data['id']){return getJsonStr(500,'参数有误！');}
              $data['I_article_classID'] =  $this->request->post('I_article_classID/d');
              $data['Vc_name'] =  $this->request->post('Vc_name/s','','htmlspecialchars');
              $data['Vc_intro'] =  $this->request->post('Vc_intro/s','','htmlspecialchars');
              $data['T_content'] =  $this->request->post('T_content/s','');
              $data['D_releasetime'] =  $this->request->post('D_releasetime/s','','htmlspecialchars');
              $data['I_dodel'] =  $this->request->post('I_dodel/d');
              $data['I_order'] =  $this->request->post('I_order/d');
              if(!$data['I_article_classID']){return getJsonStr(500,'栏目不能为空！');}
              if(!$data['Vc_name']){return getJsonStr(500,'不能为空！');}
              if(!$data['Vc_intro']){return getJsonStr(500,'简介不能为空！');}
              if(!$data['D_releasetime']){return getJsonStr(500,'发布时间不能为空！');}
              if(!$data['I_order']){return getJsonStr(500,'排序不能为空！');}
              if(!$data['T_content']){return getJsonStr(500,'内容不能为空！');}
              if(mb_strlen($data['T_content'],'UTF8')>50000){return getJsonStr(500,'内容超过50000字符限制！');}
            //验证文章是否重复添加
            if(!$this->articleModel->checkParamUpdate(array('Vc_name'=> $data['Vc_name'],'I_article_classID'=> $data['I_article_classID'],'state'=>1,'id'=>$data['id']))){
                return getJsonStr(500,'文章已存在！');
            }
            $result = $this->articleModel->update($data);
            if ($result) {
                $this->addManageLog('平台内容管理', '编辑ID为'.$data['id'].'的平台内容');
                return getJsonStrSuc('编辑成功');
            } else {
                   return getJsonStr(500,'编辑失败');
            }
        }else{
            $id =  $this->request->get('id/d','','htmlspecialchars');
            $configure = $this->articleModel->get($id);
            $this->assign([
                'data'=>$configure,
                'articleclass'=>$this->articleClassModel->getAllArticleClass(),
            ]) ;
            return $this->fetch() ;
        }

    }

}
