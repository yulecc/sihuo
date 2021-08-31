<?php

namespace app\admin\validate;

use think\Validate;

class Admin extends Validate
{

    /**
     * 验证规则
     */
    protected $rule = [
        'username' => 'require|regex:\w{3,12}|unique:admin',
//        'nickname' => 'require',
        'password' => 'require|regex:\S{32}',
        'mobile'    => 'require|regex:/^1[3456789]\d{9}$/|unique:admin,mobile',
        'company'    => 'require',
    ];

    /**
     * 提示消息
     */
    protected $message = [
    ];

    /**
     * 字段描述
     */
    protected $field = [
    ];

    /**
     * 验证场景
     */
    protected $scene = [
        'add'  => ['username', 'mobile', 'password','company'],
        'edit' => ['username', 'mobile', 'password','company'],
    ];

    public function __construct(array $rules = [], $message = [], $field = [])
    {
        $this->field = [
            'username' => __('Username'),
            'password' => __('Password'),
            'mobile'    => __('Mobile'),
            'company'    => __('Company'),
        ];
        $this->message = array_merge($this->message, [
            'username.regex' => __('Please input correct username'),
            'password.regex' => __('Please input correct password')
        ]);
        parent::__construct($rules, $message, $field);
    }

}
