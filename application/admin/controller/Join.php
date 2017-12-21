<?php
namespace app\admin\controller;
use think\Controller;

class Join extends Common
{
    public function index(){
        if(request()->isPost()){
            $key = input('post.key');
            $addtime = input('post.addtime');
            $page = input('page')?input('page'):1;
            $pageSize = input('limit')?input('limit'):config('paginate.list_rows');
            $where = array();
            if(!empty($addtime)){
                $time_array = explode('~',$addtime);
                $where['addtime'] = array('between',array(strtotime($time_array[0].' 00:00:00'),strtotime($time_array[1].' 23:59:59')));
            }
            if(!empty($key)){
                $where['linkman'] = array('like',"%".$key."%");
            }
            $join = model('Join');
            $list = $join->where($where)
                ->order('id desc')
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
        }
        return view();
    }
    //转为客户
    public function change(){
        $this->success('转为客户成功！','index');exit;
        $id = input('post.id');
        $join = model('Join');
        $company = model('Company');
        $personnel = model('Personnel');
        $auth_rule = model('AuthRule');
        $map = $join ->find($id);
        $data['name'] = $map['company'];
        $data['linkman'] = $map['linkman'];
        $data['phone'] = $map['phone'];
        $data['medianum'] = $map['medianum'];
        $data['addtime'] = time();
        $rs = $join->where('id',$id)->setField('status','1');
        if($rs){
            //插入到广告公司表
            $company_id = $company->insertGetId($data);
            //echo $company_id;exit;
            if($company_id){
                $auth =  $auth_rule->where('type',1)->column('id');
                $auth = ",".implode(',',$auth).",";
                $list['company_id'] = $company_id;
                $list['name'] = $map['linkman'];
                $list['password'] = md5('123456');
                $list['mobile'] = $map['phone'];
                $list['mail'] = $map['mail'] ? $map['mail']:"1969663066@qq.com";
                $list['is_lead'] = 1;
                $list['auth'] = $auth;
                $list['addtime'] = time();
                $res = $personnel->save($list);
                if($res){
                    $this->success('转为客户成功！','index');
                }else{
                    $this->error("转为客户失败！");
                }
            }else{
                $this->error("转为客户失败！");
            }
            
        }
    }
    //删除选中
    public function delChoice(){
        $ids = input('post.ids/a');
        $map = array(
            'id' => array('in',$ids),
        );
        $join = model('Join');
        $rs = $join->where($map)->delete();
        if($rs!==false){
            //写入日志
            //addCommitLog($this->userinfo['company_id'],$this->userinfo['id'],MODULE_NAME.'/'.ACTION_NAME,$ids,input('post.'),'删除');
            $this->success('删除成功！','index');
        }else{
            $this->error("删除失败！");
        }
    }
    //删除
    public function delData(){
        $id = input('post.id');
        $join = model('Join');
        $rs = $join->where(array('id'=>$id))->delete();
        if($rs!==false){
            $this->success('删除成功！');
        }else{
            $this->error("删除失败！");
        }
    }


}
?>
