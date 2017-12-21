<?php 
	namespace app\admin\controller;
	use think\Controller;
	class Role extends Common {

		public function index(){
	        if(request()->isPost()){
	            $page = input('page')?input('page'):1;
	            $pageSize = input('limit')?input('limit'):config('paginate.list_rows');
	            $role = model('Role');
	            $list = $role->order('id')
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
	            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
	        }
	        return view();
	    }

	    //设置权限
	    public function setAuth(){
	        if(request()->isPost()) {
	            $id = input('post.id');
	            $role = model('Role');
	            $auth = input('post.rules');
	            $auth = ltrim($auth,'0,');
	            //$auth = rtrim($auth,',');
	            $data = array('auth'=>','.$auth);
	            $rs = $role->where(array('id'=>$id))->setField($data);
	            if($rs!==false){
	                //写入日志
	                //addCommitLog($this->userinfo['company_id'],$this->userinfo['id'],MODULE_NAME.'/'.ACTION_NAME,$id,$data,'设置权限');
	                $this->success('设置成功！');
	            }else{
	                $this->error("设置失败！");
	            }
	        }else{
	            $role = model('Role');
	            $admin_rule = db('auth_rule')->field('id,parent_id as pid,title')->where('type',0)->order('sort,id asc')->select();
	            $rules = $role->where('id',input('id'))->value('auth');
	            $arr = $this->auth($admin_rule,$pid=0,$rules);
	            $arr[] = array(
	                "id"=>0,
	                "pid"=>0,
	                "title"=>"全部",
	                "open"=>true
	            );
	            $this->assign('data',json_encode($arr,true));
	            return $this->fetch();
	        }
	    }
	    //处理数据
	    static public function auth($cate , $pid=0,$rules){
	        $arr=array();
	        $rulesArr = explode(',',$rules);
	        foreach ($cate as $v){
	            if($v['pid']==$pid){
	                if(in_array($v['id'],$rulesArr)){
	                    $v['checked']=true;
	                }
	                $v['open']=true;
	                $arr[]=$v;
	                $arr= array_merge($arr,self::auth($cate, $v['id'],$rules));
	            }
	        }
	        return $arr;
	    }

	    //修改状态
	    public function setStatus(){
	        $id = input('post.id');
	        $role = model('Role');
	        $statusone = $role->where(array('id'=>$id))->value('status');//判断当前状态情况
	        if($statusone==1){
	            $data = array('status'=>0);
	            $rs = $role->where(array('id'=>$id))->setField($data);
	            $result['status'] = 0;
	        }else{
	            $data = array('status'=>1);
	            $rs = $role->where(array('id'=>$id))->setField($data);
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

	    //添加
	    public function addData(){
	        if(request()->isPost()){
	            $role = model('Role');
	            $data = input('post.');
	            $data['addtime'] = time();
	            $rs = $role->save($data);
	            if($rs){
	                //写入日志
	                //addCommitLog($this->userinfo['company_id'],$this->userinfo['id'],MODULE_NAME.'/'.ACTION_NAME,$personnel->id,$data,'添加');
	                $this->success('添加成功！','index');
	            }else{
	                $this->error("添加失败！");
	            }
	        }else{
	            return $this->fetch();
	        }
	    }

	    //编辑
	    public function editData(){
	        if(request()->isPost()) {
	            $role = model('Role');
	            $data = input('post.');
	            $id = $data['id'];
	            unset($data['id']);
	            $rs = $role->save($data,array('id'=>$id));
	            if($rs!==false){
	                //写入日志
	                //addCommitLog($this->userinfo['company_id'],$this->userinfo['id'],MODULE_NAME.'/'.ACTION_NAME,$id,$data,'编辑');
	                $this->success('保存成功！','index');
	            }else{
	                $this->error("保存失败！");
	            }
	        }else{
	            $role = model('Role');
	            $info = $role->where(array('id'=>input('id')))->field('id,name,auth,status')->find();
	            $this->assign('info',$info);
	            return $this->fetch();
	        }
	    }

	    //删除
	    public function delData(){
	        $id = input('post.id');
	        $role = model('Role');
	        $rs = $role->where(array('id'=>$id))->delete();
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
	        $role = model('Role');
	        $rs = $role->where($map)->delete();
	        if($rs!==false){
	            //写入日志
	            //addCommitLog($this->userinfo['company_id'],$this->userinfo['id'],MODULE_NAME.'/'.ACTION_NAME,$ids,input('post.'),'删除');
	            $this->success('删除成功！','index');
	        }else{
	            $this->error("删除失败！");
	        }
	    }


	}

 ?>