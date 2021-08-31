<?php

namespace app\admin\model;

use think\Model;


class Radmin extends Model
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

    ];
    

    







    public function company()
    {
        return $this->belongsTo('Company', 'company_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
