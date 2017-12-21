<?php
namespace app\admin\controller;
use think\Controller;

class Auth extends Common
{
    public function index(){
        if(request()->isPost()){
            $auth_rule = model('AuthRule');
            $where['type'] = input('param.type');
            $list = $auth_rule->where($where)->order('sort,id asc')
                ->select();
            $data = menu($list);
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$data,'rel'=>1];
        }
        $type = input('param.type') ? input('param.type'):0;
        $this->assign('type',$type);
        return view();
    }
    //添加
    public function addData(){
        if(request()->isPost()){
            $auth_rule = model('AuthRule');
            $data = input('post.');
            if($data['parent_id']>0){
                $level = $auth_rule->getFieldById($data['parent_id'],'level');
                $data['level'] = $level+1;
            }else $data['level'] = 1;
            $data['addtime'] = time();

            $rs = $auth_rule->save($data);
            if($rs){
                $this->success('添加成功！','index');
            }else{
                $this->error("添加失败！");
            }
        }else{
            $type = input('type')?input('type'):0;
            $auth_rule = model('AuthRule');
            $list = $auth_rule->order('sort,id asc')
                ->select();
            $data = menu($list);
            $this->assign('type',$type);  //权限类型
            $this->assign('lists',$data); //权限列表
            return $this->fetch();
        }
    }
    //编辑
    public function editData(){
        if(request()->isPost()) {
            $auth_rule = model('AuthRule');
            $data = input('post.');
            if($data['parent_id']>0){
                $level = $auth_rule->getFieldById($data['parent_id'],'level');
                $data['level'] = $level+1;
            }else $data['level'] = 1;
            $id = $data['id'];
            unset($data['id']);
            $rs = $auth_rule->save($data,array('id'=>$id));
            if($rs!==false){
                $this->success('保存成功！','index');
            }else{
                $this->error("保存失败！");
            }
        }else{
            $auth_rule = model('AuthRule');
            $list = $auth_rule->order('sort,id asc')
                ->select();
            $data = menu($list);
            $this->assign('lists',$data);//权限列表

            $ruleinfo = $auth_rule->where(['id'=>input('id')])->field('id,href,title,parent_id,icon,type,sort,menustatus')->find();
            $this->assign('info',$ruleinfo);
            return $this->fetch();
        }
    }
    //修改是否验证权限
    public function setOpen(){
        $id = input('post.id');
        $auth_rule = model('AuthRule');
        $statusone = $auth_rule->where(array('id'=>$id))->value('authopen');//判断当前状态情况
        if($statusone==1){
            $data = array('authopen'=>0);
            $rs = $auth_rule->where(array('id'=>$id))->setField($data);
            $result['authopen'] = 0;
        }else{
            $data = array('authopen'=>1);
            $rs = $auth_rule->where(array('id'=>$id))->setField($data);
            $result['authopen'] = 1;
        }
        if($rs!==false){
            $this->success('修改成功！','',$result);
        }else{
            $this->error("修改失败！",'',$result);
        }
    }
    //修改菜单状态
    public function setStatus(){
        $id = input('post.id');
        $auth_rule = model('AuthRule');
        $statusone = $auth_rule->where(array('id'=>$id))->value('menustatus');//判断当前状态情况
        if($statusone==1){
            $data = array('menustatus'=>0);
            $rs = $auth_rule->where(array('id'=>$id))->setField($data);
            $result['menustatus'] = 0;
        }else{
            $data = array('menustatus'=>1);
            $rs = $auth_rule->where(array('id'=>$id))->setField($data);
            $result['menustatus'] = 1;
        }
        if($rs!==false){
            $this->success('修改成功！','',$result);
        }else{
            $this->error("修改失败！",'',$result);
        }
    }
    //修改排序
    public function setOrder(){
        $auth_rule = model('AuthRule');
        $data = input('post.');
        $rs = $auth_rule->update($data);
        if($rs!==false){
            $this->success('排序更新成功！','index');
        }else{
            $this->error("排序更新失败！");
        }
    }
    //删除
    public function delData(){
        $id = input('post.id');
        $auth_rule = model('AuthRule');
        $rs = $auth_rule->where(array('id'=>$id))->delete();
        if($rs!==false){
            $this->success('删除成功！');
        }else{
            $this->error("删除失败！");
        }
    }
}
