<?php

namespace app\admin\model;

use think\Model;


class River extends Model
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

    ];
    

    







    public function radmin()
    {
        return $this->belongsTo('Radmin', 'radmin_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
