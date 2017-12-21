<?php
namespace app\admin\controller;
use think\Controller;

class Admin extends Common
{
    public function index(){
        if(request()->isPost()){
            $key = input('post.key');
            $page = input('page')?input('page'):1;
            $pageSize = input('limit')?input('limit'):config('paginate.list_rows');
            $admin = model('Admin');
            $list = $admin->where('name|mobile','like',"%".$key."%")
                ->order('id desc')
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
            foreach ($list['data'] as $k=>$v){
                $list['data'][$k]['role'] = model('Role')->where('id',$v['role_id'])->value('name');
            }
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
        }
        return view();
    }
    //添加
    public function addData(){
        if(request()->isPost()){
            $admin = model('Admin');
            $password = input('post.password','','strip_tags');
            $data = input('post.');
            $data['password'] = md5($password);
            $data['addtime'] = time();
            $rs = $admin->save($data);
            if($rs){
                //写入日志
                //addCommitLog($this->userinfo['company_id'],$this->userinfo['id'],MODULE_NAME.'/'.ACTION_NAME,$personnel->id,$data,'添加');
                $this->success('添加成功！','index');
            }else{
                $this->error("添加失败！");
            }
        }else{
            $role = model('Role');
            $list = $role ->order('id')->column('name','id');
            $this->assign('list',$list);
            return $this->fetch();
        }
    }
    //编辑
    public function editData(){
        if(request()->isPost()) {
            $admin = model('Admin');
            $data = input('post.');
            $password = input('post.password','','strip_tags');
            if($password == "123456"){
                unset($data['password']);
            }else{
                $data['password'] = md5($password);
            } 
            $id = $data['id'];
            unset($data['id']);
            $rs = $admin->save($data,array('id'=>$id));
            if($rs!==false){
                //写入日志
                //addCommitLog($this->userinfo['company_id'],$this->userinfo['id'],MODULE_NAME.'/'.ACTION_NAME,$id,$data,'编辑');
                $this->success('保存成功！','index');
            }else{
                $this->error("保存失败！");
            }
        }else{
            $admin = model('Admin');
            $role = model('Role');
            $info = $admin->where(array('id'=>input('id')))->field('id,name,mobile,role_id,status')->find();
            $list = $role ->order('id')->column('name','id');
            $this->assign('list',$list);
            $this->assign('info',$info);
            return $this->fetch();
        }
    }
    //重置密码
    public function resetPw(){
        $id = input('post.id');
        $admin = model('Admin');
        $password = rand_string(6,1);
        $data = array('password'=>md5($password));
        $rs = $admin->where(array('id'=>$id))->setField($data);
        if($rs!==false){
            //写入日志
            //addCommitLog($this->userinfo['company_id'],$this->userinfo['id'],MODULE_NAME.'/'.ACTION_NAME,$id,$data,'重置密码');
            $this->success('重置成功','',$password);
        }else{
            $this->error("重置失败");
        }
    }
    
    //修改状态
    public function setStatus(){
        $id = input('post.id');
        $admin = model('Admin');
        $statusone = $admin->where(array('id'=>$id))->value('status');//判断当前状态情况
        if($statusone==1){
            $data = array('status'=>0);
            $rs = $admin->where(array('id'=>$id))->setField($data);
            $result['status'] = 0;
        }else{
            $data = array('status'=>1);
            $rs = $admin->where(array('id'=>$id))->setField($data);
            $result['status'] = 1;
        }
        if($rs!==false){
            //写入日志
            //addCommitLog($this->userinfo['company_id'],$this->userinfo['id'],MODULE_NAME.'/'.ACTION_NAME,$id,$data,'修改状态');
            $this->success('修改成功！','',$result);
        }else{
            $this->error("修改失败！",'',$result);
        }
    }
    //删除
    public function delData(){
        $id = input('post.id');
        $personnel = model('Personnel');
        $rs = $personnel->where(array('id'=>$id,'company_id'=>$this->userinfo['company_id']))->delete();
        if($rs!==false){
            //写入日志
            //addCommitLog($this->userinfo['company_id'],$this->userinfo['id'],MODULE_NAME.'/'.ACTION_NAME,$id,'删除');
            $this->success('删除成功！');
        }else{
            $this->error("删除失败！");
        }
    }
    //删除选中
    public function delChoice(){
        $ids = input('post.ids/a');
        $map = array(
            'id' => array('in',$ids),
        );
        $admin = model('Admin');
        $rs = $admin->where($map)->delete();
        if($rs!==false){
            //写入日志
            //addCommitLog($this->userinfo['company_id'],$this->userinfo['id'],MODULE_NAME.'/'.ACTION_NAME,$ids,input('post.'),'删除');
            $this->success('删除成功！','index');
        }else{
            $this->error("删除失败！");
        }
    }
    //修改密码
    public function editPw(){
        if(request()->isPost()) {
            $admin = model('Admin');
            $oldpassword = input('post.oldpassword','','strip_tags');
            $password = input('post.password','','strip_tags');
            $verifypassword = input('post.verifypassword','','strip_tags');
            if(empty($oldpassword)){
                $this->error("原密码不能为空！");
            }
            if(md5($oldpassword)!=$this->admininfo['password']){
                $this->error("原密码错误！");
            }
            if(empty($password)){
                $this->error("新密码不能为空！");
            }
            if(empty($verifypassword)){
                $this->error("确认密码不能为空！");
            }
            if($password!=$verifypassword){
                $this->error("新密码和确认密码不一致！");
            }
            $data = array('password'=>md5($password));
            $rs = $admin->where(array('id'=>$this->admininfo['id']))->setField($data);
            if($rs!==false){
                //写入日志
                //addCommitLog($this->userinfo['company_id'],$this->userinfo['id'],MODULE_NAME.'/'.ACTION_NAME,$this->userinfo['id'],$data,'修改密码');
                $this->success('修改成功！','login/logout');
            }else{
                $this->error("修改失败！");
            }
        }else{
            return $this->fetch();
        }
    }
    //个人资料
    public function editInfo(){
        if(request()->isPost()) {
            $admin = model('Admin');
            $data = input('post.');
            unset($data['name']);
            unset($data['role_id']);
            unset($data['image']);
            $rs = $admin->where(array('id'=>$this->admininfo['id']))->update($data);
            echo $rs;
            exit;
            if($rs!==false){
                //写入日志
                //addCommitLog($this->userinfo['company_id'],$this->userinfo['id'],MODULE_NAME.'/'.ACTION_NAME,$this->userinfo['id'],$data,'修改个人资料');
                $this->success('修改成功！','login/logout');
            }else{
                $this->error("修改失败！");
            }
        }else{
            return $this->fetch();
        }
    }
}
