<?php
/**
* 角色Model类
* ======================================
*  yangyang.zhang@huwaimeibao.com
*/
namespace app\admin\model;
use think\Model;

class Role extends Model {
    //字段类型自动转换
    protected $type = [
        'addtime'=>'timestamp:Y-m-d'
    ];
	
	/**
	 * 查询一条数据
	 * @param string $where
	 * @param string $field
	 */
	public function getFieldById($id, $field='*'){
		return $this->where('id',$id)->value($field);
	}

}