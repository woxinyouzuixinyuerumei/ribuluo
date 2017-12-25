<?php
/**
* 后台Controller类（控制器）
* ======================================
*  liangke.li@monph.com 
*/
namespace app\admin\controller;
use think\Controller;

class Common extends Controller{

    /**
     * 后台控制器初始化
     */
    public function _initialize(){
    	//判断是否已经登陆
		$checkLogin = checkAdminLogin();
		//var_dump($checkLogin);
		//die();	
		if(!$checkLogin['status']){
			$this->redirect('Login/index');
		}
        define('MODULE_NAME',strtolower(request()->controller()));
        define('ACTION_NAME',strtolower(request()->action()));
        /*$role = model('Role');*/
        /*$checkLogin['admininfo']['role'] = $role->getFieldById($checkLogin['admininfo']['role_id'],'name');
        $checkLogin['admininfo']['auth'] = $role->getFieldById($checkLogin['admininfo']['role_id'],'auth');*/
		$this->assign('admininfo',$checkLogin['admininfo']);
		$this->admininfo = $checkLogin['admininfo'];
        //当前管理员权限
        /*$this->adminAuth = explode(',',$checkLogin['admininfo']['auth']);*/
        //权限管理
        if(session('admin_id')>0){
            //当前操作权限ID
            /*$this->HrefId = db('auth_rule')->where('href',MODULE_NAME.'/'.ACTION_NAME)->where('authopen',0)->where('type',0)->value('id');
            if($this->HrefId){*/
               /* if(!in_array($this->HrefId,$this->adminAuth)){
                    $this->error('您无此操作权限','index/body');
                }*/
            /*}*/
        }
    }
}
