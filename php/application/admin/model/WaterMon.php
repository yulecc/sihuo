<?php

namespace app\admin\model;

use think\Model;


class WaterMon extends BaseModel
{

    

    

    // 表名
    protected $name = 'water_mon';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'mon_category_text'
    ];

    public function getMonCategoryTextAttr($value,$data){

        return $this->cache_normal[$data['mon_category']]['value'];

    }
    







}
