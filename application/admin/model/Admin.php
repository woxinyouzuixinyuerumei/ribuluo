<?php
/**
* 管理员Model类
* ======================================
*  yangyang.zhang@huwaimeibao.com
*/
namespace app\admin\model;
use think\Model;

class Admin extends Model {
    //字段类型自动转换
    /*protected $type = [
        'entry_time'=>'timestamp:Y-m-d'
    ];*/
	
	/**
	 * 查询一条数据
	 * @param string $where
	 * @param string $field
	 */
	public function getInfo($where = true, $field='*'){
		return $this->field($field)->where($where)->order('id desc')->find();
	}

}