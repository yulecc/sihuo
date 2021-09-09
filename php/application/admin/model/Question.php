<?php

namespace app\admin\model;

use think\Model;


class Question extends Model
{

    

    

    // 表名
    protected $name = 'question';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'question_level_text'
    ];
    

    
    public function getQuestionLevelList()
    {
        return ['1' => __('Question_level 1'), '2' => __('Question_level 2')];
    }


    public function getQuestionLevelTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['question_level']) ? $data['question_level'] : '');
        $list = $this->getQuestionLevelList();
        return isset($list[$value]) ? $list[$value] : '';
    }




    public function admin()
    {
        return $this->belongsTo('Admin', 'admin_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function river()
    {
        return $this->belongsTo('River', 'river_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
