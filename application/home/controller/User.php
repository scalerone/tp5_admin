<?php
namespace app\home\controller ;

use app\common\model\UserModel;
use app\common\model\SysconfigModel;
use app\common\controller\HomeController;
use think\captcha\Captcha;
use think\Config;
use think\Validate;
use app\common\model\message\UserMessageConfig;
use app\common\model\message\MessageService;
class User extends HomeController {

	private $userModel,$messageService;

	public function __construct() {
		$this->userModel = new UserModel() ;
		$this->messageService = new MessageService();
		parent::__construct() ;
	}

	public function register () {

		return $this->fetch("register") ;
	}
	public function captcha ($id = "") {
	    $captcha = new Captcha((array)Config::get('captcha'));
	    return $captcha->entry($id);
	}



	//发送短信验证码
	public function sendsms(){

	    $phone = input('post.mobile/s','');
	    if(!funcphone($phone)){
	        return getJsonStr(500,'手机号码不正确!');
	    }
	    $r = $this->messageService->sendCode($phone);

	    return getJsonStrSuc('发送成功');

	}

	/**
	 * 忘记密码时发送验证码
	 * @return \think\response\Json
	 */
	public function sendforgetsms () {
		$phone = input('post.mobile/s','');
		if(!funcphone($phone)){
			return getJsonStr(500,'手机号码不正确!');
		}
		$isReg = db('sm_user')->where('Vc_mobile', $phone)->find() ;
		if (!$isReg) {
			return getJsonStrError('手机号未注册!') ;
		}
		$r = $this->messageService->sendCode($phone);
		
		return getJsonStrSuc('发送成功');
	}


	public function doRegister(){


	    //表单基础数据验证
	    $rules = [
	        ['mobile','require|length:11','手机号必填|手机号是11位'],
	        ['sms_code','require','手机验证码必填'],
	        ['password','require|length:6,18','密码不能为空|密码长度为6到18位'],
	        ['accept','require','请同意服务协议']

	    ];

	    $data=array();
	    $data['mobile'] = input('post.mobile/s','');
	    $data['password'] = input('post.password/s','');
	    $data['sms_code'] = input('post.sms_code/s','');
	    $data['accept'] = input('post.accept/s','');

	    $validate = new Validate($rules);
	    $result   = $validate->check($data);

	    if(!$result){
	        return getJsonStr(500,$validate->getError());
	    }
	    if(!funcphone($data['mobile'])){
	       return getJsonStr(500,'手机号码不正确!');
	     }

	     //用户名验证

	     $username = input('post.name/s','');
	     if($username){

	         $map['Vc_name']=$username;
	         $map['state']=1;
	         $find = $this->userModel->where($map)->count();
	         if($find){
	             return getJsonStr(501,'该用户名已被使用');
	         }

	     }


	     //手机验证码验证
	     $validateCode = $this->messageService->validateCode($data['sms_code']);
 	    if(!$validateCode){
 	        return getJsonStr(501,'短信验证码有误或已失效');
 	     }else{

 	     $this->messageService->clearCode();

 	     }



	    //已注册验证
	    $where['Vc_mobile']=$data['mobile'];
	    $where['state']=1;
	    $rs = $this->userModel->where($where)->count();

	    if($rs){
	        return getJsonStr(503,'手机号已被注册！');
	    }else{
	        $da=array(
	            'Vc_name' => $username,
	            'Vc_Email' => input('post.email/s',''),
	            'Vc_mobile' =>$data['mobile'],
	            'Vc_QQ' =>input('post.qq/s',''),
	            'Vc_tel' =>input('post.tel/s',''),
	            'Vc_password' => sp_password($data['password']),
	            'Vc_lastloginIP' => getIp(),
	            'Dt_lastlogintime' => date("Y-m-d H:i:s"),
	            'I_mobileauthenticate' => 1,
	        );
	        $rst = $this->userModel->save($da);
	        if($rst){
	            //登入成功页面跳转
	            $da['id']=$rst;
	            $_SESSION['user']=$this->userModel->getById($rst);
	            $umc = new UserMessageConfig($rst) ;
	            $umc->createConfig() ;
				$url=cookie('formUrl')?cookie('formUrl'):'';
	            return getJsonStrSuc('注册成功',[],$url);
	        }else{
	            return getJsonStr(504,'注册失败',[],url("user/register"));
	        }

	    }
	}
	public function login () {

	    return $this->fetch("login") ;
	}
	//登录验证

	public  function doLogin(){

	    $rules = [
	        ['username','require','手机号/用户名不能为空！'],
	        ['password','require','密码不能为空！']

	    ];
	    $da=array();
	    $da['username']=input('post.username','');
	    $da['password']=input('post.password','');
	    $validate = new Validate($rules);
	    $res   = $validate->check($da);
	    if(!$res){
	        return getJsonStr(500,$validate->getError());
	    }

	    if(preg_match('/^\d+$/', $da['username'])){//手机号登录
	        $where['Vc_mobile']=$da['username'];
	    }else{
	       $where['Vc_name']=$da['username']; // 用户名登录
	    }
	    $where['state'] = 1;
// 	    $where['I_islock'] = 0;
	    $password=$da['password'];
	    $result = $this->userModel->where($where)->find();
	    if($result['I_islock']){
	        return getJsonStr(500,"您的账号已被冻结，请联系客服！");
	    }
	    

	    if(!empty($result)){
	        if(sp_compare_password($password, $result['Vc_password'])){
	            $_SESSION["user"]=$result->toArray();
	            $user = $this->userModel->getById($this->getSessionUid());
	            $_SESSION["user"]['I_status'] = $user['I_status'];
				$this->checkUnreadMessage() ;
	            //写入此次登录信息
	            $data = array(
	                'Dt_lastlogintime' => date("Y-m-d H:i:s"),
	                'Vc_lastloginIP' => getIp(),
	            );
	            $this->userModel->where(array('id'=>$result["id"]))->update($data);
	            $redirect=empty($_SESSION['login_http_referer'])?"/":$_SESSION['login_http_referer'];
	            $_SESSION['login_http_referer']="";
	            return getJsonStrSuc('登录验证成功！',$_SESSION["user"],$redirect);
	        }else{

	        return getJsonStr(500,"密码错误！");

	        }
	    }else{
	        return getJsonStr(500,"用户名不存在！");
	    }
	}
	/**
	 * 退出登录
	 *
	 */
	public function loginout(){
	    session('user',null);
	    session(null);
	    return $this->fetch('user/login');
	}

	/**
	 * 忘记密码
	 */

	public function forgetpwd(){
		$this->assign([
			'title'=>'找回密码',
		]) ;
	    return $this->fetch('forgetpwd');
	}
	/**
	 * 密码重置
	 */
	public function doForgetpwd(){


	    //表单基础数据验证
	    $rules = [
	        ['mobile','require|length:11','手机号必填|手机号是11位'],
	        ['sms_code','require','手机验证码必填'],
	        ['password','require|length:6,18','请输入新密码|密码长度为6到18位'],
	        ['repassword','require|confirm:password','确认新密码不能为空|两次输入密码不一致']

	    ];

	    $data=array();
	    $data['mobile'] = input('post.mobile/s','');
	    $data['password'] = input('post.password/s','');
	    $data['repassword'] = input('post.repassword/s','');
	    $data['sms_code'] = input('post.sms_code/s','');

	    $validate = new Validate($rules);
	    $result   = $validate->check($data);

	    if(!$result){
	        return getJsonStr(500,$validate->getError());
	    }
	    if(!funcphone($data['mobile'])){
	        return getJsonStr(500,'手机号码不正确!');
	    }

	    //手机验证码验证
	    $validateCode = $this->messageService->validateCode($data['sms_code']);
	    if(!$validateCode){
	        return getJsonStr(501,'短信验证码有误或已失效');
	    }else{

 	     $this->messageService->clearCode();

 	     }

	    //已注册验证
	    $where['Vc_mobile']=$data['mobile'];
	    $where['state']=1;
	    $rs = $this->userModel->where($where)->value('id');


	    if(!$rs){
	        return getJsonStr(504,'手机号还未注册,不能进行密码重置！');
	    }else{
	        $da=array(
	            'Vc_password' => sp_password($data['password']),
	            'Vc_lastloginIP' => getIp(),
	            'Dt_lastlogintime' => date("Y-m-d H:i:s"),
	        );
	        $rst = $this->userModel->update($da,['Vc_mobile'=>$data['mobile']]);
	        if($rst){


	            $this->messageService->sendUserCodeReset($rs, $data['mobile'], date("Y-m-d H:i"));

	            return getJsonStrSuc('修改密码成功');

	        }else{
	            return getJsonStr(500,'修改密码失败',[],url("user/forgetpwd"));
	        }

	    }




	}


	/**
	 * 网站验证码校验---通过后才能获取手机验证码
	 */
	public function  doCaptcha(){

	    $code = input('code/s','');
	    $id = input('codeid',0);

	    if(!captcha_check($code,$id)){
	        return getJsonStr(500,'验证码错误');
	    }else{

	        return getJsonStrSuc('验证码正确！');
	    }

    }
	/**
	 * 手机验证码验证
	 */
    public function  doMobilecode($sms_code){


        //手机验证码验证
        $validateCode = $this->messageService->validateCode($sms_code);
        if(!$validateCode){
            return getJsonStr(501,'短信验证码有误或已失效');
        }else{
            return getJsonStrSuc('短信验证码正确！');
        }

    }
	/**
	 * 手机号码验证-是否已注册
	 */
    public function  doMobile($mobile){

        $where['Vc_mobile']=$mobile;
        $where['state']=1;
        $rs = $this->userModel->where($where)->count();
        if($rs){
            return getJsonStr(501,'手机号已被注册');
        }else{
            return getJsonStrSuc('未注册');
        }

    }


}
