<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
/**
 * 检测管理员是否登录
 */
function checkAdminLogin(){

	$admin = model('Admin');
	//验证SESSION
	if (session('admin_id')>0){
		$admin_id = session('admin_id');
        $mobile = session('mobile');
		$session_scode = session('scode');	        
		$admin_info = $admin->getInfo("id=".$admin_id);
		
		if (!$admin_info){
			$result['msg'] = '用户不存在';
			$result['status'] = 0;
			return $result;
		}	
		if($admin_info['status']==2){
			$result['msg'] = '此账户已被锁定';
			$result['status'] = 0;
			return $result;
		}        
		//验证登录合法性	        
		$scode = md5($admin_info['id'].$admin_info['password'].'c9b7503e7f7c5350acf4edae6fbef3a0');
		if ($scode!=$session_scode){
			$result['msg'] = '请重新登录';
			$result['status'] = 0;
			return $result;
		}	       
		//合法
		$result['status'] = true;
		$result['msg'] = '验证成功';
		$result['admininfo'] = $admin_info;
		
		return $result;
	}
	//验证COOKIE
	if (cookie('admin_id')>0){
		$admin_id = cookie('admin_id');
        /*$company_id = cookie('company_id');*/
		$mobile = cookie('mobile');	       
		$cookie_scode = cookie('scode');
		$admin_info = $admin->getInfo("id=".$admin_id);
		
		if (!$admin_info){
			$result['msg'] = '账号不存在';
			$result['status'] = 0;
			return $result;
		}	   
		if($admin_info['status']==2){
			$result['msg'] = '此账户已被锁定';
			$result['status'] = 0;
			return $result;
		}      
		//验证登录合法性
		$scode = md5($admin_info['id'].$admin_info['password'].'c9b7503e7f7c5350acf4edae6fbef3a0');
		if ($scode!=$cookie_scode){
			$result['msg'] = '请重新登录';
			$result['status'] = 0;
			return $result;
		}
		
		//合法
		$result['status'] = true;
		$result['msg'] = '验证成功';
		$result['admininfo'] = $admin_info;
		
		//刷新SESSION
		session('admin_id', $admin_id);
		session('mobile', $mobile);
		session('scode', $scode);
		
		return $result;
	}
	
	$result['status'] = 0;
	$result['msg'] = '请重新登录';
	$result['admininfo'] = array();
	return $result;
}

/**
 * 增加广告公司操作日志
 */
function addCommitLog($company_id,$personnel_id,$href,$related_id,$related_data=array(),$related_title=''){
    //如果开启写入日志则执行
    if(config('add_commit_log')){
        $auth_rule_id = db('auth_rule')->where('href',$href)->value('id');
        if(empty($auth_rule_id))$auth_rule_id = 0;
        $auth_rule_title = db('auth_rule')->where('href',$href)->value('title');
        if(empty($auth_rule_title))$auth_rule_title = $related_title;
        if(is_array($related_id)){
            $data = array();
            foreach($related_id as $k=>$v) {
                $data[] = array(
                    'company_id'=>$company_id,
                    'personnel_id'=>$personnel_id,
                    'href'=>$href,
                    'auth_rule_id'=>$auth_rule_id,
                    'auth_rule_title'=>$auth_rule_title,
                    'related_id'=>$v,
                    'related_data'=> json_encode($related_data),
                    'addtime'=>time()
                );
            }
            return model('CommitLog')->insertAll($data);
        }else{
            $data = array(
                'company_id'=>$company_id,
                'personnel_id'=>$personnel_id,
                'href'=>$href,
                'auth_rule_id'=>$auth_rule_id,
                'auth_rule_title'=>$auth_rule_title,
                'related_id'=>$related_id,
                'related_data'=> json_encode($related_data),
                'addtime'=>time()
            );
            return model('CommitLog')->insert($data);
        }
    }else{
        return true;
    }
}


/*
* 获取某个选项下的所有子选项
 */
function getOptionsById($id){
    $option = model('Option');
    $result = $option->where('parent_id',$id)->field('id,title,icon')->select();
    return $result;
}

/*
* 获取选项字段值
 */
function getOptionField($id,$field='title'){
    $option = model('Option');
    $result = $option->where('id',$id)->value($field);
    return $result;
}
function menu($data , $pid=0, $lefthtml = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'){
    $arr = array();
    foreach ($data as $v){
        if($v['parent_id']==$pid){
            $v['lefthtml'] = str_repeat($lefthtml,$v['level']-1);
            $v['title'] = $v['lefthtml'].'<i class="larry-icon">&#xe9c8;</i>&nbsp;&nbsp;'.$v['title'];
            $arr[]=$v;
            $arr = array_merge($arr,menu($data,$v['id'],$lefthtml));
        }
    }
    return $arr;
}

/**
 * 媒体类型
 */
function type_array()
{
    return array(
        array('id'=>1,'name'=>'看板','children'=>array(
            array('id'=>11,'name'=>'楼顶大牌'),
            array('id'=>12,'name'=>'墙体大牌'),
            array('id'=>13,'name'=>'落地大牌'),
            array('id'=>14,'name'=>'跨桥大牌'),
            array('id'=>15,'name'=>'围档广告'),
            array('id'=>16,'name'=>'车身广告'),
            array('id'=>17,'name'=>'框架广告')
        )),
        array('id'=>2,'name'=>'灯箱','children'=>array(
            array('id'=>21,'name'=>'墙体灯箱'),
            array('id'=>22,'name'=>'落地灯箱'),
            array('id'=>23,'name'=>'跨桥灯箱'),
            array('id'=>24,'name'=>'立杆灯牌'),
            array('id'=>25,'name'=>'公交站台灯箱')
        )),
        array('id'=>3,'name'=>'立柱','children'=>array(
            array('id'=>31,'name'=>'立交立柱'),
            array('id'=>32,'name'=>'高速单立柱')
        )),
        array('id'=>4,'name'=>'电子屏','children'=>array(
            array('id'=>41,'name'=>'公交站电子屏'),
            array('id'=>42,'name'=>'地铁站电子屏'),
            array('id'=>43,'name'=>'楼宇电子屏')
        ))
    );
}

/**
 * 出租方式
 */
function lease_mode_array()
{
    return array(
        1 => '以天为单位',
        2 => '以周为单位',
        3 => '以月为单位',
        4 => '以三月为单位',
        5 => '以半年为单位',
        6 => '以年为单位'
    );
}

/**
 * 上刊时间
 */
function publishtime_array()
{
    return array(
        1 => '每天都可以',
        2 => '每周的固定时间',
        3 => '每月的固定时间',
        4 => '每年的固定时间'
    );
}

/**
 * 客户类别
 */
function customer_type_array()
{
    return array(
        1 => '重要客户',
        2 => '普通客户',
        3 => '一般客户'
    );
}

/**
 * 销售阶段
 */
function customer_stage_array()
{
    return array(
        1 => '未洽谈',
        2 => '初访',
        3 => '复访',
        4 => '意向',
        5 => '报价',
        6 => '签约',
        7 => '投放',
        8 => '完成',
        9 => '暂时搁置',
        10 => '放弃'
    );
}

/**
 * 客户来源
 */
function customer_source_array()
{
    return array(
        1 => '转介绍',
        2 => '线上注册',
        3 => '线上询价',
        4 => '预约上门',
        5 => '陌拜',
        6 => '招商资源',
        7 => '公司资源',
        8 => '展会资源',
        9 => '个人资源',
        10 => '电话咨询',
        11 => '邮件咨询'
    );
}

/**
 * 销售阶段跟进方式
 */
function customer_stage_type_array()
{
    return array(
        1 => '电话',
        2 => '邮件',
        3 => '上门拜访',
        4 => '微信',
        5 => '钉钉',
        6 => '短信',
        7 => '客户来访',
        8 => '其他'
    );
}

/**
 * 方案阶段
 */
function plan_stage_array()
{
    return array(
        0 => '新方案',
        1 => '筛选中',
        2 => '已确定',
        3 => '已签约',
        4 => '上刊中',
        5 => '已上刊',
        6 => '已结束'
    );
}