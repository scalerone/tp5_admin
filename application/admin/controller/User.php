<?php
namespace app\admin\controller ;
use think\paginator\driver\Bootstrap;
use app\common\model\UserModel;

use think\db\Query;
use \think\Request;
class User extends AdminController {
	private $userModel;
	
	public function _initialize() {
		$this->userModel = new UserModel();
		parent::_initialize();
	}
	
    public function listPage($page=1) {
       
        $param['uname'] = input('uname/s','');
        $param['certifyStatus'] = input('certifyStatus/d',0);
    	$pages = $this->userModel->getPage($page,$param) ;
    	 $this->assign([
	       
	        'user' =>$this->userModel,
	        'page' =>$pages,
	        'param'=>$param,
    	     
	    ]) ;
    	return $this->fetch("index") ;

    }

    /**
     * 禁用/启用
     */
    public function changeAjax(){
        $id = $this->request->get('id',0);
        $st = $this->request->get('st',0);//默认为禁用
        if(!$id){
            return getJsonStrError('信息未选择');
        }
    
        $uprow = $this->userModel->updateDataById($id, array('I_islock'=>$st>0?1:0));
        if($uprow > 0){
            if($st){
    
                $this->addManageLog('会员管理', '对ID为'.$id.'的会员进行了启用(解冻)');
            }else{
    
                $this->addManageLog('会员管理', '对ID为'.$id.'的会员进行了禁用(冻结)');
            }
            return getJsonStrSuc('更改成功');
        }
        return getJsonStrError('更改失败');
    }
    
    /**
     * 删除用户--改变state状态即可
     */
    public function delAjax(){
        $id = $this->request->get('id',0);
        if(!$id){
            return getJsonStrError('信息未选择');
        }
        $uprow = $this->userModel->updateDataById($id,array('state'=>0));
        if($uprow > 0){
            $this->addManageLog('会员管理', '删除了ID为'.$id.'的会员');
            return getJsonStrSuc('删除成功');
        }
        return getJsonStrError('删除失败');
    }
    
    public function  info(){
        
        $id = $this->request->get('id',0);
        if(!$id){
            $this->error('信息未选择');
        }
        $data =  $this->userModel->getById($id);
        $this->assign([
            'user' =>$this->userModel,
            'data'=>$data,
        ]) ;
        return $this->fetch();
        
        
    }
   
    public function  edit(){
        
        if($this->request->isPost()){
            $id = input('post.id/d',0);
            $uid = input('post.uid/d',0);
            $I_status = input('post.approved/d',0);
            
            if(!$id){
                return getJsonStrError('不合法的请求');
            }
            
            if(!$I_status){
                return getJsonStrError('未选择审核状态！');
            }
            $uprow = db('sm_user_company')->where('id',$id)->setField(array('I_status'=>$I_status));
            if($uprow > 0){
                $this->addManageLog('会员管理', '认证审核了ID为'.$uid.'的会员认证申请');
                return getJsonStrSuc('审核成功');
            }else{
                
                return getJsonStrError('审核失败');
            }
            
        }else{
        
            $id = $this->request->get('id',0);
            if(!$id){
                $this->error('不合法的请求');
            }
            $data =  $this->userModel->getCompanyById($id);
            $this->assign([
                'user' =>$this->userModel,
                'data'=>$data,
            ]) ;
            return $this->fetch();
        }
        
    }
   

   

    
    
    
}
