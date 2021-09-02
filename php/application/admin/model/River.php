<?php

namespace app\admin\model;



class River extends BaseModel
{

    

    

    // 表名
    protected $name = 'river';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'river_type_text',
        'river_level_text',
        'river_attr_text'
    ];
    

    public function getRiverTypeTextAttr($value,$data){

        return $this->cache_normal[$data['river_type']]['value'];

    }

    public function getRiverLevelTextAttr($value,$data){

        return $this->cache_normal[$data['river_level']]['value'];

    }

    public function getRiverAttrTextAttr($value,$data){

        return $this->cache_normal[$data['river_attr']]['value'];

    }






    public function radmin()
    {
        return $this->belongsTo('Radmin', 'radmin_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
