<?php

namespace app\admin\model;



class Radmin extends BaseModel
{

    

    

    // 表名
    protected $name = 'radmin';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'plevel_text',
        'autharea_text'
    ];
    

    
    public function getPlevelTextAttr($value,$data){

        return $this->cache_normal[$data['plevel']]['value'];

    }

    public function getAuthareaTextAttr($value,$data){

        return $this->cache_normal[$data['autharea']]['value'];

    }





    public function company()
    {
        return $this->belongsTo('Company', 'company_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
