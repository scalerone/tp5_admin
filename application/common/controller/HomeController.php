<?php
namespace app\common\controller ;

use think\Request;
use app\common\controller\BaseController;
use app\common\model\message\SiteMessage;

class HomeController extends BaseController {
	
	use \app\common\traits\Jump ;
	
	public  $request;
	
	public function _initialize() {
	    $this->request = Request::instance() ;
	}
	public function __construct(){
		parent::__construct();
		$this->getBottonArticles();
		$this->assign([
			'articles'=>cache('articles'),
		]);
		if(!cache('HotTel')){
			db('configure')->where("code='HotTel'")->cache('HotTel',3600)->value('value');
		}
	}

	/**
	 * 生成底部文章cache
	 * @return mixed
	 */
	public function getBottonArticles(){
		if(!cache('articles')){
			$re1=db('sm_article_class')->field('id,Vc_name,I_position')->where(['state'=>1,'I_isLeftMenu'=>1])->order('I_order','desc')->select();
			foreach ($re1 as $k=>$r){
				$re2=db('sm_article')->field('id,Vc_name')->table('sm_article')->where(['state'=>1,'I_article_classID'=>$r['id']])->order('I_order','desc')->select();
				$re1[$k]['contents']=$re2;
			}
			cache('articles',$re1,120);
		}
	}
	/**
	 * 检查用户登录
	 */
	protected function check_login(){
	    if(!isset($_SESSION["user"])){
	    	$isAjax = $this->request->isAjax() ;
	        if (!$isAjax) {
	        	$_SESSION['login_http_referer'] = $_SERVER['REQUEST_URI'];
	        }
//			$this->redirect('user/login', []);
	        $this->error('您还没有登录！',url('user/login'), ['code'=>401]);
	    }
	
	}
	/**
	 * 检查用户状态
	 */
	protected function  check_user(){
	    //$user_status=db('sm_user')->where(array("id"=>$this->getSessionUid()))->value("I_islock");
	    if (isset($_SESSION['user']) && isset($_SESSION['I_islock'])) {
		    $user_status = $_SESSION["user"]['I_islock'];
		    if($user_status==1){
		        $this->error('此账号已经被禁止使用，请联系管理员！',"/", ['code'=>403]);
		    }
	    }
	}
	
	/**
	 * 获取session用户
	 * @return user
	 */
	protected function getSessionUser(){
		return $_SESSION['user'];
	}
	/**
	 * 获取session用户ID
	 * @return uid
	 */
	protected function getSessionUid(){
		$user = $this->getSessionUser();
		return $user['id'];
	}
	/**
	 * 获取session用户名称
	 */
	protected function getSessionUname(){
		$user = $this->getSessionUser();
		return iseta($user,'Vc_name');
	}
	/**
	 * 检查是否有未读消息
	 */
	protected function checkUnreadMessage() {
		$uid = $this->getSessionUid() ;
		$unread = false ;
		if ($uid) {
			$sm = new SiteMessage() ;
			$r = $sm->getMessageList($uid, 1, 0, true) ;
			$unread = $r['count'] ;
			$_SESSION['user']['unread'] = $unread ;
		}
	}
	
}