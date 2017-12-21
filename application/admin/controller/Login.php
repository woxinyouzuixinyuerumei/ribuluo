<?php
namespace app\admin\controller;
use think\Controller;

class Login extends Controller{
	
	//登录页面
    public function index(){
		//判断是否已经登陆
		return view();exit;
		$checkLogin = checkAdminLogin();
		if(!$checkLogin['status']){
			return view();
		}else{
			$this->redirect('index/index');
		}
    }
    
	//提交登陆
    public function doLogin(){
    	//var_dump($_POST);
    	$mobile = input('post.mobile','','strip_tags');
		$password = input('post.password','','strip_tags');
        $code = input('post.code','','strip_tags');

        if(!$this->check($code)){
            $this->error("验证码错误！",'',3);
        }

		if(empty($mobile)){
			$this->error("手机号不能为空！",'',1);
		}
		if(!isPhone($mobile)){
			$this->error("请输入正确的手机号！",'',1);
		}
    	if(empty($password)){
			$this->error("密码不能为空！",'',2);
		}


        $admin = model('Admin');
		$admin_info = $admin->getInfo("mobile='".$mobile."' and password='".md5($password)."'");
		/*$company = model('Company');
		$company_status = $company->getFieldById($admin_info['company_id'],'status');*/
		
		if(!$admin_info){
			$this->error("账号或密码错误！",'',2);
		}
		if($admin_info['status']==0){
			$this->error("此账号已被禁用！",'',1);
		}
		
		$scode = md5($admin_info['id'].$admin_info['password'].'c9b7503e7f7c5350acf4edae6fbef3a0');
    
	    //写入SESSION
	    session('admin_id',$admin_info['id']);
        //session('company_id',md5($admin_info['company_id']));
	    session('mobile',$mobile);
	    session('scode',$scode);	    
	    
	    //写入cookie	
	    cookie('admin_id',$admin_info['id'],2592000);
        //cookie('company_id',md5($admin_info['company_id']),2592000);
	    cookie('mobile',$mobile,2592000);
	    cookie('scode',$scode,2592000);
        $data = array();
	    $data['last_login_ip'] = get_client_ip();
	    $data['last_login_time'] = time();
	    $admin->update($data, "id=".$admin_info['id']);
		$this->success('登录成功！', 'index/index');
    }

    //检测验证码
    public function check($code){
        return captcha_check($code);
    }

    //退出登陆
    public function logout(){
    	cookie('admin_id',null);
    	cookie('mobile',null);
    	cookie('scode',null);
    	session('admin_id',null);
    	session('mobile',null);
    	session('scode',null);
    	$this->redirect('login/index');
    }
}