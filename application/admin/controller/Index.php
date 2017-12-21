<?php
namespace app\admin\controller;
use think\Controller;

class Index extends Common
{
    public function index(){
        /*汉字转拼音*/
//        $py = new \PY\CUtf8_PY();
//        $name_en = $py->encode('重庆市', 'all');
//        $short_name_en = $py->encode('重庆市');
        return view();
    }

	//首页
    public function body(){
        return view();
    }

    //菜单
    public function meun(){
        $authRule = db('auth_rule')->field('id,href as url,title,icon,parent_id as pid,spread')->where('type=0 and menustatus=1')->order('sort,id asc')->select();
        foreach ($authRule as $key=>&$val){
            $val['url'] = url($val['url']);
            if(!in_array($val['id'],$this->adminAuth)){
                unset($authRule[$key]);
            }
        }
        $result = array();
        $result = list_to_tree($authRule,'id', 'pid','children');
        return json($result);
    }

    //锁屏
    public function doLock(){
        $admin = model('Admin');
        $data = array();
        $data['is_lock'] = 1;
        $rs = $admin->update($data, "id=".$this->userinfo['id']);
        if($rs){
            $this->success('锁屏成功！');
        }else{
            $this->error("锁屏失败，请稍后再试！");
        }
    }

    //解锁
    public function unLock(){
        //var_dump($_POST);
        $password = input('post.password','','strip_tags');

        if(empty($password)){
            $this->error("密码不能为空！");
        }

        $personnel = model('Personnel');
        $personnel_info = $personnel->getInfo("id=".$this->userinfo['id']." and password='".md5($password)."'");

        $company = model('Company');
        $company_status = $company->getFieldById($personnel_info['company_id'],'status');

        if(!$personnel_info || $company_status==0){
            $this->error("密码或状态错误！");
        }
        if($personnel_info['status']==2){
            $this->error("此账号已被锁定！");
        }
        $data = array();
        $data['is_lock'] = 0;
        $rs = $personnel->update($data, "id=".$this->userinfo['id']);
        if($rs){
            $this->success('解锁成功！');
        }else{
            $this->error("解锁失败，请稍后再试！");
        }
    }

	public function upload(){
		//文件名
		$filename = input('get.filename');
		//
        $folder = input('get.folder','home');
        //图片保存路径
        $path = ROOT_PATH.'upload'.DS.$folder.DS.date("Ymd").DS;
		//sleep(10);
		//return json(['data'=>'','code'=>0,'msg'=>'数据错误']);
		
		//获取二进制流
		if(isset($GLOBALS['HTTP_RAW_POST_DATA']))$xmlstr = $GLOBALS['HTTP_RAW_POST_DATA'];
		if(empty($xmlstr)) $xmlstr = file_get_contents('php://input'); 
		
		if(empty($xmlstr)){
			return json(['data'=>'','code'=>0,'msg'=>'数据错误']);
		}
		//图片md5名称
		$filename_md5 = md5(uniqid().mt_rand(10, 99));
		$suffix = getExt($filename);
		$imagename = $filename_md5.".".$suffix;
		//$filename_array = explode('.',$filename);
		//创建文件夹目录
		CheckFolder($path);
		
		$image = $xmlstr;//得到post过来的二进制原始数据
		$file = fopen($path.DS.$imagename,"w");//打开文件准备写入
		fwrite($file,$image);//写入
		fclose($file);//关闭 
		
		$data = ['attachmentName'=>$filename,'attachmentUrl'=>config('url_domain.img').'/'.$folder.'/'.date("Ymd").'/'.$imagename,'imgUrl'=>'/'.$folder.'/'.date("Ymd").'/'.$imagename,'detailNum'=>0];
		return json(['data'=>$data,'code'=>1,'msg'=>'上传图片成功']);
	}
	
	//图片上传
    public function uploadimg(){
        // 获取表单上传文件
        $file = request()->file('image');
        if(empty($file)){
            return json(['data'=>'','code'=>0,'msg'=>'请选择图片']);
        }
        $folder = input('post.folder','home');
        //图片保存路径
        $path = ROOT_PATH.'upload'.DS.$folder.DS;
        //移动到保存路径目录下
        $info = $file->move($path);
        if($info){
            // 成功上传后 获取上传信息
            $savename = str_replace(DS,'/',$info->getSaveName());
            $data = ['attachmentName'=>$info->getFilename(),'attachmentUrl'=>config('url_domain.img').'/'.$folder.'/'.$savename,'imgUrl'=>'/'.$folder.'/'.$savename,'ext'=>$info->getExtension()];
            return json(['data'=>$data,'code'=>1,'msg'=>'上传图片成功']);
        }else{
            // 上传失败获取错误信息
            return json(['data'=>'','code'=>0,'msg'=>$file->getError()]);
        }
    }

    //文件上传
    public function uploadfile(){
        // 获取表单上传文件
        $file = request()->file('file');
        if(empty($file)){
            return json(['data'=>'','code'=>0,'msg'=>'请选择文件']);
        }
        $folder = input('post.folder','file');
        //图片保存路径
        $path = ROOT_PATH.'upload'.DS.$folder.DS;
        //移动到保存路径目录下
        $info = $file->move($path);
        if($info){
            // 成功上传后 获取上传信息
            $savename = str_replace(DS,'/',$info->getSaveName());
            $data = ['fileName'=>$info->getFilename(),'filePath'=>DS.$folder.DS.$info->getSaveName(),'fileUrl'=>'/'.$folder.'/'.$savename,'ext'=>$info->getExtension()];
            return json(['data'=>$data,'code'=>1,'msg'=>'上传文件成功']);
        }else{
            // 上传失败获取错误信息
            return json(['data'=>'','code'=>0,'msg'=>$file->getError()]);
        }
    }
}
