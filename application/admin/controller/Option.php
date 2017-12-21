<?php 
namespace app\admin\controller;
use think\Controller;

Class Option extends Common{
	public function index(){
		if(request()->isPost()){
            $parent_id = input('post.parent_id') ? input('post.parent_id'):1;
            $where['parent_id'] = $parent_id;
            $option = model('Option');
            $list = $option->order('sort,id asc')->select();
            $data = $this->menu($list,$parent_id);
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$data,'rel'=>1];
        }
        $option = model('Option');
        $info = $option->field('id,title')->where('parent_id',0)->select();
        $this->assign('info',$info);
		return view();
	}
	//处理数据
    static public function menu($data , $pid=0, $lefthtml = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'){
        $arr = array();
        foreach ($data as $v){
            if($v['parent_id']==$pid){
            	$v['lefthtml'] = str_repeat($lefthtml,$v['level']-1);
            	$v['title'] = $v['lefthtml'].'<i class="larry-icon">&#xe9c8;</i>&nbsp;&nbsp;'.$v['title'];
                $arr[]=$v;
                $arr = array_merge($arr,self::menu($data,$v['id'],$lefthtml));
            }
        }
        return $arr;
    }
	//修改选项状态
    public function setStatus(){
        $id = input('post.id');
        $option = model('Option');
        $statusone = $option->where(array('id'=>$id))->value('status');//判断当前状态情况
        if($statusone==1){
            $data = array('status'=>0);
            $rs = $option->where(array('id'=>$id))->setField($data);
            $result['status'] = 0;
        }else{
            $data = array('status'=>1);
            $rs = $option->where(array('id'=>$id))->setField($data);
            $result['status'] = 1;
        }
        if($rs!==false){
            $this->success('修改成功！','',$result);
        }else{
            $this->error("修改失败！",'',$result);
        }
    }

    //修改排序
    public function setOrder(){
        $option = model('Option');
        $data = input('post.');
        $rs = $option->update($data);
        if($rs!==false){
            $this->success('排序更新成功！','index');
        }else{
            $this->error("排序更新失败！");
        }
    }

	public function adddata(){
		if(request()->isPost()){
            $option = model('Option');
            $data = input('post.');
            if($data['parent_id']>0){
                $level = $option->getFieldById($data['parent_id'],'level');
                $data['level'] = $level+1;
            }else $data['level'] = 1;
            $data['addtime'] = time();
            $rs = $option->save($data);
            if($rs){
                $this->success('添加成功！','index');
            }else{
                $this->error("添加失败！");
            }
        }else{
            $option = model('Option');
            $list = $option->order('sort,id asc')  //->field('id,title,parent_id,level')
                ->select();
            $id = input('get.id');
            $list = $this->menu($list);
            $this->assign('id',$id);
            $this->assign('lists',$list);//选项列表
            return $this->fetch();
        }
	}
	//编辑
    public function editData(){
        if(request()->isPost()) {
            $option = model('Option');
            $data = input('post.');
            if($data['parent_id']>0){
                $level = $option->getFieldById($data['parent_id'],'level');
                $data['level'] = $level+1;
            }else $data['level'] = 1;
            $id = $data['id'];
            unset($data['id']);
            $rs = $option->save($data,array('id'=>$id));
            if($rs!==false){
                $this->success('保存成功！','index');
            }else{
                $this->error("保存失败！");
            }
        }else{
            $option = model('Option');
            $list = $option->where('id','neq',input('id'))->order('sort,id asc')
                ->select();
            $list = $this->menu($list);
            $this->assign('lists',$list);//选项列表
            $info = $option->where(['id'=>input('id')])->field('id,title,type,parent_id,icon,sort,status,remark')->find();
            $this->assign('info',$info);
            return $this->fetch();
        }
    }

    //删除
    public function delData(){
        $id = input('post.id');
        $option = model('Option');
        $rs = $option->where(array('id'=>$id))->delete();
        if($rs!==false){
            $this->success('删除成功！');
        }else{
            $this->error("删除失败！");
        }
    }
}













 ?>